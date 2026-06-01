<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProxyRequest;
use App\Http\Requests\UpdateProxyRequest;
use App\Models\Proxy;
use App\Services\ProxyCheckService;
use Illuminate\Http\JsonResponse;

class ProxyController extends Controller
{
    public function __construct(
        private readonly ProxyCheckService $proxyCheckService,
    ) {}

    public function index(): JsonResponse
    {
        $proxies = Proxy::latest()->get();

        return response()->json(['data' => $proxies]);
    }

    public function store(StoreProxyRequest $request): JsonResponse
    {
        $proxy = Proxy::create($request->validated());

        return response()->json(['data' => $proxy], 201);
    }

    public function show(Proxy $proxy): JsonResponse
    {
        return response()->json(['data' => $proxy]);
    }

    public function update(UpdateProxyRequest $request, Proxy $proxy): JsonResponse
    {
        $proxy->update($request->validated());

        return response()->json(['data' => $proxy]);
    }

    public function destroy(Proxy $proxy): JsonResponse
    {
        $proxy->delete();

        return response()->json(null, 204);
    }

    public function check(Proxy $proxy): JsonResponse
    {
        $result = $this->proxyCheckService->check($proxy);

        return response()->json(['data' => $result]);
    }

    public function checkAll(): JsonResponse
    {
        $proxies = Proxy::all();
        $results = [];

        foreach ($proxies as $proxy) {
            $results[] = $this->proxyCheckService->check($proxy);
        }

        return response()->json(['data' => $results]);
    }
}
