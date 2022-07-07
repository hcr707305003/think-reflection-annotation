<?php

namespace Shiroi\ThinkReflectionAnnotation\reflection;

use ReflectionClass;
use ReflectionMethod;

/**
 * Class Factory
 */

class Factory{

    private static $factory;

    private static $classParser;

    private $className;

    private $class_docComment = [];

    private $methods_docComment = [];

    private $class_subject = [];

    private $methods_subject = [];

    private $properties_subject = [];

    private $reflectionClass;

    public static function getInstance($class_name)
    {
        if(self::$factory == null){
            self::$factory = new Factory($class_name);
        }
        return self::$factory;
    }

    public function __construct($className)
    {
        $this->initParam($className);
        $this->docParserClass();
        $this->classParserClass();
    }

    /**
     * @return array
     */
    public function getClassDocComment(): array
    {
        return $this->class_docComment;
    }

    /**
     * @return array
     */
    public function getMethodsDocComment(): array
    {
        return $this->methods_docComment;
    }

    /**
     * @return object
     */
    public function getClassSubject(): object
    {
        return $this->class_subject;
    }

    /**
     * @return array
     */
    public function getMethodsSubject(): array
    {
        return $this->methods_subject;
    }

    /**
     * @return array
     */
    public function getPropertiesSubject(): array
    {
        return $this->properties_subject;
    }



    private function initParam($className)
    {
        $this->className = $className;
        $this->reflectionClass = new ReflectionClass($this->className);
    }

    private function docParserClass()
    {
        //DocParser class
        $this->class_docComment = (new DocParser)->parse(
            $this->reflectionClass->getDocComment()
        );
        //DocParser methods
        $methods = $this->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC + ReflectionMethod::IS_PROTECTED + ReflectionMethod::IS_PRIVATE);

        foreach ($methods as $method) {
            $this->methods_docComment[$method->getName()] = (new DocParser)->parse(
                $method->getDocComment());
        }
    }

    private function classParserClass()
    {
        $class_parser = new ClassParser;
        $class_parser->parse($this->reflectionClass);
        //ClassParser class
        $this->class_subject = $class_parser->getClass();
        //ClassParser methods
        $this->methods_subject = $class_parser->getMethods();
        //ClassParser properties
        $this->properties_subject = $class_parser->getProperties();
    }
}
