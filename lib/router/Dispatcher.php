<?php
class Dispatcher {
    private $routes = array();
    private $suffix = '';
    private $classPath = '';

    public function setSuffix($suffix) {
        $this->suffix = $suffix;
    }

    public function setClassPath($classPath) {
        $this->classPath = $classPath;
    }

    public function addRoute(Route $route) {
        $this->routes[] = $route;
    }

    public function findRoute($uri) {
        foreach ($this->routes as $route) {
            if ( $route->matches($uri) ) {
                return $route;
            }
        }
        throw new NoRouteFoundException($uri);
    }

    public function dispatch(Route $route, $method) {
        $controller = $route->getController($method);
        $class = $controller['class'];
        $method = $controller['method'];

        if ( '' === $method ) {
            throw new UnableToInvokeMethodException($class, $method, 'No method specified');
        }

        if ( '' === $class ) {
            throw new UnableToInvokeMethodException($class, $method, 'No class specified');
        }

        $class = str_replace('\\', '', $class);
        if ( !preg_match('/^[a-zA-Z0-9_]+$/', $class, $matches) ) {
            throw new UnableToInvokeMethodException($class, $method, 'Invalid class name');
        }
        $class = $class.$this->suffix;
        $file = $this->classPath."/$class.php";

        if ( !file_exists($file) ) {
            throw new UnableToInvokeMethodException($class, $method, 'Class file not found "'. $file . '"');
        }

        require_once($file);

        if ( !class_exists($class) ) {
            throw new UnableToInvokeMethodException($class, $method, 'Class "'. $class .'" not found in file "'. $file . '"');
        }

        if ( !method_exists($class, $method) ) {
            throw new UnableToInvokeMethodException($class, $method, 'Method "'. $method .'" not found in class "'. $class . '"');
        }

        try {
            $reflec = new ReflectionMethod($class, $method);
            $reflec->invokeArgs(new $class(), array($route->getParams(), $controller['type']));
        } catch ( ReflectionException $ex ) {
            throw new UnableToInvokeMethodException($class, $method, $ex->getMessage());
        }
    }
}
