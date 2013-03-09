<?php
/**
 * Templating class
 * @package com\izylab\reader
 */
class Template {
    protected static $template_dir = 'templates';
    protected static $template_suffix = '.template.php';
    protected $vars;
    protected $template_file;

    public static function setTemplateDir($template_dir) {
        self::$template_dir = $template_dir.'/';
    }

    public function __construct($template_file, $vars=array()) {
        $this->template_file = self::$template_dir.$template_file.self::$template_suffix;
        if ( !file_exists($this->template_file) ) {
            throw new Exception('Template file "'. $this->template_file .'" not found');
        }
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
}
