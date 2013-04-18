<?php
class Route {
    private $url;
    private $uri;
    private $mapping;
    private $validations;
    private $params;

    public function __construct($url, array $mapping, array $validations=null) {
        $this->url = $url;
        $this->mapping = $mapping;
        $this->validations = $validations;
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
        if ( preg_match('@\{?:[\w]+\}?@', $this->mapping['controller']) ) {
            $controller = preg_replace_callback('@\{?:[\w]+\}?@',
                array($this, 'rep_params'), $this->mapping['controller']);
            $this->mapping['controller'] = strtoupper($controller[0]) . substr($controller, 1);
        }

        // replace params in method names
        foreach ( $this->mapping['methods'] as $index => $method ) {
            if ( preg_match('@\{?:[\w]+\}?@', $method['name']) ) {
                $name = preg_replace_callback('@\{?:[\w]+\}?@',
                    array($this, 'rep_params'), $method['name']);
                if ( $method['name'][0] == '{' ) {
                    $name = strtolower($name[0]) . substr($name, 1);
                }
                $this->mapping['methods'][$index]['name'] = $name;
            }
        }

        return true;
    }

    public function getController($method_type) {
        $type = strtolower($method_type);
        $match = null;
        foreach ( $this->mapping['methods'] as $method ) {
            if ( array_key_exists('type', $method) ) {
                if ( $type == $method['type'] ) {
                    $match = $method['name'];
                    break;
                }
            } else {
                $match = $method['name'];
            }
        }
        if ( !$match ) {
            throw new NoHandlerFoundException($this->uri, $method_type, $this->url);
        }
        return array('class' => $this->mapping['controller'], 'method' => $match, 'type' => $method_type);
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
