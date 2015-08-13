<?php
class TextStream {
    private $index = 0;
    private $text;
    private $length;

    public function __construct($text) {
        $this->text = $text;
        $this->length = strlen($text);
    }

    public function consume($count) {
        $string = $this->peek($count);
        $this->index += strlen($string);
        return $string;
    }

    public function consumeAll($str) {
        $consumed = '';
        if (!is_array($str)) {
            $len = strlen($str);
        }

        while (true) {
            if (is_array($str)) {
                $start_index = $this->index;
                foreach ($str as $s) {
                    $len = strlen($s);
                    $found = substr($this->text, $this->index, $len);
                    if ($found == $s) {
                        $consumed .= $found;
                        $this->index += $len;
                        break;
                    }
                }
                if ($start_index == $this->index) {
                    break;
                }
            } else {
                $found = substr($this->text, $this->index, $len);
                if ($found != $str) {
                    break;
                }
                $consumed .= $found;
                $this->index += $len;
            }
        }
        return $consumed;
    }

    public function consumeTo($str) {
        if (is_array($str)) {
            $pos = $this->length;
            foreach ($str as $s) {
                $loc = strpos($this->text, $s, $this->index);
                if ($loc === false) {
                    continue;
                }
                if ($loc < $pos) {
                    $pos = $loc;
                }
            }
            if ($pos == $this->length) {
                return '';
            }

        } else {
            $pos = strpos($this->text, $str, $this->index);
            if ($pos === false) {
                return '';
            }
        }

        $string = substr($this->text, $this->index, $pos - $this->index);
        $this->index = $pos;
        return $string;
    }

    public function eof() {
        return $this->index >= $this->length;
    }

    public function charsLeft() {
        return $this->length - $this->index;
    }

    public function peek($count=1) {
        return substr($this->text, $this->index, $count);
    }
}

class LinkParser {
    public static function parse_link($link) {
        /* Tokens:
            <
                WS*
                tag
                (
                    WS
                    attrib
                    (
                        WS*
                        =
                        WS*
                        value
                    )?
                )*
                WS*
            >
         */

        $attribs = array();
        $stream = new  TextStream($link);

        if ($stream->consume(1) != '<') {
            return false;
        }

        $stream->consumeAll(' ');

        if ($stream->consume(1) != 'a') {
            return false;
        }

        $stream->consumeAll(' ');

        while ($stream->peek() != '>') {
            $attrib = $stream->consumeTo(array(' ', '=', '>'));
            $stream->consumeAll(' ');
            if ($stream->peek() == '=') {
                $stream->consume(1);
                $stream->consumeAll(' ');
                if ($stream->peek() == '"' || $stream->peek() == "'") {
                    $char = $stream->consume(1);
                    $value = $stream->consumeTo($char);
                    if (!$value) {
                        throw new Exception("unfinished quote");
                    }
                    $attribs[$attrib] = $value;
                    $stream->consume(1);
                } else {
                    $attribs[$attrib] = $stream->consumeTo(array(' ', '>'));
                }
            } else {
                if ($attrib) {
                    $attribs[$attrib] = '';
                }
            }
            $stream->consumeAll(' ');
        }

        return $attribs;
    }

    public static function html_link($attribs) {
        $attrs = array('a');
        foreach ($attribs as $key => $value) {
            if (!$value) {
                $attrs[] = $key;
            } else {
                $value = htmlentities($value);
                $attrs[] = "$key=\"$value\"";
            }
        }
        return '<' . join(' ', $attrs) . '>';
    }
}

/*
$html = '<a  href="http://google.com/"   nofollow>Link</a>'
    .'< a  target="top" href="http://yahoo.com/">Second Link</a>'
    .'<a href=index.html class =foo noframe target = top>3</a>';

echo preg_replace_callback('|<\\s*a\\s*[^>]+>|',
    function ($matches) {
        $link = parse_link($matches[0]);
        if (isset($link['href'])) {
        }

        return html_link($link);
    }, $html) . "\n";
 */
