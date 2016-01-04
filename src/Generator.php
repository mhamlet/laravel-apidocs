<?php

namespace MHamlet\Apidocs;

class Generator {

    /**
     * @var array
     */
    private $routes;

    /**
     * @var self[]
     */
    private static $generatorInstances = [];

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
    private static function getInstance($prefix, array $routes) {

        $prefix = ltrim(trim($prefix), '/');

        if (!array_key_exists($prefix, self::$generatorInstances)) {
            self::$generatorInstances[$prefix] = new self($routes);
        }

        return self::$generatorInstances[$prefix];
    }

    /**
     * @return Generator
     */
    public static function forAllRoutes() {

        $key = "__APIDOCS_ALL";
        $routes = RouteParser::describeRoutes(RouteParser::getRoutes());

        return self::getInstance($key, $routes);
    }

    /**
     * @param string $prefix
     *
     * @return Generator
     */
    public static function forRoutesWithPrefix($prefix) {

        $routes = RouteParser::describeRoutes(RouteParser::getRoutesByPrefix($prefix));

        return self::getInstance($prefix, $routes);
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
