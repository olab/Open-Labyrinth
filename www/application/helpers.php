<?php

function admin_url($path = '')
{
    $url = URL::base(true);
    if ($path && is_string($path)) {
        $url .= ltrim($path, '/');
    }

    return $url;
}

function get_option($option, $default = false)
{
    return Model_Leap_Option::get_option($option, $default);
}


/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 *
 * @param string $data Value to check to see if was serialized.
 * @param bool $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized($data, $strict = true)
{
    // if it isn't a string, it isn't serialized.
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace) {
            return false;
        }
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3) {
            return false;
        }
        if (false !== $brace && $brace < 4) {
            return false;
        }
    }
    $token = $data[0];
    switch ($token) {
        case 's' :
            if ($strict) {
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
        // or else fall through
        case 'a' :
        case 'O' :
            return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b' :
        case 'i' :
        case 'd' :
            $end = $strict ? '$' : '';

            return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
    }

    return false;
}

function wp_upload_dir()
{
    return array(
        'basedir' => DOCROOT . 'files/',
        'baseurl' => DOCROOT . 'files/',
    );
}

function is_ssl()
{
    if (isset($_SERVER['HTTPS'])) {
        if ('on' == strtolower($_SERVER['HTTPS'])) {
            return true;
        }
        if ('1' == $_SERVER['HTTPS']) {
            return true;
        }
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }

    return false;
}

function current_user_can($capability)
{
    //TODO: implement or replace
    return true;
}

function esc_html($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false)
{
    $string = (string)$string;

    if (0 === strlen($string)) {
        return '';
    }

    // Don't bother if there are no specialchars - saves some processing
    if (!preg_match('/[&<>"\']/', $string)) {
        return $string;
    }

    // Account for the previous behaviour of the function when the $quote_style is not an accepted value
    if (empty($quote_style)) {
        $quote_style = ENT_NOQUOTES;
    } elseif (!in_array($quote_style, array(0, 2, 3, 'single', 'double'), true)) {
        $quote_style = ENT_QUOTES;
    }

    // Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
    if (!$charset) {
        static $_charset = null;
        if (!isset($_charset)) {
            $_charset = 'UTF-8';
        }
        $charset = $_charset;
    }

    if (in_array($charset, array('utf8', 'utf-8', 'UTF8'))) {
        $charset = 'UTF-8';
    }

    $_quote_style = $quote_style;

    if ($quote_style === 'double') {
        $quote_style = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ($quote_style === 'single') {
        $quote_style = ENT_NOQUOTES;
    }

    if (!$double_encode) {
        // Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
        // This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
        //$string = wp_kses_normalize_entities( $string );
    }

    $string = @htmlspecialchars($string, $quote_style, $charset, $double_encode);

    // Backwards compatibility
    if ('single' === $_quote_style) {
        $string = str_replace("'", '&#039;', $string);
    }

    return $string;
}

/**
 * @return wpdb
 */
function getWPDB()
{
    global $wpdb;

    if (!isset($wpdb)) {
        $config = include './application/config/database.php';
        $config = $config['default']['connection'];
        $GLOBALS['wpdb'] = new wpdb($config['username'], $config['password'], $config['database'],
            $config['hostname']);
        $wpdb = $GLOBALS['wpdb'];
    }

    return $wpdb;
}

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  mixed $target
 * @param  string|array $key
 * @param  mixed $default
 * @return mixed
 */
function data_get($target, $key, $default = null)
{
    if (is_null($key)) {
        return $target;
    }

    $key = is_array($key) ? $key : explode('.', $key);

    while (($segment = array_shift($key)) !== null) {
        if ($segment === '*') {
            if (!is_array($target)) {
                return value($default);
            }

            $result = ArrayHelper::pluck($target, $key);

            return in_array('*', $key) ? ArrayHelper::collapse($result) : $result;
        }

        if (ArrayHelper::accessible($target) && ArrayHelper::exists($target, $segment)) {
            $target = $target[$segment];
        } elseif (is_object($target) && isset($target->{$segment})) {
            $target = $target->{$segment};
        } else {
            return value($default);
        }
    }

    return $target;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed $value
 * @return mixed
 */
function value($value)
{
    return $value instanceof Closure ? $value() : $value;
}