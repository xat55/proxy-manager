<?php

namespace App\Services;

use App\Models\Proxy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProxyCheckService
{
    /**
     * Check if a proxy is working by attempting to connect through it.
     */
    public function check(Proxy $proxy): Proxy
    {
        $proxy->update(['status' => 'checking']);

        try {
            $proxyUri = $proxy->type . '://';
            if ($proxy->username && $proxy->password) {
                $proxyUri .= $proxy->username . ':' . $proxy->password . '@';
            }
            $proxyUri .= $proxy->ip . ':' . $proxy->port;

            $response = Http::withOptions([
                'proxy' => $proxyUri,
                'timeout' => 10,
                'connect_timeout' => 5,
                'verify' => false,
            ])->get('http://httpbin.org/ip');

            $proxy->update([
                'status' => $response->successful() ? 'active' : 'error',
                'last_checked_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Proxy check failed for {$proxy->ip}:{$proxy->port}", [
                'error' => $e->getMessage(),
            ]);

            $proxy->update([
                'status' => 'error',
                'last_checked_at' => now(),
            ]);
        }

        return $proxy->fresh();
    }
}
