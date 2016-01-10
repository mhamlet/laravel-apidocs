<?php

namespace MHamlet\Apidocs\Parsers\Primitives;

use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class ClassParser
 *
 * Primitive class parser. Depends on phpDocumentor2 and PHP-Parser
 *
 * @package MHamlet\Apidocs\Parsers
 */
class ClassParser {

    const CLASS_METHOD_PUBLIC = 2;
    const CLASS_METHOD_PRIVATE = 4;
    const CLASS_METHOD_PROTECTED = 8;
    const CLASS_METHOD_ABSTRACT = 16;
    const CLASS_METHOD_FINAL = 32;
    const CLASS_METHOD_OWN = 64;

    /**
     * Class to parse
     *
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $class_file_contents = '';

    /**
     * @var ReflectionClass
     */
    private $reflector;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var MethodParser[]
     */
    private $method_parsers = [];

    /**
     * @param string $class Class name with namespace to parse
     */
    public function __construct($class) {

        // Initialize class
        $this->class = $class;

        // Create new parser instance from ParserFactory
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);

        // Create new Reflection class to give it to phpDocumentor later
        $this->reflector = new ReflectionClass($this->class);

        // Get class file contents to parse it
        $this->class_file_contents = file_get_contents($this->reflector->getFileName());
    }

    /**
     * @return ReflectionClass
     */
    public function getReflector() {

        return $this->reflector;
    }

    /**
     * @return Parser
     */
    public function getParser() {

        return $this->parser;
    }

    /**
     * @return string
     */
    public function getCode() {

        return $this->class_file_contents;
    }

    /**
     * Get all methods of class
     *
     * @param int $filter Filter bitmask
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMethods($filter = 0) {

        $methods = collect();

        $reflectorFilter = 0;

        // Generate filter for reflection class
        if ($filter & ClassParser::CLASS_METHOD_PROTECTED) {
            $reflectorFilter |= ReflectionMethod::IS_PROTECTED;
        }

        if ($filter & ClassParser::CLASS_METHOD_PUBLIC) {
            $reflectorFilter |= ReflectionMethod::IS_PUBLIC;
        }

        if ($filter & ClassParser::CLASS_METHOD_PRIVATE) {
            $reflectorFilter |= ReflectionMethod::IS_PRIVATE;
        }

        if ($filter & ClassParser::CLASS_METHOD_ABSTRACT) {
            $reflectorFilter |= ReflectionMethod::IS_ABSTRACT;
        }

        if ($filter & ClassParser::CLASS_METHOD_FINAL) {
            $reflectorFilter |= ReflectionMethod::IS_FINAL;
        }

        if ($reflectorFilter === 0) {
            $reflectorMethods = $this->reflector->getMethods();
        }
        else {

            $reflectorMethods = $this->reflector->getMethods($reflectorFilter);
        }

        $includeInheritedMethods = !($filter & ClassParser::CLASS_METHOD_OWN);

        foreach ($reflectorMethods as $method) {

            if (!$includeInheritedMethods && $method->class !== $this->class) {
                continue;
            }

            $methods->push($this->getMethodParser($method)->getParsedMethod());
        }

        return $methods;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getOwnMethods() {

        return $this->getMethods(self::CLASS_METHOD_OWN);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPublicMethods() {

        return $this->getMethods(self::CLASS_METHOD_PUBLIC);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPrivateMethods() {

        return $this->getMethods(self::CLASS_METHOD_PRIVATE);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getProtectedMethods() {

        return $this->getMethods(self::CLASS_METHOD_PROTECTED);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAbstractMethods() {

        return $this->getMethods(self::CLASS_METHOD_ABSTRACT);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFinalMethods() {

        return $this->getMethods(self::CLASS_METHOD_FINAL);
    }

    /**
     * @param string $method
     *
     * @return \stdClass
     */
    public function getMethod($method) {

        return $this->getMethodParser($method)->getParsedMethod();
    }

    /**
     * @param string $method
     *
     * @return MethodParser
     */
    public function getMethodParser($method) {

        if (!array_key_exists($method, $this->method_parsers)) {
            $this->method_parsers[$method] = new MethodParser($this, $method);
        }

        return $this->method_parsers[$method];
    }
}
