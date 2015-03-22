<?php

use \AmiLabs\DevKit\Controller;
use \AmiLabs\DevKit\RPC;
use \AmiLabs\Chainy\Frontend\TX;

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
        
        //$aPos = $this->getBlockPositionByTransaction('a3c66f98eea1f34b81fe4e30215cb04c9c9810207f4119b4154c2fc83e735c9b');
        //$addr = TX::encodeBase58((int)$aPos['block'] * 10000 + $aPos['position']);
        //var_dump($addr);
        //die();

        $code  = $oRequest->getCallParameters(0);

        if($code == 'add'){
            if($url = $oRequest->get('url', false, INPUT_POST)){
                TX::createHashLinkTransaction($url);
                die();
            }
            $this->templateFile = 'index/add';
            return;
        }

        $strPos = TX::decodeBase58($code);
        if($strPos < 3000000000){
            die('Not found');
            $this->notFound();
        }
        
        $block = (int)substr($strPos, 0, 6);
        $position = (int)substr($strPos, 6);
        $txNo = TX::getTransactionByBlockPosition($block, $position);
        
        $aFileData = array(
            'b66904081cef5cadfd693715e2d0bd622a5726aa410217b9f26bd5bdd92e2231' => array('image', '23KB'),
            '9da063f7aad545b97e3ad5a5b12fdadbe8a7f2ebd39c437cd8a56541a4d8049b' => array('image', '27KB'),
            '8f7c4607cdf8867ca04f13de2585cb362b01354d7a05ce8ecbb94060d4d65c6e' => array('archive', '4KB'),
            'a3c66f98eea1f34b81fe4e30215cb04c9c9810207f4119b4154c2fc83e735c9b' => array('pdf', '434KB')
        );

        list($fileFmt, $fileSize) = isset($aFileData[$txNo]) ? $aFileData[$txNo] : array('archive', '0KB');
        
        $aTransaction = array(
            'tx'    => $txNo,
            'block' => $block,
            'fmt'   => $fileFmt,
            'size'  => $fileSize
        );

        if(TX::isChainyTransaction($txNo)){
            $aTransaction += TX::decodeChainyTransaction($txNo);
            $this->oView->set('aTX', $aTransaction);
        }else{
            die('Not chainy');
            //$this->notFound();
        }
    }

    protected function notFound(){
        header('Location: http://chainy.info/err/404');
        die();        
    }
}