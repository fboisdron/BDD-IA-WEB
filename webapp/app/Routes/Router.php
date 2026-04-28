<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\HomeController;
use App\Controllers\TreeController;
use App\Controllers\MapController;
use App\Controllers\PredictionController;

class Router
{
    private array $routes = [];
    private array $controllers = [];

    public function __construct(
        HomeController $homeController,
        TreeController $treeController,
        MapController $mapController,
        PredictionController $predictionController
    ) {
        $this->controllers = [
            'home' => $homeController,
            'trees' => $treeController,
            'maps' => $mapController,
            'predictions' => $predictionController,
        ];

        $this->registerRoutes();
    }

    /**
     * Register all application routes
     */
    private function registerRoutes(): void
    {
        // Page routes
        $this->routes['GET']['/'] = ['home', 'index'];
        $this->routes['GET']['/trees'] = ['trees', 'index'];
        $this->routes['GET']['/trees/create'] = ['trees', 'create'];
        $this->routes['GET']['/maps'] = ['maps', 'index'];
        $this->routes['GET']['/predictions'] = ['predictions', 'index'];

        // API routes
        $this->routes['GET']['/api/trees/list'] = ['trees', 'list'];
        $this->routes['GET']['/api/trees/stats'] = ['trees', 'stats'];
        $this->routes['POST']['/api/trees/store'] = ['trees', 'store'];

        $this->routes['GET']['/api/maps/points'] = ['maps', 'getPoints'];
        $this->routes['GET']['/api/maps/metadata'] = ['maps', 'getMetadata'];

        $this->routes['POST']['/api/predictions/age'] = ['predictions', 'predictAge'];
        $this->routes['POST']['/api/predictions/cluster'] = ['predictions', 'predictCluster'];
        $this->routes['POST']['/api/predictions/alert'] = ['predictions', 'predictAlert'];
    }

    /**
     * Route the current request
     */
    public function dispatch(): array
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/webapp/public', '', $path);
        $path = preg_replace('#(^|/)index\.php$#', '/', $path);

        if ($path === '' || $path === '/') {
            $path = '/';
        }

        // Find matching route
        if (!isset($this->routes[$method][$path])) {
            return [
                'status' => 404,
                'error' => "Route not found: $method $path",
            ];
        }

        [$controllerName, $methodName] = $this->routes[$method][$path];

        try {
            $controller = $this->controllers[$controllerName];
            
            if (!method_exists($controller, $methodName)) {
                return [
                    'status' => 500,
                    'error' => "Method $methodName not found in controller $controllerName",
                ];
            }

            // Call controller method
            $result = $controller->$methodName($_GET + $_POST);

            return [
                'status' => 200,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get all registered routes (useful for debugging)
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
