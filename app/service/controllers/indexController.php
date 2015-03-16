<?php

use \AmiLabs\DevKit\Controller;
use \AmiLabs\DevKit\RPC;
use \AmiLabs\DevKit\TX;

class indexController extends Controller {
    /**
     * Test service action.
     *
     * @param \AmiLabs\DevKit\Application $oApp        Application object
     * @param \AmiLabs\DevKit\RequestDriver $oRequest  Request driver
     */
    public function actionTest($oApp, $oRequest){
        $this->getView()->set('message', 'Hello World!');
        $this->getView()->set('data',  $oRequest->getCallParameters());
    }
    /**
     * Send coins.
     *
     * @param \AmiLabs\DevKit\Application $oApp        Application object
     * @param \AmiLabs\DevKit\RequestDriver $oRequest  Request driver
     */
    public function actionSend($oApp, $oRequest){

        $oRPC = new RPC();
/*
        $source         = $oRequest->getCallParameters('source', '1HBu84JwPoqdqSk8K723XpQB68Ei5JjdBX');
        $destination    = $oRequest->getCallParameters('destination', '1ET6Ty8BTjtR5PompP2HTQKGBg7KkYgSw3');
        $asset          = $oRequest->getCallParameters('asset', 'BTC');
        $publicKey      = $oRequest->getCallParameters('publicKey', '02d7c64e9c3f69404b7d9bf8ff3980dc28928c6fd118be342a610f280a1bb43f4b');
        $quantity       = (real)$oRequest->getCallParameters('quantity', 7800);

        if(!$source || !$destination || !$publicKey){
            // todo
        }

        try{
            $result = $oRPC->execCounterpartyd(
                'create_send',
                array(
                    "asset"                     => $asset,
                    "source"                    => $source,
                    "destination"               => $destination,
                    "quantity"                  => $quantity,
                    "allow_unconfirmed_inputs"  => true,
                    "encoding"                  => "multisig",
                    "pubkey"                    => array($publicKey)
                ),
                true
            );
            if(!is_null($result)){
                $oTx = new TX();               
                //$string = 'HELLO BLOCKCHAIN, I AM ENCODED BEAUTIFUL STRING!!!';
                //$result = $oTx->addMultisigDataOutput($result, $string, $publicKey);
                //$result = $oTx->addOpReturnOutput($result, chr(32) . 'DEVCHA' . chr(1) . pack('H*', hash('sha256', $string)));
                $file = 'test';
                $hash = pack('H*', '8a1f7f7c201ff55fc99a98bb67502e61866c2bdf1ecb06fc1ccb722a45f33c3a');
                $filesize = 0;
                $result = $oTx->addMultisigDataOutput($result, str_pad(pack('H*', dechex($filesize)), 4, chr(0), STR_PAD_LEFT) . $file, $publicKey);
                $result = $oTx->addOpReturnOutput($result, chr(64) . 'DEVCHA' . chr(4) . $hash);
                $this->getView()->set('result', $result);
            }else{
                // todo
            }
        }catch(\Exception $e){
            // todo
        }
  */

        
        try{
            $result = $oRPC->execCounterpartyd(
                'create_order',
                array(
                    "expiration"                => 1000,
                    "fee_provided"              => 10000,
                    "fee_required"              => 0,
                    "give_asset"                => "OAZT",
                    "get_asset"                 => "BTC",
                    "source"                    => "1CcLeZmyZWMhW3mBrJumPNiyDjNLfqJQVe",
                    "get_quantity"              => 10000,
                    "give_quantity"             => 1,
                    "allow_unconfirmed_inputs"  => true,
                    "encoding"                  => "multisig",
                    "pubkey"                    => array("0319e01f867964d97a02cf926529506410b1bb2bbcc69630a3fb8d1b49a47af42e")
                ),
                true
            );
            if(!is_null($result)){
                $this->getView()->set('result', $result);
            }else{
                // todo
            }
        }catch(\Exception $e){
            // todo
        }        
   
    }

    public function actionSsend($oApp, $oRequest){

        $oRPC = new RPC();

        try{
            $result = $oRPC->execCounterpartyd(
                'create_send',
                array(
                    "asset"                     => 'OAZT',
                    "source"                    => '2_1BCHVoMDKQ6ff7MKrnojNVfSiTq4F9JZ2p_1EpqDHoaBMvQ8VXQVhCnW8XTisVJ3hUSXQ_2',
                    "destination"               => '1BCHVoMDKQ6ff7MKrnojNVfSiTq4F9JZ2p',
                    "quantity"                  => 10000000,
                    "allow_unconfirmed_inputs"  => true,
                    "encoding"                  => "multisig",
                    "pubkey"                    => array('036dac44398b60cad2042a7fc12be05b0917bbfeac3d9cf2cd74bb4745959d1017', '03a3b5ab6e837a02be8249fae4cf0b68e8c54eb3b2083314be67d1bd27d84401dc')
                ),
                true
            );
            if(!is_null($result)){
                $result = $oRPC->execBitcoind(
                    'signrawtransaction',
                    array($result, array(), array('L2AQvHtUzTHTDS3r8sUd3ss1VsKwxhgBAj64UHEqWN9vrYutFYK1', 'KxEHczTg3zXmdzyfcU3nquVaGLMQDfLqJ7WKLRkrHSa9WdA2G2Dc')),
                    true
                );
                $this->getView()->set('result', $result);
            }else{
                // todo
            }
        }catch(\Exception $e){
            // todo
        }        
   
    }

    /**
     * Broadcast signed transaction.
     *
     * @param \AmiLabs\DevKit\Application $oApp        Application object
     * @param \AmiLabs\DevKit\RequestDriver $oRequest  Request driver
     */
    public function actionBroadcast($oApp, $oRequest, $isCP = false){
        
        $oRPC = new RPC();

        $signedTxHex = $oRequest->getCallParameters('signedTxHex', false);
        if(!$signedTxHex){
            // todo
        }
        try{
            if($isCP){
                $result = $oRPC->execCounterpartyd(
                    'broadcast_tx',
                    array(
                        'signed_tx_hex' => $signedTxHex
                    ),
                    true
                );
            }else{
                $result = $oRPC->execBitcoind(
                    'sendrawtransaction',
                    array($signedTxHex),
                    true
                );
            }
            if(!is_null($result)){
                $this->getView()->set('result',$result);
            }else{
                // todo
            }
        }catch(\Exception $e){
            // todo
        }
    }
}