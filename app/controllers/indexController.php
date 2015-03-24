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
            if($url = $oRequest->get('url', false, INPUT_POST)){
                TX::createHashLinkTransaction($url);
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

        if(TX::isChainyTransaction($txNo)){
            $aTransaction += TX::decodeChainyTransaction($txNo);
            $this->oView->set('aTX', $aTransaction);
        }else{
            $this->notFound();
        }
    }

    protected function notFound(){
        header('Location: http://chainy.info/err/404');
        die();        
    }
}