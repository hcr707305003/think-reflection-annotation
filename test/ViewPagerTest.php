<?php
namespace test;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Shiroi\ThinkLogViewer\LogServer;
use Shiroi\ThinkReflectionAnnotation\reflection\DocParserFactory;

class ViewPagerTest extends TestCase
{
    /**
     * 调试
     * @doesNotPerformAssertions
     * @throws ReflectionException
     */
    public function testViewPager()
    {
        $class_name = self::class;
        $reflection = new ReflectionClass($class_name);
        //通过反射获取类的注释
        $doc = $reflection->getDocComment();
        //解析类的注释头
        $parse_result = DocParserFactory::getInstance()->parse($doc);
        //输出测试
        var_dump($doc);
        echo "\r\n";
        var_dump($parse_result);
        echo "\r\n";
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC + ReflectionMethod::IS_PROTECTED + ReflectionMethod::IS_PRIVATE);
        //遍历所有的方法

        foreach ($methods as $method) {
            //获取方法的注释
            $doc = $method->getDocComment();
            //解析注释
            $info = DocParserFactory::getInstance()->parse($doc);
            $metadata = $parse_result + $info;
            //获取方法的类型
            $method_flag = $method->isProtected();//还可能是public,protected类型的
            //获取方法的参数
            $params = $method->getParameters();
            $position = 0;    //记录参数的次序
            $defaults = $arguments = [];
            foreach ($params as $param) {
                $arguments[$param->getName()] = $position;
                //参数是否设置了默认参数如果设置了则获取其默认值
                $defaults[$position] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : NULL;
                $position++;
            }

            $call = array(
                'class_name' => $class_name,
                'method_name' => $method->getName(),
                'arguments' => $arguments,
                'defaults' => $defaults,
                'metadata' => $metadata,
                'method_flag' => $method_flag
            );
            var_dump($call);
            echo "\r\n-----------------------------------\r\n";
        }
    }
}