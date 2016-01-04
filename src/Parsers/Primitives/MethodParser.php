<?php

namespace MHamlet\Apidocs\Parsers\Primitives;

use phpDocumentor\Reflection\DocBlock;

class MethodParser {

    /**
     * @var \ReflectionMethod
     */
    private $method;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(\ReflectionMethod $method) {

        $this->method = $method;
    }

    /**
     * @return DocBlock
     */
    private function getDocblock() {

        if (is_null($this->docBlock)) {

            // Creating docblock for parsing the docs
            $this->docBlock = new DocBlock($this->method);
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
     * @return \stdClass
     */
    public function getParsedMethod() {

        $method = new \stdClass();
        $method->name = $this->method->name;
        $method->params = $this->getTags('apiParam', true, true, true);

        return $method;
    }
}
