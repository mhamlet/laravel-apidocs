<?php

namespace MHamlet\Apidocs;

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class RouteParser {

    /**
     * @var Router
     */
    private static $router;

    /**
     * @var RouteCollection
     */
    private static $routes;

    /**
     * @param Router $router
     */
    public static function setRouter(Router $router) {

        self::$router = $router;
    }

    /**
     * @return Router
     */
    public static function getRouter() {

        return self::$router;
    }

    /**
     * @return RouteCollection
     */
    public static function getRoutes() {

        if (is_null(self::$routes)) {

            self::$routes = self::$router->getRoutes();
        }

        return self::$routes;
    }

    /**
     * @param string $prefix
     *
     * @return RouteCollection
     */
    public static function getRoutesByPrefix($prefix) {

        $prefix = ltrim(trim($prefix), '/');

        $routes = self::getRoutes();
        $prefixLength = strlen($prefix);

        $filteredRoutes = new RouteCollection();

        foreach ($routes as $route) {

            $uri = ltrim(trim($route->getUri()), '/');

            if (substr($uri, 0, $prefixLength) == $prefix) {
                $filteredRoutes->add($route);
            }
        }

        return $filteredRoutes;
    }

    /**
     * @param RouteCollection $routeCollection
     *
     * @return array
     */
    public static function describeRoutes(RouteCollection $routeCollection) {

        $routes = [];

        foreach ($routeCollection as $route) {

            // Path
            $path = $route->getUri();

            // Method and controller
            $action = $route->getAction();

            if ($action['uses'] instanceof \Closure) {
                $method = null;
                $controller = null;
            }
            else {
                $controller = explode('@', $action['controller']);
                $method = $controller[1];
                $controller = $controller[0];
            }

            // Verbs
            $verbs = $route->getMethods();

            $routes[] = [
                'path' => $path,
                'controller' => $controller,
                'method' => $method,
                'verbs' => $verbs,
            ];
        }

        return $routes;
    }
}
