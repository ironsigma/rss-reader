<?php
/**
 * Templating class
 * @package com\izylab\reader
 */
class Template {
    protected static $template_dir = 'templates/';
    protected $vars;
    protected $template_file;
    public function __construct($template_file, $vars=array()) {
        if ( !file_exists(self::$template_dir.$template_file) ) {
            throw new Exception('Template file '. $template_file .' not found');
        }
        $this->template_file = $template_file;
        $this->vars = $vars;
    }
    public function render() {
        ob_start();
        extract($this->vars);
        include self::$template_dir.$this->template_file;
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
