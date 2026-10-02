<?php

namespace App\Console\Commands;

use App\Support\MerchantValidator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('merchant:validate')]
#[Description('Validate the product catalog against Google Merchant Center readiness checks')]
class MerchantValidateCommand extends Command
{
    public function handle(MerchantValidator $validator): int
    {
        $report = $validator->validateCatalog();

        $this->newLine();
        $this->line('=====================================');
        $this->line('GOOGLE MERCHANT VALIDATION');
        $this->line('TOTAL : '.$report['total']);
        $this->line('VALIDES : '.$report['valid']);
        $this->line('BLOQUÉS : '.$report['blocked']);
        $this->line('AVERTISSEMENTS : '.$report['warnings']);
        $this->line('=====================================');

        $bySeverity = collect($report['issues'])->groupBy('severity');

        foreach (['CRITICAL', 'IMPORTANT', 'WARNING'] as $severity) {
            $items = $bySeverity->get($severity, collect());
            if ($items->isEmpty()) {
                continue;
            }

            $this->newLine();
            $this->error($severity.' ERRORS ('.$items->count().')');

            foreach ($items as $issue) {
                $this->line(str_repeat('-', 40));
                $this->line('Product ID: '.($issue['product_id'] ?? 'n/a'));
                $this->line('SKU: '.($issue['sku'] ?: 'n/a'));
                $this->line('Product name: '.$issue['name']);
                $this->line('Field: '.$issue['field']);
                $this->line('Current value: '.$issue['value']);
                $this->line('Expected/required state: '.$issue['expected']);
                $this->line('Severity: '.$issue['severity']);
                $this->line('Reason: '.$issue['reason']);
                $this->line('Recommended correction: '.$issue['recommendation']);
            }
        }

        if ($report['blocked'] > 0) {
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('No CRITICAL blockers. Human review still required in Merchant Center.');

        return self::SUCCESS;
    }
}
