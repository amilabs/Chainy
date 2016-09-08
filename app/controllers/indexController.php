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
            $result = TX::decodeChainyTransaction($code);
        }else{
            // by TX Hash
        }
        if(isset($result) && is_array($result)){
            if($result['type'] == TX::TX_TYPE_HASHLINK){
                $result['filesize'] = TX::getFileSize($result['filesize']);
                switch($result['filetype']){
                    case TX::FILE_TYPE_PDF:
                        $result['filetype'] = 'pdf';
                        break;
                    case TX::FILE_TYPE_ARCHIVE:
                        $result['filetype'] = 'archive';
                        break;
                    case TX::FILE_TYPE_TEXT:
                        $result['filetype'] = 'text';
                        break;
                    case TX::FILE_TYPE_IMAGE:
                        $result['filetype'] = 'image';
                        break;
                    default:
                        $result['filetype'] = '';
                }
                $result['block']    = 'Unknown';
                $result['tx']       = 'Unknown';
                $result['date']     = 'Unknown';

                $this->oView->set('aTX', $result);
            }
            if($result['type'] == TX::TX_TYPE_REDIRECT){
                header('Location:' . $result['url']);
                die();
            }
        }else{
            $oLogger->log('ERROR: Code ' . $code . ' not found (404), no corresponding transaction.');
            $this->notFound();
        }

        /*
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
         */
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
                case 'Local file hash':
                    $filename = $oRequest->get('filename', FALSE, INPUT_POST);
                    $filesize = $oRequest->get('filesize', FALSE, INPUT_POST);
                    $hash     = $oRequest->get('hash', FALSE, INPUT_POST);
                    $result = TX::createLocalFileHashLinkTransaction($filename, $filesize, $hash, $description ? $description : FALSE);
                    break;
                case 'File hash':
                    $result = TX::createHashLinkTransaction($url, $description ? $description : FALSE);
                    break;
                case 'Redirect':
                    $result = TX::createRedirectTransaction($url);
                    break;
                case 'Text':
                    $result = TX::createTextTransaction($description);
                    break;
                case 'Hash':
                    $result = TX::createHashTransaction($description);
                    break;
                case 'Encrypted Text':
                    $encrypted  = $oRequest->get('encrypted', FALSE, INPUT_POST);
                    $hash       = $oRequest->get('hash', FALSE, INPUT_POST);
                    $result     = TX::createEncryptedTextTransaction($encrypted, $hash);
                    break;
                default:
                    $result = array('error' => 'Invalid operation');
            }
            $success = $result && is_array($result) && !isset($result['error']);
            if($success){
                $tx = TX::publishData($result['data']);
                if(is_array($tx)){
                    $result += $tx;
                }
            }
            $message = ($success) ? (ucfirst($type) . ' JSON:') : ('ERROR: Unable to add ' . $type . ($result && is_array($result) && isset($result['error']) ? ' (' . $result['error'] . ')' : ''));
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
