<?php

namespace Shiroi\ThinkReflectionAnnotation\reflection;

/**
 * Class DocParserFactory 解析doc
 * @example DocParserFactory::getInstance->parse($doc);
 */

class DocParserFactory{
    private static $p;

    public static function getInstance(): DocParser
    {
        if(self::$p == null){
            self::$p = new DocParser;
        }
        return self::$p;
    }
}
