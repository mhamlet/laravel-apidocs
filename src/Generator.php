<?php

namespace MHamlet\Apidocs;

use Illuminate\Routing\Controller;

class Generator {

    private $controllers = [];

    /**
     * Create a new Skeleton Instance
     */
    public function __construct() {

    }

    /**
     * @param Controller $controller
     */
    public function addController($controller) {

        if (!array_search($controller, $this->controllers)) {
            $this->controllers[] = $controller;
        }
    }

    public function generate() {

        foreach ($this->controllers as $controller) {

            $parser = new Parser($controller);
            dd($parser->parseMethod('index'));
        }
    }
}
