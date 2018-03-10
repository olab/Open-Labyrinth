<?php

defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------
// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

$configPath = DOCROOT . 'config.json';
if (!file_exists($configPath)) {
    throw new \ErrorException('File of configuration (' . $configPath . ') not found.');
}
$config = file_get_contents($configPath);
$config = json_decode($config, true);

/**
 * Set precision for PHP floating points
 */
ini_set('precision', '16');

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set($config['timezone']);

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, $config['locale']);

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang($config['lang']);

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
    'base_url' => $config['base_url'],
    'errors' => true
));

Cookie::$path = Kohana::$base_url;

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
$modules = array(
    'auth' => MODPATH . 'auth', // Basic authentication
    'cache' => MODPATH . 'cache', // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench', // Benchmarking tool
    'database' => MODPATH . 'database', // Database access
    // 'image'      => MODPATH.'image', // Image manipulation
    'acl' => MODPATH . 'acl', // Access control module
    'orm' => MODPATH . 'orm', // Object Relationship Mapping
    // 'unittest'   => MODPATH.'unittest', // Unit testing
    // 'userguide'  => MODPATH.'userguide', // User guide and API documentation
    'leap' => MODPATH . 'leap', // Include Leap ORM
    'breadcrumbs' => MODPATH . 'breadcrumbs', // Breadcrumbs
    'restful' => MODPATH . 'restful', // RESTful interface
    'oauth' => MODPATH . 'oauth', // OAuth module
    'phpexcel' => MODPATH . 'phpexcel',
    'kohana-media' => MODPATH . 'kohana-media',
    'TinCanPHP' => MODPATH . 'TinCanPHP', // https://github.com/RusticiSoftware/TinCanPHP
    'h5p-php-library' => MODPATH . 'h5p-php-library', // https://github.com/h5p/h5p-php-library
    'h5p-editor-php-library' => MODPATH . 'h5p-editor-php-library', // https://github.com/h5p/h5p-editor-php-library
);

Kohana::modules($modules);
$mods = Model_Leap_Vocabulary_Vocablet::getEnabled();

$mods = array_merge($modules, $mods);


/**
 * Load Composer packages from vendor folder
 */
$vendorAutoload = DOCROOT . '../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}


/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */


Route::set('sparql', '<directory>(/<controller>(/<action>(/<id>)))',
    array(
        'directory' => '(sparql/api|sparql)'
    ))
    ->defaults(array(
        'controller' => 'endpoint',
        'action' => 'index',
    ));

Route::set('metadata', '<directory>(/<controller>(/<action>(/<id>)))',
    array(
        'directory' => '(metadata/api|metadata)'
    ))
    ->defaults(array(
        'controller' => 'manager',
        'action' => 'index',
    ));

Route::set('rdf', '<controller>/<type>/<id>',
    array(
        'controller' => 'resource|data'
    )
);

Route::set('vocabulary_mappings', '<directory>(/<controller>(/<action>))',
    array(
        'directory' => 'vocabulary/mappings'
    ))
    ->defaults(array(
        'controller' => 'manager',
        'action' => 'index',
    ));
Route::set('vocabulary_vocablets', '<directory>(/<controller>(/<action>))',
    array(
        'directory' => 'vocabulary/vocablets'
    ))
    ->defaults(
        array(
            'controller' => 'manager',
            'action' => 'index',
        ));
Route::set('vocabulary_inline', '<directory>(/<controller>(/<action>))',
    array(
        'directory' => 'vocabulary/inline'
    ))
    ->defaults(
        array(
            'controller' => 'manager',
            'action' => 'index',
        ));
Route::set('vocabulary_inline_entities', '<directory>(/<controller>(/<action>))',
    array(
        'directory' => 'vocabulary/inline/entities'
    ))
    ->defaults(
        array(
            'controller' => 'manager',
            'action' => 'index',
        ));

Route::set('vocabulary', '<directory>(/<controller>(/<action>))',
    array(
        'directory' => 'vocabulary'
    ))
    ->defaults(
        array(
            'controller' => 'manager',
            'action' => 'index',
        ));


Kohana::modules($mods);
Route::set('default', '(<controller>(/<action>(/<id>)(/<id2>)(/<id3>)(/<id4>)(/<id5>)(/<id6>)))')
    ->defaults(array(
        'controller' => 'home',
        'action' => 'index',
    ));

Route::set('error', 'error/<action>(/<message>)', array('action' => '[0-9]++', 'message' => '.+'))
    ->defaults(array(
        'controller' => 'error'
    ));

require_once './application/helpers.php';