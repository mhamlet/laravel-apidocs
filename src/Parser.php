<?php

namespace MHamlet\Apidocs;

use Illuminate\Routing\Controller;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\ParserFactory;
use ReflectionClass;

/**
 * Class Parser
 *
 * @package MHamlet\Apidocs
 */
class Parser {

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var ReflectionClass
     */
    private $reflector;

    /**
     * @var \PhpParser\Parser
     */
    private $parser;

    /**
     * @var null|\PhpParser\Node[]
     */
    private $statements;

    private $methodStatementsReturns = [];

    public function __construct($controller) {

        $this->controller = $controller;

        $this->reflector = new ReflectionClass($this->controller);
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);

        try {
            $this->statements = $this->parser->parse(file_get_contents($this->reflector->getFileName()));
            $this->parseClassStatements();
        }
        catch (\Exception $e) {
        }
    }

    /**
     * @param Node $statements
     */
    private function parseClassStatements($statements = false) {

        if ($statements === FALSE) {
            $statements = $this->statements;
        }

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
     * @param Node $statement
     */
    private function parseMethodStatement($statement) {

        // Parsing methods
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
     * @param Node   $statement
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

    public function parseMethod($method) {

        $methodReflector = $this->reflector->getMethod($method);

        // Creating docblock for parsing the docs
        $docblock = new DocBlock($methodReflector);

        // Parsing params
        $params = [];

        foreach ($docblock->getTagsByName('apiParam') as $param) {

            // Removing $ sign from name
            $name = $param->getVariableName();

            if (substr($name, 0, 1) == '$') {
                $name = substr($name, 1);
            }

            $params[] = [
                'type' => $param->getType(),
                'name' => $name,
            ];
        }

        // Parse for return value
        $return = [];

        if (!is_null($this->statements)) {

            $return = @$this->methodStatementsReturns[$method] ?: [];
        }

        return [
            'description' => $docblock->getText(),
            'params' => $params,
            'return' => $return,
        ];
    }
}
