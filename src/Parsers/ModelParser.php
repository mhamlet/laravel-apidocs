<?php

namespace MHamlet\Apidocs\Parsers;

use Illuminate\Database\Eloquent\Model;
use MHamlet\Apidocs\Parsers\Primitives\ClassParser;

class ModelParser {

    /**
     * @var string
     */
    private $model;

    /**
     * @var ClassParser
     */
    private $class_parser;

    /**
     * @var self[]
     */
    private static $instances = [];

    /**
     * @param string $model
     */
    private function __construct($model) {

        $this->model = $model;
        $this->class_parser = new ClassParser($this->model);
    }

    /**
     * @param string $model
     *
     * @return ModelParser
     */
    public static function getInstance($model) {

        if (!array_key_exists($model, self::$instances)) {
            self::$instances[$model] = new self($model);
        }

        return self::$instances[$model];
    }

    public function getFields() {

    }

    public function getAttributes() {

    }

    public function getSerializableAttributes() {

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getScopes() {

        $methods = $this->class_parser->getMethods();

        $scopes = collect();

        foreach ($methods as $method) {
            if (substr($method->name, 0, 5) !== 'scope') {
                continue;
            }

            $scopes->push(camel_case(substr($method->name, 5)));
        }

        return $scopes;
    }

    public function getRelations() {

    }
}
