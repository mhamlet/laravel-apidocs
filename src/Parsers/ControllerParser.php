<?php

namespace MHamlet\Apidocs\Parsers;

use Illuminate\Routing\Controller;
use MHamlet\Apidocs\Parsers\Primitives\ClassParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\ParserFactory;
use ReflectionClass;

/**
 * Class ControllerParser
 *
 * @package MHamlet\Apidocs\Parsers
 */
class ControllerParser {

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var ClassParser
     */
    private $class_parser;

    /**
     * @var null|\PhpParser\Node[]
     */
    private $statements;

    private $methodStatementsReturns = [];

    public function __construct($controller) {

        $this->controller = $controller;

        // Creating class parser for controller
        $this->class_parser = new ClassParser($this->controller);

//        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
//
//        try {
//            $this->parseClassStatements($this->parser->parse(file_get_contents($this->reflector->getFileName())));
//        }
//        catch (\Exception $e) {
//        }
    }

    /**
     * @param Node[] $statements
     */
    private function parseClassStatements($statements) {

        foreach ($statements as $statement) {

            if (!($statement instanceof ClassMethod)) {

                if (property_exists(get_class($statement), 'stmts')) {
                    $this->parseClassStatements($statement->stmts);
                }
            }
            else {
                $this->parseMethodStatement($statement);
            }
        }
    }

    /**
     * @param Node[] $statement
     */
    private function parseMethodStatement($statement) {

        // Skip magic methods
        if (substr($statement->name, 0, 2) == '__') {
            return;
        }

        $method = $statement->name;
        $statements = $statement->stmts;

        foreach ($statements as $statement) {
            $this->parseReturnStatement($method, $statement);
        }
    }

    /**
     * @param string $method
     * @param Node[] $statement
     */
    private function parseReturnStatement($method, $statement) {

        if (!($statement instanceof Return_)) {

            $statements = $statement->stmts;

            foreach ($statements as $statement) {
                $this->parseReturnStatement($method, $statement);
            }
        }
        else {

            if (!array_key_exists($method, $this->methodStatementsReturns)) {
                $this->methodStatementsReturns[$method] = [];
            }

            $this->methodStatementsReturns[$method][] = 'r';
        }
    }

    /**
     * @param string $method
     *
     * @return \stdClass
     */
    public function parseMethod($method) {

        return $this->class_parser->getMethod($method);

//        if (!is_null($this->statements)) {
//
//            $return = @$this->methodStatementsReturns[$method] ?: [];
//        }
    }
}
