<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'price', 'regular_price',
        'short_description', 'description', 'attributes', 'image', 'in_stock',
    ];

    protected $casts = [
        'attributes' => 'array',
        'in_stock' => 'boolean',
        'price' => 'decimal:2',
        'regular_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function isOnSale(): bool
    {
        return $this->regular_price && $this->price && $this->price < $this->regular_price;
    }

    public function favoritedBy(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Resolve a usable public URL for the product image.
     * Falls back to sized WordPress derivatives when the original file is missing/empty.
     */
    public function imageUrl(?string $fallback = null): string
    {
        $fallback = $fallback ?? asset('images/logo-rizzo.png');
        $candidates = [];

        if ($this->image) {
            $candidates[] = $this->image;
            $pathInfo = pathinfo($this->image);
            $dir = ($pathInfo['dirname'] ?? '.') === '.' ? '' : $pathInfo['dirname'].'/';
            $filename = $pathInfo['filename'] ?? '';
            $ext = $pathInfo['extension'] ?? '';

            if ($filename !== '') {
                foreach (['-768x768', '-600x600', '-300x300', '-150x150'] as $suffix) {
                    if ($ext !== '') {
                        $candidates[] = $dir.$filename.$suffix.'.'.$ext;
                    }
                    $candidates[] = $dir.$filename.$suffix.'.webp';
                }
                $candidates[] = $dir.$filename.'.webp';
            }
        }

        foreach ($this->relationLoaded('images') ? $this->images : [] as $image) {
            if (! empty($image->path)) {
                $candidates[] = $image->path;
            }
        }

        foreach (array_unique($candidates) as $relative) {
            $relative = ltrim(str_replace('\\', '/', $relative), '/');
            $absolute = public_path($relative);
            if (is_file($absolute) && filesize($absolute) > 0) {
                return asset($relative);
            }
        }

        return $fallback;
    }

    /**
     * The stored description is plain text using a small fixed set of
     * section headings. Split it into [heading => [lines]] so views can
     * render each section with appropriate formatting.
     */
    public function descriptionSections(): array
    {
        $headings = [
            'Riepilogo tecnico',
            'Descrizione dettagliata',
            'Caratteristiche tecniche',
            'Consegna',
            'Stoccaggio e utilizzo',
            'Conservazione e utilizzo',
            'Diritto di recesso',
        ];

        $lines = preg_split('/\r\n|\r|\n/', trim((string) $this->description));
        $sections = [];
        $current = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (in_array($line, $headings, true)) {
                $current = $line;
                $sections[$current] = [];

                continue;
            }

            if ($current === null) {
                $current = 'Descrizione';
                $sections[$current] = [];
            }

            $sections[$current][] = $line;
        }

        return $sections;
    }
}
