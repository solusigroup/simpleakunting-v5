protected $middlewareGroups = [
'web' => [
// ... middleware lainnya
\App\Http\Middleware\TenantBeaconMiddleware::class,
],
];