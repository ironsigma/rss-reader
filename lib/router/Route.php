<?php
class Route {
    const GET = 1;
    const POST = 2;
    const PUT = 3;
    const DELETE = 4;

    private $url;
    private $uri;
    private $mapping = array();
    private $validations;
    private $params;

    public static function mapUrl($url) {
        return new Route($url);
    }

    public function __construct($url) {
        $this->url = $url;
    }

    public function validateToken($token, $regex) {
        $this->validations[$token] = $regex;
        return $this;
    }

    public function addController($class) {
        $this->mapping[] = array('class' => $class, 'methods' => array());
        return $this;
    }

    public function addAction($method, $type=null) {
        $count = count($this->mapping) - 1;
        if ( $count == -1 ) {
            throw new Exception('No controller in specified');
        }
        $this->mapping[count($this->mapping)-1]['methods'][] = array('name' => $method, 'type' => $type);
        return $this;
    }

    public function getParams() {
        return $this->params;
    }

    public function matches($uri) {
        $qs = '';
        $pos = strpos($uri, '?');
        if ( $pos ) {
            foreach ( explode('&', substr($uri, $pos+1)) as $pair ) {
                if ( strpos($pair, '=') ) {
                    list($var, $val) = explode('=', $pair, 2);
                    $this->params[urldecode($var)] = urldecode($val);
                } else {
                    $this->params[urldecode($pair)] = null;
                }
            }
            $uri = substr($uri, 0, $pos);
        }

        // build URL regex
        $regex_url = preg_replace_callback('@:[\w]+@', array($this, 'rep_validators'), $this->url);
        if ( !preg_match('@^'. $regex_url .'$@', $uri, $param_values) ) {
            return false;
        }

        $this->uri = $uri;

        array_shift($param_values);

        // save params
        preg_match_all('@:([\w]+)@', $this->url, $param_names, PREG_PATTERN_ORDER);
        $param_names = $param_names[0];

        foreach($param_names as $index => $value) {
            $this->params[$value] = urldecode($param_values[$index]);
        }

        // replace params in controller name
        foreach ( $this->mapping as $map_idx => $map ) {
            if ( preg_match('@\{?:[\w]+\}?@', $map['class']) ) {
                $class = preg_replace_callback('@\{?:[\w]+\}?@',
                    array($this, 'rep_params'), $map['class']);
                $this->mapping[$map_idx]['class'] = strtoupper($class[0]) . substr($class, 1);
            }

            // replace params in method names
            foreach ( $map['methods'] as $action_idx => $method ) {
                if ( preg_match('@\{?:[\w]+\}?@', $method['name']) ) {
                    $name = preg_replace_callback('@\{?:[\w]+\}?@',
                        array($this, 'rep_params'), $method['name']);
                    if ( $method['name'][0] == '{' ) {
                        $name = strtolower($name[0]) . substr($name, 1);
                    }
                    $this->mapping[$map_idx]['methods'][$action_idx]['name'] = $name;
                }
            }
        }

        return true;
    }

    private function typeToConst($method_type) {
        switch ( strtolower($method_type) ) {
        case 'get': return Route::GET;
        case 'put': return Route::PUT;
        case 'delete': return Route::DELETE;
        case 'post': return Route::POST;
        }
        return null;
    }

    public function getController($method_type) {
        $type = $this->typeToConst($method_type);
        $match = null;
        $class = null;
        foreach ( $this->mapping as $controller ) {
            $class = $controller['class'];
            foreach ( $controller['methods'] as $method ) {
                if ( $method['type'] ) {
                    if ( $type == $method['type'] ) {
                        $match = $method['name'];
                        break 2;
                    }
                } else {
                    $match = $method['name'];
                }
            }
        }
        if ( !$match ) {
            throw new NoHandlerFoundException($this->uri, $method_type, $this->url);
        }
        return array('class' => $class, 'method' => $match, 'type' => $method_type);
    }

    public function rep_params($matches) {
        $match = $matches[0];
        if ( $match[0] == '{' ) {
            $match = substr($match, 1, -1);
            $upper = true;
        } else {
            //$match = substr($match, 1);
            $upper = false;
        }
        if ( $this->params && array_key_exists($match, $this->params) ) {
            $value = $this->params[$match];
            if ( $upper ) {
                $value = strtoupper($value[0]) . substr($value, 1);
            }
            return $value;
        }
        return $match;
    }

    public function rep_validators($matches) {
        if ( $this->validations && array_key_exists($matches[0], $this->validations) ) {
            return '('. $this->validations[$matches[0]] .')';
        }
        return '([a-zA-Z0-9_\+\-%]+)';
    }
}
