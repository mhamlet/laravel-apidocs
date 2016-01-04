<?php

namespace MHamlet\Apidocs\Generators;

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
     * @return Generator
     */
    public static function getInstance($prefix, array $routes) {

        $prefix = ltrim(trim($prefix), '/');

        if (!array_key_exists($prefix, self::$instances)) {
            self::$instances[$prefix] = new self($routes);
        }

        return self::$instances[$prefix];
    }

    /**
     * @return array
     */
    public function describeRoutes() {

        return $this->routes;
    }

    public function generate() {

        // Getting routes
        $routes = $this->describeRoutes();

        foreach ($routes as $route) {

            $parser = new Parser($route['controller']);
            dd($parser->parseMethod('index'));
        }
    }
}
