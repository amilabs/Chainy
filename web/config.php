<?php
/**
 * Project configuration.
 * 
 * @todo Documentation how to make local config file.
 */
use AmiLabs\DevKit\Registry;

// Project flag used to check if every php file runs in proper environment.
define('AMILABS', true);

// Default project configuration.
$aConfig = array(
    'path' => array(
        // Document root
        'www' => dirname(__FILE__),
        // Libraries
        'lib' => realpath(dirname(__FILE__) . '/../vendor'),
        // Application classes
        'app' => realpath(dirname(__FILE__) . '/../app' . (defined('APP_NAME') ? '/' . APP_NAME : '')),
        // Temporary files
        'tmp' => realpath(dirname(__FILE__) . '/../tmp'),
        // Logs and other debug files
        'log' => realpath(dirname(__FILE__) . '/../log'),
    )
);

// Check if local application config file is present. Will use default config if not.
$cfgFile = (defined('APP_NAME')) ? (APP_NAME . '.') : '';
if(file_exists('config.' . $cfgFile . 'local.php')){
    require_once 'config.' . $cfgFile . 'local.php';
}elseif($cfgFile && file_exists('config.' . $cfgFile . 'php')){
    require_once 'config.' . $cfgFile . 'php';
}

// Set path constants.
define('PATH_WWW', $aConfig['path']['www']);
define('PATH_LIB', $aConfig['path']['lib']);
define('PATH_APP', $aConfig['path']['app']);
define('PATH_TMP', $aConfig['path']['tmp']);
define('PATH_LOG', $aConfig['path']['log']);

require_once PATH_LIB . '/autoload.php';

// Initialize environment registry
Registry::addStorage('ENV');

// Store configuration to registry
Registry::addStorage('CFG')->set(Registry::ROOT, $aConfig, Registry::PERSIST);
unset($aConfig);
