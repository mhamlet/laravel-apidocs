<?php

namespace MHamlet\Apidocs\Generators;

use MHamlet\Apidocs\Parsers\ControllerParser;

/**
 * Class RouteDocsGenerator
 *
 * @package MHamlet\Apidocs\Generators
 */
class RouteDocsGenerator {

    /**
     * @var array
     */
    private $routes;

    /**
     * @var self[]
     */
    private static $instances = [];

    /**
     * Create a new Generator Instance
     *
     * @param array $routes
     */
    private function __construct(array $routes) {

        $this->routes = $routes;
    }

    /**
     * @param string $prefix
     * @param array  $routes
     *
     * @return self
     */
    public static function getInstance($prefix, array $routes) {

        $prefix = ltrim(trim($prefix), '/');

        if (!array_key_exists($prefix, self::$instances)) {
            self::$instances[$prefix] = new self($routes);
        }

        return self::$instances[$prefix];
    }

    /**
     * Return route describers - path, verbs, controller and method
     *
     * @return array
     */
    public function describeRoutes() {

        return $this->routes;
    }

    /**
     * Returns same data, as "describeRoutes" method, with additional data
     *
     * @return array
     */
    public function generate() {

        // Getting routes
        $routes = $this->describeRoutes();

        foreach ($routes as &$route) {

            if (is_null($route['controller']) || is_null($route['method'])) {
                continue;
            }

            $parser = new ControllerParser($route['controller']);
            $parsedMethod = $parser->parseMethod($route['method']);

            unset($route['controller']);
            unset($route['method']);

            $route['params'] = $parsedMethod->params;
            $route['description'] = $parsedMethod->description;
            $route['responses'] = $parsedMethod->returns;
        }

        return $routes;
    }
}
