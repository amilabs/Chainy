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

use AmiLabs\DevKit\Controller;
use AmiLabs\DevKit\Logger;
use AmiLabs\Chainy\TX;

class indexController extends Controller {

    public function __construct(){
        parent::__construct();
        $this->layoutName = 'new';
    }

    /**
     * Index action.
     *
     * @param  array $aParameters  Application parameters
     * @return \AmiLabs\DevKit\Controller
     */
    public function actionIndex(array $aParameters){
        set_time_limit(0);
        $this->layoutName = 'view';
        $oLogger = Logger::get('access-chainy');
        $byHash = isset($aParameters['byHash']) ? $aParameters['byHash'] : FALSE;
        if($byHash){
            $link = TX::getChainyLink($aParameters['hash']);
            $code = substr($link, strrpos($link, '/') + 1);
        }else{
            $code = isset($aParameters['code']) ? $aParameters['code'] : "";
        }
        $result = FALSE;
        if(strlen($code)){
            $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Unknown';
            $oLogger->log('Code:' . $code . ', IP:' . $ipAddress . ', Referer:' . $referer);
            $result = TX::decodeChainyTransaction($code);
        }
        if(is_array($result) && isset($result['type'])){
            $this->oView->set('title', $code . ' details', true);
            switch($result['type']){
                case TX::TX_TYPE_REDIRECT:
                    if(!isset($aParameters['noRedirect'])){
                        header('Location:' . $result['url']);
                        die();
                    }
                case TX::TX_TYPE_HASHLINK:
                case TX::TX_TYPE_TEXT:
                case TX::TX_TYPE_HASH:
                case TX::TX_TYPE_ENCRYPTED:
                    $this->oView->set('aTX', $result);
                    break;
            }
            return;
        }
        $oLogger->log('ERROR: Code ' . $code . ' not found (404), no corresponding transaction.');
        $this->action404($aParameters);
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
        $this->oView->set('title', 'add', true);
        session_start();
        $this->getView()->set('contractAddress', $this->getConfig()->get('contractAddress'));
        if(isset($_SESSION['add_result'])){
            $result = $_SESSION['add_result'];
            session_unset();
            $this->getView()->set('success', $result['success']);
            $this->getView()->set('message', $result['message']);
            if(isset($result['hash']) && !$result['mist']){
                $this->getView()->set('hash', $result['hash']);
            }
            if(isset($result['data'])){
                $this->getView()->set('chainyJSON', json_encode($result['data'], JSON_UNESCAPED_SLASHES));
            }
            if(isset($result['transaction']) && !$result['mist']){
                $this->getView()->set('chainyTransaction', json_encode($result['transaction']));
            }
        }
        if($oRequest->getMethod() === 'POST'){
            if($this->getConfig()->get('captcha', FALSE)){
                // Check Captcha
                $recaptcha = $oRequest->get('g-recaptcha-response', FALSE, INPUT_POST);
                $url = "https://www.google.com/recaptcha/api/siteverify";
                $res = $this->_post($url, array(
                    'secret' => $this->getConfig()->get('captchaSecret', ""),
                    'response' => $recaptcha
                ));
                if(!is_array($res) || !isset($res['success']) || !$res['success']){
                    $_SESSION['add_result'] = array(
                        'success' => false,
                        'message' => "Invalid captcha"
                    );
                    header('Location: ?');
                    die();
                }
            }
            $publish = $oRequest->get('publish', FALSE, INPUT_POST);
            $sender = $oRequest->get('sender', FALSE, INPUT_POST);
            if(FALSE !== $sender){
               TX::setDefaultSender($sender);
            }
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
            $result['mist'] = $oRequest->get('mist', FALSE, INPUT_POST);
            $success = $result && is_array($result) && !isset($result['error']);
            if($success && !$result['mist']){
                $oCfg = $this->getConfig();
                if($oCfg->get('autopublish', FALSE) && $publish){
                    $strData = json_encode($result['data'], JSON_UNESCAPED_SLASHES);
                    $limit = $oCfg->get('maxJsonSize', 4700) - 200;
                    if(strlen($strData) > $limit){
                        // @todo: limits to config
                        $success = false;
                        $result = array('error' => 'Data is too big to publish (' . $limit . ' bytes maximum allowed)');
                    }else{
                        $tx = TX::publishData($result['data']);
                        if(is_array($tx)){
                            unset($result['data']);
                            $result += $tx;
                        }
                    }
                }
            }
            $message = ($success) ? (ucfirst($type) . ' JSON:') : ('ERROR: Unable to add ' . $type . ($result && is_array($result) && isset($result['error']) ? ' (' . $result['error'] . ')' : ''));
            $result['success'] = $success;
            $result['message'] = $message;
            $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Unknown';
            $oLogger = Logger::get('add-chainy');
            $oLogger->log('CREATE: ' . var_export($result, TRUE) . ' (IP=' . $ipAddress . ',referer=' . $referer . ')');
            $_SESSION['add_result'] = $result;
            header('Location: ?');
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
        $result = TX::getChainyLink($aParameters['hash']);
        echo $result ? $result : '';
        die();
    }

    /**
     * getShort action.
     *
     * @param  array $aParameters  Application parameters
     * @return \AmiLabs\DevKit\Controller
     */
    public function action404(array $aParameters){
        $this->oView->set('title', '404 - Not Found', true);
        $this->templateFile = 'index/404';
    }

    /**
     * JSON RPC request implementation.
     *
     * @param string $method  Method name
     * @param array $params   Parameters
     * @return array
     */
    protected function _post($url, $params = array()){
        $result = false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        if($result && $result[0] == '{'){
            $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
        }
        curl_close($ch);
        return $result;
    }
}
