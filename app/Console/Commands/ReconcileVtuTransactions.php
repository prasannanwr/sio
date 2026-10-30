<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\VtuTransaction;
use App\Services\VtuPaymentService;
use App\Services\VtuProviderService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class ReconcileVtuTransactions extends Command
{
    protected $signature = 'vtu:reconcile
        {--minutes=5 : Only check transactions that have not changed for this many minutes}
        {--give-up-after=60 : Mark a purchase failed when the provider still has no record of it after this many minutes}';

    protected $description = 'Settle VTU purchases stuck in processing and start paid purchases that never began';

    public function handle(VtuProviderService $provider, VtuPaymentService $payments): int
    {
        $cutoff = now()->subMinutes((int) $this->option('minutes'));

        $this->startPaidPurchases($payments, $cutoff);
        $this->settleProcessingPurchases($provider, $payments, $cutoff);
        $this->reportPaidFailures();

        return self::SUCCESS;
    }

    /**
     * Payments recorded as paid whose purchase never started.
     */
    protected function startPaidPurchases(VtuPaymentService $payments, Carbon $cutoff): void
    {
        $transactions = VtuTransaction::query()
            ->where('status', VtuTransaction::STATUS_PENDING)
            ->where('updated_at', '<=', $cutoff)
            ->whereHas('payment', fn ($query) => $query->whereIn('status', [Transaction::STATUS_PAID, Transaction::STATUS_OVERPAID]))
            ->limit(100)
            ->get();

        foreach ($transactions as $transaction) {
            $result = $payments->fulfillIfPaid($transaction);

            $this->line("{$transaction->reference}: PENDING -> {$result->status}");
        }
    }

    /**
     * Ask the provider for the final result of purchases still processing.
     */
    protected function settleProcessingPurchases(VtuProviderService $provider, VtuPaymentService $payments, Carbon $cutoff): void
    {
        $giveUpBefore = now()->subMinutes((int) $this->option('give-up-after'));

        $transactions = VtuTransaction::query()
            ->where('status', VtuTransaction::STATUS_PROCESSING)
            ->where('updated_at', '<=', $cutoff)
            ->limit(100)
            ->get();

        foreach ($transactions as $transaction) {
            try {
                $response = $provider->status($transaction->reference);
            } catch (Throwable $e) {
                $this->warn("{$transaction->reference}: status check failed: {$e->getMessage()}");

                continue;
            }

            if (($response['status'] ?? null) === 'not_found') {
                if ($transaction->updated_at <= $giveUpBefore) {
                    $transaction->update([
                        'status' => VtuTransaction::STATUS_FAILED,
                        'provider_response' => [
                            'error' => 'The provider has no record of this purchase.',
                            'last_response' => $response,
                        ],
                    ]);

                    $this->warn("{$transaction->reference}: PROCESSING -> FAILED (no record at the provider)");
                }

                continue;
            }

            $payments->syncTransactionFromProvider($transaction, $response);

            $this->line("{$transaction->reference}: PROCESSING -> {$transaction->fresh()->status}");
        }
    }

    /**
     * Customers who paid for a purchase that failed need a refund or a manual top-up.
     */
    protected function reportPaidFailures(): void
    {
        $references = VtuTransaction::query()
            ->where('status', VtuTransaction::STATUS_FAILED)
            ->whereHas('payment', fn ($query) => $query->whereIn('status', [Transaction::STATUS_PAID, Transaction::STATUS_OVERPAID]))
            ->pluck('reference');

        if ($references->isNotEmpty()) {
            $this->warn("{$references->count()} paid VTU purchase(s) failed and need a refund: " . $references->implode(', '));
        }
    }
}
