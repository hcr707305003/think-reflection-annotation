<?php

namespace Shiroi\ThinkReflectionAnnotation\reflection;

class ClassParser
{
    protected object $object;

    protected object $class;

    protected array $properties = array();

    protected array $methods = array();

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return object
     */
    public function getClass()
    {
        return $this->object;
    }

    function parse($object) {
        $this->class = $this->object = $object;
        $this->parseClass();
    }

    private function parseClass() {
        //class
        $this->class->content = $this->getFileLines($this->object->getFileName(),$this->object->getStartLine(),$this->object->getEndLine());
        $doc = $this->getFileLines($this->object->getFileName(),1,$this->object->getStartLine());
        $this->class->namespace = $this->getNamespace($doc);
        $this->class->use = $this->getUse($doc);

        //propertie
        $this->properties = $this->object->getProperties();
        foreach ($this->properties as $k => $v) {
            $this->properties[$k]->permission = $this->getPermission($v);
        }

        //method
        $this->methods = $this->object->getMethods();
        foreach ($this->methods as $k => $v) {
            $this->methods[$k]->permission = $this->getPermission($v);
            $this->methods[$k]->content = $this->getFileLines($v->getFileName(),$v->getStartLine(),$v->getEndLine());
        }
    }

    function getPermission($o) {
        if($o->isPrivate()) {
            return 'private';
        }
        if($o->isProtected()) {
            return 'protected';
        }
        if($o->isPublic()) {
            return 'public';
        }
    }

    function getUse(array $doc): array
    {
        $use = [];
        foreach ($doc as $line) {
            $result = $this->both_field_exists(trim($line),'use',1);
            if($result['bool']) $use[] = str_replace([' ',';'],'',$result['cut_content']);
        }
        return $use;
    }

    function getNamespace(array $doc)
    {
        $namespace = "";
        foreach ($doc as $line) {
            $result = $this->both_field_exists(trim($line),'namespace',1);
            if($result['bool']) $namespace = str_replace([' ',';'],'',$result['cut_content']);
        }
        return $namespace;
    }

    function getFileLines($filename, $startLine = 1, $endLine = 50, $method = 'rb')
    {
        $content = array();
        if (version_compare(PHP_VERSION, '5.1.0', '>=')) { // 判断php版本（因为要用到SplFileObject，PHP>=5.1.0）
            $count = $endLine - $startLine;
            $fp = new \SplFileObject($filename, $method);
            $fp->seek($startLine - 1); // 转到第N行, seek方法参数从0开始计数
            for ($i = 0; $i <= $count; ++$i) {
                $content[] = $fp->current(); // current()获取当前行内容
                $fp->next(); // 下一行
            }
        } else { //PHP<5.1
            $fp = fopen($filename, $method);
            if (!$fp)
                return 'error:can not read file';
            for ($i = 1; $i < $startLine; ++$i) { // 跳过前$startLine行
                fgets($fp);
            }

            for ($i; $i <= $endLine; ++$i) {
                $content[] = fgets($fp); // 读取文件行内容
            }
            fclose($fp);
        }
        return array_filter($content); // array_filter过滤：false,null,''
    }

    /**
     * 判断文本是否在(头部|尾部|当前文本)存在
     * @param string $string (文本内容)
     * @param string $subString （是否存在该字段）
     * @param int $type (0=>不指定头部或者尾部, 1=>头部, 2=>尾部)
     * @return array
     */
    function both_field_exists(string $string, string $subString, int $type = 0): array
    {
        $bool = false;
        $cut_content = $string;
        if ($type == 0) {
            $bool = mb_strpos($string,$subString);
            if($bool) {
                $cut_content = str_replace($subString,'',$string);
            }
        } elseif ($type == 1) {
            $bool = mb_substr($string, 0, mb_strlen($subString)) === $subString;
            if($bool) {
                $cut_content = mb_substr($string,mb_strlen($subString),(mb_strlen($string)-mb_strlen($subString)));
            }
        } elseif ($type == 2) {
            $bool = mb_substr($string, mb_strpos($string, $subString)) === $subString;
            if($bool) {
                $cut_content = mb_substr($string,0,mb_strpos($string, $subString));
            }
        }
        return compact('bool','cut_content');
    }
}