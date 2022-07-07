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

    /**
     * @return void
     */
    public function testAutoLoad()
    {
        //方法名
        $action = request()->action();
        //控制器
        $controller = request()->controller();
        //多模块的模块名
        $module = app('http')->getName();
        //路由的后缀附加
        $routeConfig = app('route')->config();
        $controller_layer = $routeConfig['controller_layer'];
        $controller_suffix = $routeConfig['controller_suffix'];
        if($controller_suffix) {
            $controller = $controller . ucfirst($controller_layer);
        }
        //class name
        $class_name = 'app\\'.($module?$module.'\\':'').'controller\\'.$controller;
        //执行工厂
        $factory = Factory::getInstance($class_name);
        //返回结果
        $methodsDoc = $factory->getMethodsDocComment();
        //配置
        $reflection = config('reflection');
        //获取命名空间
        $getClassSubject = $factory->getClassSubject();
        $namespace = $getClassSubject->namespace;
        $class_arr = [];
        //反射class
        foreach ($methodsDoc[$action] as $key => $doc) {
            //定义回调方法
            if(in_array($key,array_keys($reflection))) {
                $isUse = true;
                foreach ($getClassSubject->use as $use) {
                    $bool = mb_substr($use, mb_strpos($use, $doc)) === $doc;
                    if($bool) {
                        $isUse = false;
                        $class_arr[] = $use;
                        break;
                    }
                }
                if($isUse) {
                    $class_arr[] = $namespace . '\\' . $doc;
                }
            }
        }
    }
}