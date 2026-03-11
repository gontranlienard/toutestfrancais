protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\SetLocale::class,
		
    ],
	
];
protected $middlewareAliases = [
    'admin.auth' => \App\Http\Middleware\AdminAuth::class,
];

