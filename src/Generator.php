<?php

namespace MHamlet\Apidocs;

use MHamlet\Apidocs\Generators\RouteDocsGenerator;

class Generator {

    /**
     * @return RouteDocsGenerator
     */
    public static function forAllRoutes() {

        $key = "__APIDOCS_ALL";
        $routes = RouteResolver::describeRoutes(RouteResolver::getRoutes());

        return RouteDocsGenerator::getInstance($key, $routes);
    }

    /**
     * @param string $prefix
     *
     * @return RouteDocsGenerator
     */
    public static function forRoutesWithPrefix($prefix) {

        $routes = RouteResolver::describeRoutes(RouteResolver::getRoutesByPrefix($prefix));

        return RouteDocsGenerator::getInstance($prefix, $routes);
    }
}
