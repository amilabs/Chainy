<?php
/**
 * Sample configuration file for HTTP service working with mainnet blockchain node.
 *
 * Copy tis file to config.service.local.php before making any changes.
 */

if(!defined('AMILABS')) die;

$aConfig += array(
    
    // Blockchain settings
    'Blockchain' => array(
        // Using Mainnet
        'testnet' => false
    ),
    
    // RPC services configuration
    'RPCServices' => array(
        // Server address and port for "counterblockd" service
        'counterblockd' => array(
            'driver'  => 'json',
            'address' => 'http://localhost:4100/api/'
        ),
        // Server address and access data for "bitcoind" service
        'bitcoind' => array(
            'driver'  => 'json',
            'address' => 'http://localhost:4332/',
            'login' => 'user',
            'password' => 'password'
        )
    )
);