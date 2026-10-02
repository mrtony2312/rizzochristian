<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\MerchantListing;
use App\Support\MerchantProductIdentifiers;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('merchant:normalize-catalog {--csv=storage/app/merchant-catalog.csv} {--pending=storage/app/merchant-ean-pending.csv}')]
#[Description('Normalize brands, clear unconfirmed identifiers, remove permanent strikethrough prices, export Merchant CSV')]
class MerchantNormalizeCatalogCommand extends Command
{
    public function handle(): int
    {
        $updated = 0;
        $rows = [];
        $pending = [];

        foreach (Product::query()->with('category')->orderBy('id')->get() as $product) {
            $brand = MerchantProductIdentifiers::resolveBrand(
                $product->brand,
                $product->description,
                $product->name
            );

            $gtin = MerchantProductIdentifiers::resolveGtin($product->gtin, $product->slug);
            $mpn = MerchantProductIdentifiers::resolveMpn($product->mpn, $product->slug);

            // Permanent ~18% markdowns are not dated Merchant sales.
            $payable = $product->price;
            $regular = $payable;
            $saleStart = null;
            $saleEnd = null;

            $product->fill([
                'brand' => $brand,
                'gtin' => $gtin,
                'mpn' => $mpn,
                'regular_price' => $regular,
                'sale_price_starts_at' => $saleStart,
                'sale_price_ends_at' => $saleEnd,
            ]);
            $product->save();
            $updated++;

            $listing = new MerchantListing($product->fresh(['category', 'images']));

            $rows[] = [
                'id' => $listing->id(),
                'titre' => $listing->title(),
                'brand' => $listing->brand() ?? '',
                'gtin' => $listing->gtin() ?? '',
                'mpn' => $listing->mpn() ?? '',
                'identifier_exists' => $listing->hasIdentifierExistsNo() ? 'no' : '',
                'price' => $listing->price().' '.$listing->currency(),
                'sale_price' => $listing->regularPrice() ? $listing->price().' '.$listing->currency() : '',
                'sale_price_effective_date' => $listing->salePriceEffectiveDate() ?? '',
            ];

            $pending[] = [
                'id' => $listing->id(),
                'slug' => $product->slug,
                'titre' => $listing->title(),
                'brand' => $listing->brand() ?? '',
                'ean_a_confirmer_chez_fournisseur' => $this->pendingEanNote($product, $listing),
                'notes' => $this->pendingNotes($product, $listing),
            ];
        }

        $this->syncProductsJson();

        $csvPath = base_path($this->option('csv'));
        $pendingPath = base_path($this->option('pending'));
        $this->writeCsv($csvPath, $rows);
        $this->writeCsv($pendingPath, $pending);

        $this->info("Normalized {$updated} products.");
        $this->info('CSV: '.$csvPath);
        $this->info('Pending EAN list: '.$pendingPath);

        return self::SUCCESS;
    }

    private function pendingEanNote(Product $product, MerchantListing $listing): string
    {
        if ($listing->gtin() !== null) {
            return '';
        }

        return match (true) {
            str_contains(mb_strtolower($product->slug), 'mcz-stream') => 'Confirmer EAN carton MCZ Stream Comfort Air 12 UP! (article 7122059). Ne pas utiliser 8018459172355 (variante R / sortie arrière).',
            str_contains(mb_strtolower($product->slug), 'mcz-cute') => 'Confirmer EAN carton MCZ Cute Air 8 UP! (article 7122002). MPN 7122002 uniquement après confirmation SKU vendu.',
            str_contains(mb_strtolower($product->slug), 'mcz-doc'),
            str_contains(mb_strtolower($product->slug), 'mcz-ego') => 'Confirmer EAN / MPN fabricant MCZ du modèle exact vendu (finition / sortie fumi).',
            str_contains(mb_strtolower($product->slug), 'ecopower') => 'Confirmer EAN palette 65×15 kg / 975 kg. Ne pas utiliser 5430000813013 (sac 15 kg).',
            $listing->brand() !== null => 'Confirmer EAN/MPN fabricant du conditionnement exact vendu (facture / carton).',
            default => 'Sans marque fabricant connue : laisser gtin/mpn vides; identifier_exists=no tant qu’aucune source primaire.',
        };
    }

    private function pendingNotes(Product $product, MerchantListing $listing): string
    {
        $notes = [];

        if ($listing->brand() === null) {
            $notes[] = 'brand vide (combustible générique ou fabricant non identifiable dans le titre)';
        }

        if ($listing->hasIdentifierExistsNo()) {
            $notes[] = 'identifier_exists=no';
        }

        $notes[] = 'prix unique payable (ancien barré ~18 % retiré — promo non datée)';

        return implode(' | ', $notes);
    }

    private function syncProductsJson(): void
    {
        $path = database_path('data/products.json');
        if (! File::exists($path)) {
            return;
        }

        $products = json_decode(File::get($path), true);
        if (! is_array($products)) {
            return;
        }

        $byId = Product::query()->get()->keyBy('id');

        foreach ($products as &$row) {
            $id = $row['id'] ?? null;
            if ($id === null || ! isset($byId[$id])) {
                continue;
            }

            $product = $byId[$id];
            $row['brand'] = $product->brand;
            $row['gtin'] = $product->gtin;
            $row['mpn'] = $product->mpn;
            $row['energy_efficiency_class'] = $product->energy_efficiency_class;
            $row['price'] = (float) $product->price;
            $row['regular_price'] = (float) $product->regular_price;
        }
        unset($row);

        File::put(
            $path,
            json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
        );
    }

    /**
     * @param  list<array<string, string>>  $rows
     */
    private function writeCsv(string $path, array $rows): void
    {
        File::ensureDirectoryExists(dirname($path));
        $handle = fopen($path, 'w');
        if ($handle === false) {
            throw new \RuntimeException('Unable to write '.$path);
        }

        fwrite($handle, "\xEF\xBB\xBF");

        if ($rows === []) {
            fclose($handle);

            return;
        }

        fputcsv($handle, array_keys($rows[0]));
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}
