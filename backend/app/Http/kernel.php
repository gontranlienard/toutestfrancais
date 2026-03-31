protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\SetLocale::class,
		
    ],
	
];
protected $middlewareAliases = [
    'admin.auth' => \App\Http\Middleware\AdminAuth::class,
];
protected $middleware = [
    // ...
    \App\Http\Middleware\LogVisitor::class,
];

