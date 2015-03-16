<?php

use \AmiLabs\DevKit\Controller;
use \AmiLabs\DevKit\RPC;
use \AmiLabs\DevKit\TX;

class indexController extends Controller {
    /**
     * Transaction type marker
     *
     * @var string
     */
    protected $marker = '444556434841';
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
        $strPos = TX::decodeBase58($code);
        if($strPos < 3000000000){
            $this->notFound();
        }
        
        $block = (int)substr($strPos, 0, 6);
        $position = (int)substr($strPos, 6);
        $txNo = $this->getTransactionByBlockPosition($block, $position);
        
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

        if($this->isChainyTransaction($txNo)){
            $aTransaction += $this->decodeChainyTransaction($txNo);
            $this->oView->set('aTX', $aTransaction);
        }else{
            $this->notFound();
        }
    }

    protected function isChainyTransaction($tx){
        $oRPC = new RPC();
        $result = $oRPC->execBitcoind('getrawtransaction', array($tx), false, true);
        return (strlen($result) && (strpos($result, $this->marker) !== false));
    }


    protected function notFound(){
        header('Location: http://chainy.info/err/404');
        die();        
    }
    
    
    protected function decodeChainyTransaction($tx){
        $aTX = array();
        $oRPC = new RPC();
        $data = $oRPC->execBitcoind('getrawtransaction', array($tx), false, true);

        // 1. Get OP_RETURN data
        $opData = TX::getDecodedOpReturn($data, true);
        $aTX['hash'] = substr($opData, 16);
        $fileType = substr($opData, 15, 1);
        //die();
        /*
        $aTX = TX::decodeTransaction($result);
        foreach($aTX['vout'] as $aOut){
            if(strpos($aOut['scriptPubKey'], '5121') === 0){
            }
        }
         * 
         */
        return $aTX;
        
    }

    protected function getBlockPositionByTransaction($txHash){
        $block = null;
        $txPosition = null;
        $oRPC = new RPC();

        if(!$txHash){
            // todo
        }

        try{
            $aResult = $oRPC->execBitcoind(
                'getrawtransaction',
                array(
                    $txHash,
                    1
                ),
                false,
                true
            );
            if(is_array($aResult)){
                $blockHash = $aResult['blockhash'];
                try{
                    $aResult = $oRPC->execBitcoind(
                        'getblock',
                        array($blockHash),
                        false,
                        true
                    );
                    if(is_array($aResult)){
                        $block = $aResult['height'];
                        $aTx = $aResult['tx'];
                        sort($aTx);
                        $key = array_search($txHash, $aTx);
                        if($key !== false){
                            $txPosition = $key;
                        }
                    }else{
                        // todo
                    }
                }catch(\Exception $e){
                    // todo
                }
            }else{
                // todo
            }
        }catch(\Exception $e){
            // todo
        }

        return array(
            'block' => $block,
            'position' => $txPosition
        );
    }

    protected function getTransactionByBlockPosition($block, $position){
        $txHash = null;
        $oRPC = new RPC();

        if(!$block || !$position){
            // todo
        }

        try{
            $aResult = $oRPC->execCounterpartyd(
                'get_block_info',
                array(
                    'block_index' => $block
                ),
                true
            );
            if(is_array($aResult)){
                $blockHash = $aResult['block_hash'];
                try{
                    $aResult = $oRPC->execBitcoind(
                        'getblock',
                        array($blockHash),
                        false,
                        true
                    );
                    if(is_array($aResult)){
                        $aTx = $aResult['tx'];
                        sort($aTx);
                        if(isset($aTx[$position])){
                            $txHash = $aTx[$position];
                        }
                    }else{
                        // todo
                    }
                }catch(\Exception $e){
                    // todo
                }
            }else{
                // todo
            }
        }catch(\Exception $e){
            // todo
        }

        return $txHash;
    }

}