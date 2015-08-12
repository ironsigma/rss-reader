<?php
/**
 * Templating class
 * @package com\hawkprime\reader
 */
class Template {
    protected static $path = 'templates';
    protected static $suffix = '.php';
    protected $vars;
    protected $template_file;

    public static function setTemplateDir($path) {
        static::$path = $path;
    }

    public function __construct($template_file, $vars=array()) {
        $this->template_file = static::file($template_file);
        $this->vars = $vars;
    }
    public function render() {
        ob_start();
        extract($this->vars);
        include $this->template_file;
        $ouput = ob_get_contents();
        ob_end_clean();
        return $ouput;
    }
    public function display() {
        echo $this->render();
    }
    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }
    public function __get($name) {
        return $this->vars[$name];
    }

    public static function file($file) {
        $inc_file = static::joinPaths(static::$path, $file.static::$suffix);
        if ( !file_exists($inc_file) ) {
            throw new Exception('Template file "'. $inc_file .'" not found');
        }
        return $inc_file;
    }

    public static function joinPaths(/*...*/) {
        $path_list = func_get_args();
        $path_count = count($path_list);
        $root = count($path_list) && substr($path_list[0], 0, 1) == '/' ? '/' : '';
        $components = array();
        for ( $i = 0; $i < $path_count; $i ++ ) {
            $path = trim($path_list[$i], '/');
            if ( strlen($path) ) {
                $components[] = $path;
            }
        }
        return $root . join('/', $components);
    }

}
