<?php

namespace AmiLabs\Chainy;

use AmiLabs\CryptoKit\RPC;
use AmiLabs\CryptoKit\BlockchainIO;
use AmiLabs\DevKit\Registry;
use AmiLabs\DevKit\Cache;
use Moontoast\Math\BigNumber;

/**
 * Chainy transaction class.
 */
class TX extends \AmiLabs\CryptoKit\TX {
    /**
     * Transaction types
     */
    const TX_TYPE_INVALID   = 0;
    const TX_TYPE_REDIRECT  = 1;
    const TX_TYPE_HASH      = 2;
    const TX_TYPE_TEXT      = 3;
    const TX_TYPE_HASHLINK  = 4;
    /**
     * Supported file types
     */
    const FILE_TYPE_UNKNOWN = 0;
    const FILE_TYPE_PDF     = 1;
    const FILE_TYPE_ARCHIVE = 2;
    const FILE_TYPE_TEXT    = 3;
    const FILE_TYPE_IMAGE   = 4;
    /**
     * URL types
     */
    const URL_TYPE_HTTP     = 0;
    const URL_TYPE_HTTPS    = 1;
    /**
     * Chainy protocol version
     */
    const PROTOCOL_VERSION = '000';
    /**
     * Base58 alphabet
     *
     * @var string
     */
    protected static $alphabet = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    /**
     * Returns the date block was generated.
     *
     * @param int $block         Block index
     * @param bool $dateFormat   PHP date format, or return result as unix timestamp if false
     * @return boolean|string
     */
    public static function getBlockDate($block, $dateFormat = 'Y-m-d H:i:s'){
        if(!$block){
            return false;
        }
        $aResult = FALSE;
        try{
            $aResult = BlockchainIO::getInstance()->getBlockInfo($block);
        }catch(\Exception $e){ /* todo */ }
        $time = is_array($aResult) && isset($aResult['block_time']) ? $aResult['block_time'] : FALSE;
        return ($time !== FALSE) ? ($dateFormat ? date($dateFormat, (int)$time) : $time) : $time;
    }
    /**
     * Returns block and position inside a block by hash of the transaction.
     *
     * @param string $txHash  Transaction hash
     * @return boolean|array
     */
    public static function getPositionInBlockByTransaction($txHash){
        if(!$txHash){
            return array('block' => null, 'position' => null);;
        }
        $block = null;
        $txPosition = null;
        try{
            $aResult = BlockchainIO::getInstance()->getRawTransaction($txHash, TRUE, FALSE, FALSE);
            if(is_array($aResult) && isset($aResult['blockhash'])){
                $blockHash = $aResult['blockhash'];
                $aResult = BlockchainIO::getInstance()->getBlock($blockHash);
                if(is_array($aResult)){
                    $block = $aResult['height'];
                    $aTx = $aResult['tx'];
                    sort($aTx);
                    $key = array_search($txHash, $aTx);
                    if($key !== false){
                        $txPosition = $key;
                    }
                }
            }
        }catch(\Exception $e){ /* todo */ }
        return array('block' => $block, 'position' => $txPosition);
    }
    /**
     * Returns transaction hash by block and position inside block.
     *
     * @param int $block     Block index
     * @param int $position  Position in block
     * @return boolean|string
     */
    public static function getTransactionByPositionInBlock($block, $position){
        if(!$block || !$position){
            return null;
        }
        $txHash = null;
        try{
            $aResult = BlockchainIO::getInstance()->getBlockInfo($block);
            if(is_array($aResult)){
                $blockHash = $aResult['block_hash'];
                $aResult = BlockchainIO::getInstance()->getBlock($blockHash);
                if(is_array($aResult)){
                    $aTx = $aResult['tx'];
                    sort($aTx);
                    if(isset($aTx[$position])){
                        $txHash = $aTx[$position];
                    }
                }
            }
        }catch(\Exception $e){ /* todo */ }
        return $txHash;
    }
    /**
     * Checks if specified transaction hash belongs to correct Chainy transaction.
     *
     * @param string $tx  Transaction hash
     * @return boolean
     */
    public static function isChainyTransaction($tx){
        $result = FALSE;
        $raw = '';
        try{
            $raw = BlockchainIO::getInstance()->getRawTransaction($tx);
            $result = self::isChainyTransactionRaw($raw);
        }catch(\Exception $e){ /* todo */ }
        return $result;
    }
    /**
    * Detects Chainy transaction by special marker in raw hex.
    *
    * @param string $raw  Transaction raw hex
    * @return boolean
    */
    protected static function isChainyTransactionRaw($raw){
        $result = FALSE;
        if(strlen($raw)){
            $aMarkers = Registry::useStorage('CFG')->get('markers', array());
            foreach($aMarkers as $marker){
                if(strpos(strtolower($raw), $marker) !== false){
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }
    /**
     * Returns Chainy transaction type.
     *
     * @param string $tx  Transaction hash
     * @return int
     */
    public static function getTransactionType($tx){
        $result = self::TX_TYPE_INVALID;
        try{
            $data = BlockchainIO::getInstance()->getRawTransaction($tx);
            if(self::isChainyTransactionRaw($data)){
                $opData = TX::getDecodedOpReturn($data, true);
                if($opData){
                    $result = self::getTransactionTypeByOpReturn($opData);
                }
            }
        }catch(\Exception $e){ /* todo */ }
        return $result;
    }
    /**
     * Returns Chainy transaction type by OP_RETURN output raw data.
     *
     * @param string $opData  OP_RETURN output raw data
     * @return int
     */
    protected static function getTransactionTypeByOpReturn($opData){
        return hexdec(substr($opData, 0, 1));
    }
    /**
     * Decode Chainy transaction.
     *
     * @param string $tx  Transaction hash
     * @return array
     */
    public static function decodeChainyTransaction($tx){
        $aTX = array();
        try{
            $data = BlockchainIO::getInstance()->getRawTransaction($tx);
            if(self::isChainyTransactionRaw($data)){
                $opData = TX::getDecodedOpReturn($data, true);
                $txType = self::getTransactionTypeByOpReturn($opData);
                $aTX['type'] = $txType;
                switch($txType){
                    case self::TX_TYPE_REDIRECT:
                        $aTX += self::decodeRedirectTransaction($opData, $data);
                        break;
                    case self::TX_TYPE_HASHLINK:
                        $aTX += self::decodeHashLinkTransaction($opData, $data);
                        break;
                    case self::TX_TYPE_INVALID:
                    default:
                        // todo
                }
            }
        }catch(\Exception $e){ /* todo */ }
        return $aTX;
    }
    /**
     * Decodes transacton of "Hash and Link" type.
     *
     * @param string $opReturnData  OP_RETURN raw hex
     * @param string $data          Transaction raw hex
     * @return array
     */
    protected static function decodeRedirectTransaction($opReturnData, $data){
        $aTX = array(
            'link'      => ''
        );
        // Url protocol
        $isHttps = (int)substr(decbin(hexdec(substr($opReturnData, 1, 1))), 0, 1);
        if(strlen($opReturnData) > 16){
            $link = hex2bin(substr($opReturnData, 14));
        }else{
            $aTrans = self::decodeTransaction($data);
            foreach($aTrans['vout'] as $aOut){
                if(strpos($aOut['scriptPubKey'], '5121') === 0){
                    $link = self::decodeMultisigOutput($aOut['scriptPubKey']);
                    break;
                }
            }
        }
        if(strlen($link)){
            $aTX['link'] = 'http' . ($isHttps ? 's' : '') . '://' . $link;
        }
        return $aTX;
    }
    /**
     * Decodes transacton of "Hash and Link" type.
     *
     * @param string $opReturnData
     * @param string $data
     * @return array
     */
    protected static function decodeHashLinkTransaction($opReturnData, $data){
        $aTX = array(
            'file_name' => 'unknown',
            'file_size' => 0,
            'link'      => ''
        );
        // Sha256
        $aTX['hash'] = substr($opReturnData, 16);
        // Url protocol
        $isHttps = (int)substr(decbin(hexdec(substr($opReturnData, 1, 1))), 0, 1);

        $aTrans = self::decodeTransaction($data);
        foreach($aTrans['vout'] as $aOut){
            if(strpos($aOut['scriptPubKey'], '5121') === 0){
                $data = self::decodeMultisigOutput($aOut['scriptPubKey']);
            }
        }
        if(substr($data, strlen($data) - 4, 1) === '.'){
            $link = $data;
            $size = 22000;
        }else{
            $link = substr($data, 0, strlen($data) - 4);
            $size = hexdec(bin2hex(substr($data, strlen($data) - 4, 4)));
        }
        $filename = basename($link);
        if(strpos($filename, '?') !== false){
            $filename = substr($filename, 0, strpos($filename, '?'));
        }
        $aTX['file_name'] = $filename;
        $aTX['link'] = 'http' . ($isHttps ? 's' : '') . '://' . $link;
        $aTX['file_size'] = self::getFileSize($size);

        // Filetype
        $fileType = (int)substr($opReturnData, 15, 1);
        if(!$fileType){
            $fileType = self::getFileType($link);
        }
        switch($fileType){
            case self::FILE_TYPE_PDF:
                $aTX['file_type'] = 'pdf';
                break;
            case self::FILE_TYPE_ARCHIVE:
                $aTX['file_type'] = 'archive';
                break;
            case self::FILE_TYPE_TEXT:
                $aTX['file_type'] = 'text';
                break;
            case self::FILE_TYPE_IMAGE:
                $aTX['file_type'] = 'image';
                break;
            default:
                $aTX['file_type'] = '';
        }
        return $aTX;
    }
    /**
     * Creates, compiles, signs and sends Chainy transaction.
     *
     * @param string $opretStr  OP_RETURN raw data
     * @param string $msigStr   Multisig output data
     * @return string
     * @ignore
     */
    public static function sendChainyTransaction($opretStr, $msigStr = false){
        $aConfig = Registry::useStorage('CFG')->get('addresses');
        $oRPC = new RPC();
        // 1. create send
        try{
            $raw = $oRPC->execCounterpartyd(
                'create_send',
                array(
                    "asset"                     => 'BTC',
                    "source"                    => $aConfig['source']['address'],
                    "destination"               => $aConfig['destination']['address'],
                    "quantity"                  => 7800,
                    "allow_unconfirmed_inputs"  => true,
                    "encoding"                  => "multisig",
                    "pubkey"                    => array($aConfig['source']['pubkey'])
                ),
                true
            );
        }catch(\Exception $e){ die('SEND: Exception'); }
        if(!$raw){
            die('SEND: empty response');
        }
        // 2. add op_return and multisig
        if($msigStr){
            $raw = self::addMultisigDataOutput($raw, $msigStr);
        }
        $raw = self::addOpReturnOutput($raw, $opretStr);
        // 3. Sign
        try{
            $raw = $oRPC->execBitcoind('signrawtransaction', array($raw, array(), array($aConfig['source']['privkey'])), true);
        }catch(\Exception $e){
            die('SIGN: Exception');
        }
        // 4. broadcast
        try{
            $tx = $oRPC->execBitcoind('sendrawtransaction', array($raw['hex']), true);
        }catch(\Exception $e){
            die('BROADCAST: Exception ' . $e->getMessage());
        }
        return $tx;
    }
    /**
     * Returns current Chainy marker from config.
     *
     * @return string
     */
    public static function getMarker(){
        return Registry::useStorage('CFG')->get('marker');
    }
    /**
     * Create Chainy transaction of "Redirect" type.
     *
     * @param string $url  URL of redirect
     * @return string
     */
    public static function createRedirectTransaction($url){
        $tx = 'not created';

        $protocol = (strpos($url, 'https://') === 0) ? self::URL_TYPE_HTTPS : self::URL_TYPE_HTTP;
        $url = substr($url, $protocol ? 8 : 7);

        $fileType = self::getFileType($url);

        $markerHex = pack('H*', self::getMarker());
        $sByte = str_pad(decbin(self::TX_TYPE_REDIRECT), 4, '0', STR_PAD_LEFT) . $protocol . self::PROTOCOL_VERSION;

        $msigStr = false;
        $opRetData = '';
        if(strlen($url) <= 33){
            $opRetData = $url;
        }else{
            $msigStr = $url;
        }
        $opretStr = chr(bindec($sByte)) . $markerHex . $opRetData;

        return self::sendChainyTransaction($opretStr, $msigStr);
    }
    /**
     * Create Chainy transaction of "Hash and Link" type.
     *
     * @param string $url  URL of file
     * @return string
     */
    public static function createHashLinkTransaction($url){
        set_time_limit(0);
        $tx = 'not created';
        $oCache = Cache::get(md5($url));
        $destination = PATH_TMP . "/" . md5($url) . '.tmp';
        if(!$oCache->exists()){
            // Download file from web
            // Todo: partial downloads of big files
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $data = curl_exec($ch);
            curl_close ($ch);
            $oCache->save($data);
        }
        if(!$oCache->exists()){
            die('Error downloading file ' . $url);
        }
        // Todo: calculate hash by reading parts of the file
        $hash = hash_file('sha256', $oCache->getFilename());
        $fileSize = filesize($oCache->getFilename());

        $protocol = (strpos($url, 'https://') === 0) ? self::URL_TYPE_HTTPS : self::URL_TYPE_HTTP;
        $url = substr($url, $protocol ? 8 : 7);

        $fileType = self::getFileType($url);

        $markerHex = pack('H*', self::getMarker());
        $sByte = str_pad(decbin(self::TX_TYPE_HASHLINK), 4, '0', STR_PAD_LEFT) . $protocol . self::PROTOCOL_VERSION;

        $opretStr = chr(bindec($sByte)) . $markerHex . chr($fileType) . pack('H*', $hash);

        $msigStr = $url . pack('H*', str_pad(dechex($fileSize), 8, '0', STR_PAD_LEFT));
        $sizeBytes = str_pad(pack('H*', dechex($fileSize)), 4, chr(0), STR_PAD_LEFT);

        $oCache->clear();

        return self::sendChainyTransaction($opretStr, $msigStr);
    }
    /**
     * Returns file type by filename or url.
     *
     * @param string $url  URL of file
     * @return int
     */
    protected static function getFileType($url){
        $urlNoParams = $url;
        if(strpos($url, '?') !== false){
            $urlNoParams = substr($url, 0, strpos($url, '?'));
        }
        $ext = strtolower(substr($urlNoParams, strrpos($urlNoParams, '.') + 1));
        $fileType = self::FILE_TYPE_UNKNOWN;
        $aFileTypes = array(
            self::FILE_TYPE_PDF     => array('pdf'),
            self::FILE_TYPE_ARCHIVE => array('zip', 'rar', 'gz', 'arj', '7z', 'tgz', 'lzh'),
            self::FILE_TYPE_TEXT    => array('txt', 'doc', 'dox', 'rtf'),
            self::FILE_TYPE_IMAGE   => array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'psd', 'tiff', 'ico', 'pic', 'pcx')
        );
        foreach($aFileTypes as $type => $aExtensions){
            if(in_array($ext, $aExtensions)){
                $fileType = $type;
                break;
            }
        }
        return $fileType;
    }
    /**
     * Encodes int to base58 string.
     *
     * @param int $int  Number to encode
     * @return string
     */
    public static function encodeBase58($int){
        $number = new BigNumber($int);
        $result = '';
        do{
            $tmpNumber = clone($number);
            $reminder = (int)$tmpNumber->mod(58)->getValue();
            $result .= self::$alphabet[$reminder];
            $number = $number->divide(58)->floor();
        }while($number->getValue() > 0);
        $result = strrev($result);
        return $result;
    }
    /**
     * Decodes base58 string into int.
     *
     * @param string $base58  String to decode
     * @return int
     */
    public static function decodeBase58($base58){
        $int_val = 0;
        for($i = strlen($base58) - 1, $j = 1, $base = strlen(self::$alphabet); $i>=0; $i--, $j *= $base){
            $int_val += $j * strpos(self::$alphabet, $base58{$i});
        }
        return $int_val;
    }
    /**
     * Decodes multisig output.
     *
     * @param string $outHex
     * @param bool $withFileSize
     * @return string
     */
    public static function decodeMultisigOutput($outHex){
        $data = '';
        if((strlen($outHex) == 210) && (strpos($outHex, '5121') === 0)){
            $dataLength  = hexdec(substr($outHex, 4, 2));
            $outHex = substr($outHex, 6);
            $hex1 = substr($outHex, 0, 64);
            $hex2 = substr($outHex, 66, 66);
            $hex3 = substr($outHex, 134, 66);

            $data = ltrim($hex1 . $hex2 . $hex3, '0');
            $data = substr($data, 0, $dataLength);
            $data = pack('H*', $data);
        }
        return $data;
    }
    /**
     * Returns filesize with size dimension units.
     *
     * @param int $size
     * @return string
     */
    protected static function getFileSize($size){
        if($size < 1024){
            return $size . 'B';
        }
        if($size < 1024 *  1024){
            return round($size / 1024) . 'KB';
        }
        if($size < 1024 *  1024 * 1024){
            return round($size / (1024 * 1024)) . 'MB';
        }
        return round($size / (1024 * 1024 * 1024)) . 'GB';
    }
}
