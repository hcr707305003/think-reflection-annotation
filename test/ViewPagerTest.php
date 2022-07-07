<?php
namespace test;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Shiroi\ThinkLogViewer\LogServer;
use Shiroi\ThinkReflectionAnnotation\reflection\DocParserFactory;
use Shiroi\ThinkReflectionAnnotation\reflection\Factory;

/**
 * debug
 * @author shiroi
 */
class ViewPagerTest extends TestCase
{
    /**
     * 调试
     * @doesNotPerformAssertions
     * @throws ReflectionException
     */
    public function testViewPager()
    {
        $factory = Factory::getInstance(self::class);
        var_dump($factory->getMethodsDocComment());
    }
}