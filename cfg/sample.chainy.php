<?php

/**
 * Copyright 2016 Everex https://everex.io
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
