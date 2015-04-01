<?php

use \AmiLabs\DevKit\Controller;
use \AmiLabs\Chainy\TX;

class indexController extends Controller {
    /**
     * Transaction type marker
     *
     * @var string
     */
    protected $marker = '444556434841'; // DEVCHA
    /**
     * Index action prototype.
     *
     * @param \AmiLabs\DevKit\Application   $oApp      Application object
     * @param \AmiLabs\DevKit\RequestDriver $oRequest  Request object
     * @return \AmiLabs\DevKit\Controller
     */
    public function actionIndex($oApp, $oRequest){
      
        $code  = $oRequest->getCallParameters(0);

        if($code == 'add'){
            $url = $oRequest->get('url', false, INPUT_POST);
            $type = $oRequest->get('addType', false, INPUT_POST);
            $tx = false;
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
            $this->templateFile = 'index/add';
            return;
        }
        $byHash = false;
        if($code == 'tx'){
            $txNo = $oRequest->getCallParameters(1);
            $aBlockData = TX::getPositionInBlockByTransaction($txNo);
            $block = $aBlockData['block'];
            $byHash = true;
        }
        if($code == 'getshort'){
            $txNo = $oRequest->getCallParameters(1);
            $pos = TX::getPositionInBlockByTransaction($txNo);
            if(!is_null($pos['block'])){
                echo TX::encodeBase58($pos['block'] . str_pad($pos['position'], 4, '0', STR_PAD_LEFT));
            }else{
                echo "Transaction was not included in a block yet.";
            }
            die();
        }
        if(!$byHash){
            $strPos = TX::decodeBase58($code);
            if($strPos < 3000000000){
                $this->notFound();
            }
            $block = (int)substr($strPos, 0, 6);
            $position = (int)substr($strPos, 6);
            $txNo = TX::getTransactionByPositionInBlock($block, $position);
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
            $this->notFound();
        }
    }

    protected function notFound(){
        header('Location: http://chainy.info/err/404');
        die();        
    }
}
