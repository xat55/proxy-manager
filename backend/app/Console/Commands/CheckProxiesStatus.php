<?php

namespace App\Console\Commands;

use App\Models\Proxy;
use App\Services\ProxyCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckProxiesStatus extends Command
{
    protected $signature = 'proxies:check-status';
    protected $description = 'Check status of all proxies';

    public function handle(ProxyCheckService $checkService): int
    {
        $proxies = Proxy::all();

        if ($proxies->isEmpty()) {
            $this->info('No proxies to check.');

            return self::SUCCESS;
        }

        $this->info("Checking {$proxies->count()} proxies...");
        $bar = $this->output->createProgressBar($proxies->count());
        $bar->start();

        $results = ['active' => 0, 'error' => 0];

        foreach ($proxies as $proxy) {
            try {
                $checkService->check($proxy);
                $results[$proxy->fresh()->status === 'active' ? 'active' : 'error']++;
            } catch (\Throwable $e) {
                Log::error("Proxy check command error for {$proxy->ip}:{$proxy->port}", [
                    'error' => $e->getMessage(),
                ]);
                $results['error']++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Active: {$results['active']}, Error: {$results['error']}");

        return self::SUCCESS;
    }
}
