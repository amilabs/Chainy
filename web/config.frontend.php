<?php
/**
 * Sample configuration file for frontend application working with service backend.
 *
 * Copy tis file to config.frontend.local.php before making any changes.
 */

if(!defined('AMILABS')) die;

$aConfig += array(
    'request' => array(
        'type' => '\\AmiLabs\\Chainy\\Frontend\\RequestCHAINY'
    )
);

// Add RPC settings
if(file_exists('config.service.local.php')){
    require_once 'config.service.local.php';
}