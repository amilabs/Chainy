<?php
/**
 * Sample configuration file for Chainy application.
 *
 * Copy this file to config.local.php before making any changes.
 */

$aConfig['path']['app'] = rtrim(realpath(dirname(__FILE__) . '/../app'), '/');

$aConfig += array(
    'Router' => array(
        'aRoutes' => array(
            ''               => array(),
            ':code'          => array('default' => array('byHash' => FALSE)),
            'add'            => array('default' => array('action' => 'add')),
            'tx/:hash'       => array('default' => array('code' => FALSE, 'byHash' => TRUE)),
            'getShort/:hash' => array('default' => array('action' => 'short'))
        )
    ),
    // CryptoKit configuration
    'CryptoKit' => array(
        'layer' => 'Counterparty',
        'testnet' => FALSE,
        'RPC' => array(
            'services' => array(
                array(
                    // Server address and port for "Counterblock" service
                    'counterblockd' => array(
                        'address' => 'login:password@node.address:4100',
                    ),
                    // Server address and port for "Counterparty" service
                    'counterpartyd' => array(
                        'address' => 'login:password@node.address:4000',
                    ),
                    // Server address and access data for "Bitcoind" service
                    'bitcoind'      => array(
                        'address' => 'login:password@node.address:4332',
                    )
                ),
                // May contain several nodes
            )
        )
    ),
    // Chainy transaction markers
    // 434841494e59 - CHAINY
    // 444556434841 - DEVCHA
    'marker' => '444556434841',
    'markers' => array('434841494e59', '444556434841'),
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
