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
     * @param string $path
     * @return string
     */
    private static function sanitizePath($path)
    {
        return '/' . ltrim($path, '/');
    }
}
