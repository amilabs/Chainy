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
 * Copy this file to config.chainy.php before making any changes.
 */

$aConfig['path']['app'] = rtrim(realpath(dirname(__FILE__) . '/../app'), '/');

$aConfig += array(
    'Router' => array(
        'aRoutes' => array(
            ''               => array(),
            ':code'          => array('default' => array('byHash' => FALSE)),
            'i/:code'        => array('default' => array('byHash' => FALSE, 'noRedirect' => TRUE)),
            'add'            => array('default' => array('action' => 'add')),
            'tx/:hash'       => array('default' => array('code' => FALSE, 'byHash' => TRUE)),
            'getShort/:hash' => array('default' => array('action' => 'short'))
        )
    ),
    'link404'         => '/404',
    'service'         => "http://localhost:8344", // Where bin/service.js is up
    'autopublish'     => FALSE,
    'sender'          => '0x...',
    'contractAddress' => '0x...'
);
