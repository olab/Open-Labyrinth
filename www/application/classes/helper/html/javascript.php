<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 11/11/2012
 * Time: 1:57 πμ
 * To change this template use File | Settings | File Templates.
 */
class Helper_Html_Javascript
{
    static protected $scripts = array();
    static protected $will_already_be_auto_rendered = false;

    static public function add($file)
    {
        if (is_array($file)) {
            self::$scripts = arr::merge(self::$scripts, $file);
        } else
            if (!in_array($file, self::$scripts))
                self::$scripts[] = $file;
        //make sure the script tags are inserted in the header just before sending the page to the browser

    }

    static public function render($print = FALSE)
    {
        $output = '';
        foreach (self::$scripts as $script)
            $output .= html::script($script);

        if ($print)
            echo $output;

        return $output;
    }



    static public function render_in_head() {
        //insert the script tags just before the </head> tag
        //The to be flushed data is found in Event::$data
        Event::$data = str_replace("</head>", self::render() . " </head>", Event::$data);
    }

} // End javascript_Core
