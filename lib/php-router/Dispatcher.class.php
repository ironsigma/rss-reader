<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://robap.github.com/php-router/
 * @package com\github\robap\php-router
 */
class Dispatcher
{
    /**
     * The suffix used to append to the class name
     * @var string
     */
    protected $suffix;

    /**
     * The path to look for classes (or controllers)
     * @var string
     */
    protected $classPath;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setSuffix('');
    }

    /**
     * Attempts to dispatch the supplied Route object. Returns false if it fails
     * @param Route $route
     * @param mixed $context
     * @throws classFileNotFoundException
     * @throws badClassNameException
     * @throws classNameNotFoundException
     * @throws classMethodNotFoundException
     * @throws classNotSpecifiedException
     * @throws methodNotSpecifiedException
     * @return mixed - result of controller method or FALSE on error
     */
    public function dispatch( Route $route, $context = null, $request_method='GET' )
    {
        $base_class = trim($route->getMapClass());
        $method     = trim($route->getMapMethod($request_method));
        $arguments  = array_merge($_GET, $route->getMapArguments());

        if( '' === $base_class )
            throw new ClassNotSpecifiedException('Class Name not specified');

        if( '' === $method )
            throw new MethodNotSpecifiedException('Method Name not specified');

        //Because the class could have been matched as a dynamic element,
        // it would mean that the value in $base_class is untrusted. Therefore,
        // it may only contain alphanumeric characters. Anything not matching
        // the regexp is considered potentially harmful.
        $base_class = str_replace('\\', '', $base_class);
        preg_match('/^[a-zA-Z0-9_]+$/', $base_class, $matches);
        if( count($matches) !== 1 )
            throw new BadClassNameException('Disallowed characters in class name ' . $base_class);

        //Apply the suffix
        $file_name = $this->classPath . $base_class . $this->suffix;
        $class = $base_class . str_replace($this->getFileExtension(), '', $this->suffix);
        
        //At this point, we are relatively assured that the file name is safe
        // to check for it's existence and require in.
        if( FALSE === file_exists($file_name) ) {
            $file_name = $this->classPath . strtoupper($base_class[0])
                . (strlen($base_class) == 1 ? '' : substr($base_class, 1))
                . $this->suffix;
            if( FALSE === file_exists($file_name) ) {
                throw new ClassFileNotFoundException("Class file \"$file_name\" not found");
            }
        }

        require_once($file_name);

        //Check for the class class
        if( FALSE === class_exists($class) )
            throw new ClassNameNotFoundException('Class not found ' . $class);

        //Check for the method
        if( FALSE === method_exists($class, $method))
            throw new ClassMethodNotFoundException('Method not found ' . $method);

        //All above checks should have confirmed that the class can be instatiated
        // and the method can be called
        return $this->dispatchController($class, $method, $arguments, $context);
    }
    
    /**
     * Create instance of controller and dispatch to it's method passing
     * arguments. Override to change behavior.
     * 
     * @param string $class
     * @param string $method
     * @param array $args
     * @return mixed - result of controller method
     */
    protected function dispatchController($class, $method, $args, $context = null)
    {
        $obj = new $class($context);
        return call_user_func(array($obj, $method), $args);
    }

    /**
     * Sets a suffix to append to the class name being dispatched
     * @param string $suffix
     * @return Dispatcher
     */
    public function setSuffix( $suffix )
    {
        $this->suffix = $suffix . $this->getFileExtension();

        return $this;
    }

    /**
     * Set the path where dispatch class (controllers) reside
     * @param string $path
     * @return Dispatcher
     */
    public function setClassPath( $path )
    {
        $this->classPath = preg_replace('/\/$/', '', $path) . '/';

        return $this;
    }

    public function getFileExtension()
    {
        return '.php';
    }
}

/**
 * @package com\github\robap\php-router
 */
class BadClassNameException extends Exception{}
/**
 * @package com\github\robap\php-router
 */
class ClassFileNotFoundException extends Exception{}
/**
 * @package com\github\robap\php-router
 */
class ClassNameNotFoundException extends Exception{}
/**
 * @package com\github\robap\php-router
 */
class ClassMethodNotFoundException extends Exception{}
/**
 * @package com\github\robap\php-router
 */
class ClassNotSpecifiedException extends Exception{}
/**
 * @package com\github\robap\php-router
 */
class MethodNotSpecifiedException extends Exception{}
