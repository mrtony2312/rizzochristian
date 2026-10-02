<?php

namespace App\Console\Commands;

use App\Support\MerchantCatalog;
use App\Support\MerchantValidator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('merchant:feed-test')]
#[Description('Generate and parse the Google Merchant feed, failing on CRITICAL issues')]
class MerchantFeedTestCommand extends Command
{
    public function handle(MerchantCatalog $catalog, MerchantValidator $validator): int
    {
        $validation = $validator->validateCatalog();
        $critical = collect($validation['issues'])->where('severity', 'CRITICAL');

        if ($critical->isNotEmpty()) {
            $this->error('CRITICAL validation issues found: '.$critical->count());
            foreach ($critical as $issue) {
                $this->line('#'.$issue['product_id'].' '.$issue['field'].': '.$issue['reason']);
            }

            return self::FAILURE;
        }

        $items = $catalog->feedListings();
        $shipping = $catalog->shipping();

        $xmlString = view('feeds.google-merchant', [
            'items' => $items,
            'shipping' => $shipping,
            'updated' => now()->toAtomString(),
            'feedTitle' => config('merchant.feed.title'),
            'feedDescription' => $catalog->purchaseTermsHtml(),
        ])->render();

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);

        if ($xml === false) {
            foreach (libxml_get_errors() as $error) {
                $this->error(trim($error->message));
            }
            libxml_clear_errors();

            return self::FAILURE;
        }

        $xml->registerXPathNamespace('g', 'http://base.google.com/ns/1.0');
        $feedItems = $xml->xpath('//item') ?: [];
        $ids = [];
        $duplicates = 0;

        foreach ($feedItems as $item) {
            $g = $item->children('g', true);
            $id = (string) $g->id;
            $title = (string) $g->title;
            $price = (string) $g->price;
            $availability = (string) $g->availability;
            $link = (string) $g->link;
            $image = (string) $g->image_link;

            if ($id === '' || $title === '' || $price === '' || $link === '' || $image === '') {
                $this->error("Incomplete item in feed (id={$id}).");

                return self::FAILURE;
            }

            if (! in_array($availability, ['in_stock', 'out_of_stock', 'preorder', 'backorder'], true)) {
                $this->error("Invalid availability '{$availability}' for {$id}.");

                return self::FAILURE;
            }

            if (isset($ids[$id])) {
                $duplicates++;
            }
            $ids[$id] = true;
        }

        if ($duplicates > 0) {
            $this->error("Duplicate g:id values: {$duplicates}");

            return self::FAILURE;
        }

        $this->info('Feed XML well-formed.');
        $this->info('Items in feed: '.count($feedItems));
        $this->info('Validation warnings: '.$validation['warnings']);
        $this->info('merchant:feed-test passed (no CRITICAL issues).');

        return self::SUCCESS;
    }
}
