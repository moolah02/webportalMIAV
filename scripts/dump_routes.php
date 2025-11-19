<?php
// Quick script to list routes and catch problematic controller class reflections
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var \Illuminate\Routing\RouteCollection $routes */
$routes = $app['router']->getRoutes();

foreach ($routes as $route) {
    try {
        $controllerClass = null;
        if (method_exists($route, 'getControllerClass')) {
            $controllerClass = $route->getControllerClass();
        } elseif (isset($route->getAction()['controller'])) {
            $controllerClass = $route->getAction()['controller'];
        }

        echo str_pad($route->uri(), 60) . ' | ' . ($controllerClass ?? 'N/A') . PHP_EOL;
    } catch (Throwable $e) {
        echo "ERROR for route: " . $route->uri() . PHP_EOL;
        echo "Message: " . $e->getMessage() . PHP_EOL;
        echo "Action: \n";
        var_export($route->getAction());
        echo PHP_EOL . str_repeat('-', 80) . PHP_EOL;
    }
}
