<?php

namespace MHamlet\Apidocs;

use MHamlet\Apidocs\Generators\RouteDocsGenerator;

class Generator {

    /**
     * @return Generator
     */
    public static function forAllRoutes() {

        $key = "__APIDOCS_ALL";
        $routes = RouteParser::describeRoutes(RouteParser::getRoutes());

        return RouteDocsGenerator::getInstance($key, $routes);
    }

    /**
     * @param string $prefix
     *
     * @return Generator
     */
    public static function forRoutesWithPrefix($prefix) {

        $routes = RouteParser::describeRoutes(RouteParser::getRoutesByPrefix($prefix));

        return RouteDocsGenerator::getInstance($prefix, $routes);
    }

}
