<?php

class CustomAssetManager
{
    /**
     * @var array
     */
    private static $scripts = [];

    /**
     * @var array
     */
    private static $styles = [];

    /**
     * @var array
     */
    private static $raw_scripts = [];

    /***
     * @param string $handle
     * @param string $code
     */
    public static function addRawScript($handle, $code)
    {
        static::$raw_scripts[$handle][] = $code;
    }

    /**
     * @param array $controller
     * @return array
     */
    public static function loadAssets($templateData)
    {
        if (empty($templateData)) {
            $templateData = [];
        }

        foreach (static::getScripts() as $script) {
            $templateData['scripts_stack'][] = $script;
        }

        foreach (static::getStyles() as $script) {
            $templateData['styles_stack'][] = $script;
        }

        foreach (static::getRawScripts() as $script) {
            $templateData['raw_scripts_stack'][] = $script;
        }

        return $templateData;
    }

    /**
     * @param string $handle
     * @param string $path
     */
    public static function addScript($handle, $path)
    {
        static::$scripts[$handle][] = static::sanitizePath($path);
    }

    /**
     * @param string $handle
     * @param string $path
     */
    public static function addStyle($handle, $path)
    {
        static::$styles[$handle][] = static::sanitizePath($path);
    }

    /**
     * @return array
     */
    public static function getScripts()
    {
        return ArrayHelper::flatten(static::$scripts);
    }

    /**
     * @return array
     */
    public static function getStyles()
    {
        return ArrayHelper::flatten(static::$styles);
    }

    /**
     * @return array
     */
    public static function getRawScripts()
    {
        return ArrayHelper::flatten(static::$raw_scripts);
    }

    /**
     * @param string $path
     * @return string
     */
    private static function sanitizePath($path)
    {
        return '/' . ltrim($path, '/');
    }
}
