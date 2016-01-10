<?php

namespace MHamlet\Apidocs\Parsers\Primitives;

use phpDocumentor\Reflection\DocBlock;

class MethodParser {

    /**
     * @var ClassParser
     */
    private $class;

    /**
     * @var ClassParser
     */
    private $originalClass;

    /**
     * @var string
     */
    private $method;

    /**
     * @var \ReflectionMethod
     */
    private $reflector;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @param ClassParser $class
     * @param string      $method
     */
    public function __construct($class, $method) {

        $this->class = $class;
        $this->method = $method;

        $this->reflector = $this->class->getReflector()->getMethod($this->method);
    }

    /**
     * @return ClassParser
     */
    private function getOriginalClass() {

        if (is_null($this->originalClass)) {
            $this->originalClass = new ClassParser($this->reflector->getDeclaringClass()->name);
        }

        return $this->originalClass;
    }

    /**
     * @return DocBlock
     */
    private function getDocblock() {

        if (is_null($this->docBlock)) {

            // Creating docblock for parsing the docs
            $this->docBlock = new DocBlock($this->reflector);
        }

        return $this->docBlock;
    }

    private function getTags($tag_name, $has_type = false, $has_name = false, $has_description = false) {

        // Parsing params
        $tags = collect();

        $docBlockTags = $this->getDocblock()->getTagsByName($tag_name);

        foreach ($docBlockTags as $docBlockTag) {

            $tag = new \stdClass();

            if ($has_type) {
                $tag->type = $docBlockTag->getType();
            }

            if ($has_name) {

                $tag->name = $docBlockTag->getVariableName();

                // Removing $ sign from name
                if (substr($tag->name, 0, 1) == '$') {
                    $tag->name = substr($tag->name, 1);
                }
            }

            if ($has_description) {

                $tag->description = $docBlockTag->getDescription();
            }

            $tags->push($tag);
        }

        return $tags;
    }

    /**
     * @return string
     */
    public function getCode() {

        // Getting class code
        $class_code = explode(PHP_EOL, $this->getOriginalClass()->getCode());

        // Getting start and end lines
        $start_line = $this->reflector->getStartLine();
        $end_line = $this->reflector->getEndLine();

        $method_code = "";

        for ($line = $start_line; $line <= $end_line; $line++) {

            if (trim($class_code[$line - 1]) === "") continue;

            $method_code .= $class_code[$line - 1] . PHP_EOL;
        }

        return rtrim($method_code);
    }

    /**
     * @return \stdClass
     */
    public function getParsedMethod() {

        $method = new \stdClass();
        $method->name = $this->reflector;
        $method->params = $this->getTags('apiParam', true, true, true);
        $method->description = $this->getDocblock()->getShortDescription();
        $method->returns = [];

        return $method;
    }
}
