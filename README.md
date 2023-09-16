# think-reflection-annotation

### 1. class的反射插件(同个注解反射某个class、某个method、某个param、通过观察者模式关联绑定的某个method处理多个事务)

### 2. 安装
```composer
composer require shiroi/think-reflection-annotation
```

### 3. 使用
```php
//需要反射的类
$class = new TestClass();

//实例化反射类
$factory = \Shiroi\ThinkReflectionAnnotation\reflection\Factory::getInstance($class);
//反射类(类的详情、包含私有方法、私有属性)
var_dump($factory->getClassSubject());

//反射类的方法
var_dump($factory->getMethodsSubject());

//反射类的属性
var_dump($factory->getPropertiesSubject());

//反射类的doc
var_dump($factory->getClassDocComment());

//反射类的方法doc
var_dump($factory->getMethodsDocComment());
```

### 4. 注：所有框架都适用，引入便可使用了~
