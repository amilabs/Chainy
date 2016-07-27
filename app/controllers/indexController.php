<?php

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
            $url = $oRequest->get('url', FALSE, INPUT_POST);
            $type = $oRequest->get('addType', FALSE, INPUT_POST);
            $tx = FALSE;
            switch($type){
                case 'filehash':
                    $tx = TX::createHashLinkTransaction($url);
                    break;
                case 'redirect':
                    $tx = TX::createRedirectTransaction($url);
                    break;
            }
            if($tx){
                echo 'Transaction Hash: ' . $tx;
                die();
            }
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
        $pos = TX::getPositionInBlockByTransaction($txNo);
        if(!is_null($pos['block'])){
            $short = TX::encodeBase58($pos['block'] . str_pad($pos['position'], 4, '0', STR_PAD_LEFT));
            echo '<a href="http://txn.me/' . $short . '">' .  $short . '</a>';
        }else{
            echo "Transaction was not included in a block yet.";
        }
        die();
    }
    /**
     * Not found.
     */
    protected function notFound(){
        header('Location: http://chainy.info/err/404', TRUE, 301);
        die();
    }
}
