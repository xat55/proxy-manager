<?php

namespace App\Http\Controllers\Docs\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\View\View;
use OpenApi\Attributes as OA;
use OpenApi\Generator;

#[OA\Info(
    version: '1.0.0',
    description: 'API for managing and monitoring proxy servers',
    title: 'Proxy Manager API',
    contact: new OA\Contact(email: 'admin@example.com'),
)]
#[OA\Server(
    url: 'http://localhost:8081',
    description: 'Local development server',
)]
#[OA\Tag(
    name: 'Proxies',
    description: 'Operations with proxy servers',
)]
class ProxyController extends Controller
{
    /**
     * Display the Swagger UI page.
     */
    public function index(Request $request): View
    {
        $documentation = $request->offsetGet('documentation') ?? 'default';
        $config = config('l5-swagger.documentations.' . $documentation, []);
        $useAbsolutePath = config('l5-swagger.defaults.paths.use_absolute_path', true);

        $urlsToDocs = [
            $config['api']['title'] ?? $documentation => route('swagger.json'),
        ];

        return view('l5-swagger::index', [
            'documentation' => $documentation,
            'documentationTitle' => $config['api']['title'] ?? $documentation,
            'secure' => RequestFacade::secure(),
            'urlToDocs' => route('swagger.json'),
            'urlsToDocs' => $urlsToDocs,
            'operationsSorter' => $config['operations_sort'] ?? null,
            'configUrl' => $config['additional_config_url'] ?? null,
            'validatorUrl' => $config['validator_url'] ?? null,
            'useAbsolutePath' => $useAbsolutePath,
        ]);
    }

    /**
     * Generate and return the OpenAPI JSON specification.
     */
    public function json(): JsonResponse
    {
        $openapi = (new Generator())->generate([
            app_path('Http/Controllers/Docs'),
        ]);

        if ($openapi === null) {
            return response()->json(['error' => 'Failed to generate OpenAPI spec'], 500);
        }

        return response()->json($openapi);
    }

    // ──────────────────────────────────────────────
    //  OpenAPI endpoint annotations
    // ──────────────────────────────────────────────

    #[OA\Get(
        path: '/api/proxies',
        summary: 'List all proxies',
        tags: ['Proxies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of proxies',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(type: 'object'),
                        ),
                    ],
                ),
            ),
        ],
    )]
    public function listProxies(): void {}

    #[OA\Post(
        path: '/api/proxies',
        summary: 'Create a new proxy',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ip', 'port', 'type'],
                properties: [
                    new OA\Property(property: 'ip', type: 'string', example: '192.168.1.1'),
                    new OA\Property(property: 'port', type: 'integer', example: 3128),
                    new OA\Property(property: 'type', type: 'string', enum: ['http', 'https', 'socks4', 'socks5'], example: 'http'),
                    new OA\Property(property: 'username', type: 'string', nullable: true),
                    new OA\Property(property: 'password', type: 'string', nullable: true),
                ],
            ),
        ),
        tags: ['Proxies'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Proxy created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                    ],
                ),
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ],
    )]
    public function createProxy(): void {}

    #[OA\Get(
        path: '/api/proxies/{proxy}',
        summary: 'Get a single proxy',
        tags: ['Proxies'],
        parameters: [
            new OA\Parameter(name: 'proxy', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Proxy ID'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Proxy details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'Proxy not found'),
        ],
    )]
    public function showProxy(): void {}

    #[OA\Put(
        path: '/api/proxies/{proxy}',
        summary: 'Update a proxy',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'ip', type: 'string', example: '192.168.1.1'),
                    new OA\Property(property: 'port', type: 'integer', example: 3128),
                    new OA\Property(property: 'type', type: 'string', enum: ['http', 'https', 'socks4', 'socks5']),
                    new OA\Property(property: 'username', type: 'string', nullable: true),
                    new OA\Property(property: 'password', type: 'string', nullable: true),
                ],
            ),
        ),
        tags: ['Proxies'],
        parameters: [
            new OA\Parameter(name: 'proxy', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Proxy ID'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Proxy updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'Proxy not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ],
    )]
    public function updateProxy(): void {}

    #[OA\Delete(
        path: '/api/proxies/{proxy}',
        summary: 'Delete a proxy',
        tags: ['Proxies'],
        parameters: [
            new OA\Parameter(name: 'proxy', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Proxy ID'),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Proxy deleted'),
            new OA\Response(response: 404, description: 'Proxy not found'),
        ],
    )]
    public function deleteProxy(): void {}

    #[OA\Post(
        path: '/api/proxies/{proxy}/check',
        summary: 'Check proxy status',
        tags: ['Proxies'],
        parameters: [
            new OA\Parameter(name: 'proxy', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Proxy ID'),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Check result',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'object'),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'Proxy not found'),
        ],
    )]
    public function checkProxy(): void {}

    #[OA\Post(
        path: '/api/proxies/check-all',
        summary: 'Check status of all proxies',
        tags: ['Proxies'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All proxies checked',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(type: 'object'),
                        ),
                    ],
                ),
            ),
        ],
    )]
    public function checkAllProxies(): void {}
}
