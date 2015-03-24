<?php
/**
 * Sample configuration file for Chainy application.
 *
 * Copy this file to config.local.php before making any changes.
 */

if(!defined('AMILABS')) die;

$aConfig += array(
    'request' => array(
        'type' => '\\AmiLabs\\Chainy\\RequestCHAINY'
    ),
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
    ),
    'addresses' => array(
        'source' => array(
            'address'   => 'SOURCE_ADDRESS',
            'pubkey'    => 'PUBLIC_KEY',
            'privkey'   => 'PRIVATE_KEY'
        ),
        'destination' => array(
            'address'   => 'DESTINATION_ADDRESS'
        )
    )
);
