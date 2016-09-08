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
        session_start();
        if(isset($_SESSION['add_result'])){
            $result = $_SESSION['add_result'];
            session_unset();
            $this->getView()->set('success', $result['success']);
            $this->getView()->set('message', $result['message']);
            if(isset($result['hash'])){
                $this->getView()->set('hash', $result['hash']);
            }
            if(isset($result['data'])){
                $this->getView()->set('chainyJSON', json_encode($result['data'], JSON_UNESCAPED_SLASHES));
            }
            if(isset($result['transaction'])){
                $this->getView()->set('chainyTransaction', json_encode($result['transaction']));
            }
        }
        if($oRequest->getMethod() === 'POST'){
            $type = $oRequest->get('addType', FALSE, INPUT_POST);
            $url = $oRequest->get('url', FALSE, INPUT_POST);
            $description = $oRequest->get('description', "", INPUT_POST);
            switch($type){
                case 'filehash':
                    $result = TX::createHashLinkTransaction($url, $description ? $description : FALSE);
                    break;
                case 'redirect':
                    $result = TX::createRedirectTransaction($url);
                    break;
                default:
                    $result = array('error' => 'Invalid operation');
            }
            $success = $result && is_array($result) && !isset($result['error']);
            $message = ($success) ? (ucfirst($type) . ' JSON:') : ('ERROR: Unable to add ' . $type . ($tx && is_array($tx) && isset($tx['error']) ? ' (' . $tx['error'] . ')' : ''));
            $result['success'] = $success;
            $result['message'] = $message;
            $_SESSION['add_result'] = $result;
            header('Location: /add');
            die();
        }
        session_write_close();
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
