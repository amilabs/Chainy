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

use \AmiLabs\DevKit\Controller;
use \AmiLabs\DevKit\Logger;
use \AmiLabs\Chainy\TX;

class indexController extends Controller {
    /**
     * Index action.
     *
     * @param  array $aParameters  Application parameters
     * @return \AmiLabs\DevKit\Controller
     */
    public function actionIndex(array $aParameters){
        set_time_limit(0);
        $oLogger = Logger::get('access-chainy');
        $byHash = $aParameters['byHash'];
        if(!$byHash){
            // By Code
            $code = $aParameters['code'];
            if(strlen($code) >= 5){
                $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Unknown';
                $oLogger->log('Code:' . $code . ', IP:' . $ipAddress . ', Referer:' . $referer);
            }
            $strPos = sprintf("%0.0f", TX::decodeBase58(substr($code, 0, 6)));
            if($strPos < 3000000000){
                $oLogger->log('ERROR: Code ' . $code . ' not found (404), cannot decode Base58.');
                $this->notFound();
            }
            $block = (int)substr($strPos, 0, 6);
            $position = (int)substr($strPos, 6);
            $txNo = TX::getTransactionByPositionInBlock($block, $position);
        }else{
            // by TX Hash
            $txNo = $aParameters['hash'];
            $aBlockData = TX::getPositionInBlockByTransaction($txNo);
            $block = $aBlockData['block'];
        }
        $blockDate = TX::getBlockDate($block);
        $aTransaction = array(
            'tx'    => $txNo,
            'block' => $block,
            'date'  => $blockDate
        );
        if($txNo && TX::isChainyTransaction($txNo)){
            $aTransaction += TX::decodeChainyTransaction($txNo);
            if($aTransaction['type'] == TX::TX_TYPE_HASHLINK){
                $this->oView->set('aTX', $aTransaction);
            }
            if($aTransaction['type'] == TX::TX_TYPE_REDIRECT){
                header('Location:' . $aTransaction['link']);
                die();
            }
        }else{
            $oLogger->log('ERROR: Code ' . $code . ' not found (404), no corresponding transaction.');
            $this->notFound();
        }
    }
    /**
     * Add action.
     *
     * @param  array $aParameters  Application parameters
     * @return \AmiLabs\DevKit\Controller
     */
    public function actionAdd(array $aParameters){
        set_time_limit(0);
        $oRequest = $this->getRequest();
        if($oRequest->getMethod() === 'POST'){
            $type = $oRequest->get('addType', FALSE, INPUT_POST);
            $url = $oRequest->get('url', FALSE, INPUT_POST);
            switch($type){
                case 'filehash':
                    $tx = TX::createHashLinkTransaction($url);
                    break;
                case 'redirect':
                    $tx = TX::createRedirectTransaction($url);
                    break;
                default:
                    $tx = array();
            }
            $success = $tx && is_array($tx) && !isset($tx['error']);
            $this->getView()->set('success', $success);
            if($success){
                $message =  ucfirst($type) . ' added';
                if(isset($tx['hash'])){
                    $this->getView()->set('hash', $tx['hash']);
                }
                if(isset($tx['data'])){
                    $this->getView()->set('chainyJSON', json_encode($tx['data']));
                }
                if(isset($tx['transaction'])){
                    $this->getView()->set('chainyTransaction', json_encode($tx['transaction']));
                }
            }else{
                $message = 'ERROR: Unable to add ' . $type . ($tx && is_array($tx) && isset($tx['error']) ? ' (' . $tx['error'] . ')' : '');
            }
            $this->getView()->set('message', $message);
        }
    }

    /**
     * getShort action.
     *
     * @param  array $aParameters  Application parameters
     * @return \AmiLabs\DevKit\Controller
     */
    public function actionShort(array $aParameters){
        set_time_limit(0);
        $txNo = $aParameters['hash'];
        $result = TX::getTransactionCode($aParameters['hash']);
        echo $result ? $result : '';
        die();
    }
    /**
     * Not found.
     */
    protected function notFound(){
        // header('Location: http://chainy.info/err/404', TRUE, 301);
        die(404);
    }
}
