<?php

namespace AmiLabs\Chainy;

use \AmiLabs\CryptoKit\RPC;
use \AmiLabs\DevKit\Registry;

/**
 * Chainy transaction class.
 */
class TX extends \AmiLabs\CryptoKit\TX {
    /**
     * Transaction types
     */
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
     * @param int $block
     * @return boolean|string
     */
    public static function getBlockDate($block){
        if(!$block){
            return false;
        }
        $oRPC = new RPC();
        try{
            $aResult = $oRPC->execCounterpartyd('get_block_info', array('block_index' => $block), false, true);
        }catch(\Exception $e){ /* todo */ }
        return date('Y-m-d H:i:s', (int)$aResult['block_time']);
    }
    /**
     * Returns block and position inside a block by hash of the transaction.
     *
     * @param string $txHash
     * @return boolean|array
     */
    public static function getPositionInBlockByTransaction($txHash){
        if(!$txHash){
            return false;
        }
        $block = null;
        $txPosition = null;
        $oRPC = new RPC();
        try{
            $aResult = $oRPC->execBitcoind('getrawtransaction', array($txHash, 1), false, false);
            if(is_array($aResult)){
                $blockHash = $aResult['blockhash'];
                $aResult = $oRPC->execBitcoind('getblock', array($blockHash), false, true);
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
     * @param int $block
     * @param int $position
     * @return boolean|string
     */
    public static function getTransactionByPositionInBlock($block, $position){
        if(!$block || !$position){
            return false;
        }
        $txHash = null;
        $oRPC = new RPC();
        try{
            $aResult = $oRPC->execCounterpartyd('get_block_info', array('block_index' => $block), false, true);
            if(is_array($aResult)){
                $blockHash = $aResult['block_hash'];
                $aResult = $oRPC->execBitcoind('getblock', array($blockHash), false, true);
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
     * Detects Chainy transaction by special marker.
     *
     * @param string $tx
     * @return boolean
     */
    public static function isChainyTransaction($tx){
        $result = false;
        $oRPC = new RPC();
        $raw = $oRPC->execBitcoind('getrawtransaction', array($tx), false, true);
        if(strlen($raw)){
            $aMarkers = Registry::useStorage('CFG')->get('markers');
            foreach($aMarkers as $marker){
                if(strpos(strtolower($raw), $marker) !== false){
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }
    public static function getTransactionType($tx){
        $oRPC = new RPC();
        $data = $oRPC->execBitcoind('getrawtransaction', array($tx), false, true);
        $opData = TX::getDecodedOpReturn($data, true);
        return hexdec(substr($opData, 0, 1));
    }
    /**
     * Decode Chainy transaction.
     *
     * @param string $tx
     * @return array
     */
    public static function decodeChainyTransaction($tx){
        $aTX = array();
        $oRPC = new RPC();
        $data = $oRPC->execBitcoind('getrawtransaction', array($tx), false, true);
        $opData = TX::getDecodedOpReturn($data, true);
        $txType = hexdec(substr($opData, 0, 1));
        $aTX['type'] = $txType;
        switch($txType){
            case self::TX_TYPE_REDIRECT:
                $aTX += self::decodeRedirectTransaction($opData, $data);
                break;
            case self::TX_TYPE_HASHLINK:
            default:
                $aTX += self::decodeHashLinkTransaction($opData, $data);
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
    public static function packChainyTransaction(){
        
    }
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

        echo('SIGNED TX: ' . $raw['hex'] . '<br><Br>');

        // 4. broadcast
        try{
            $tx = $oRPC->execBitcoind('sendrawtransaction', array($raw['hex']), true);
        }catch(\Exception $e){
            die('BROADCAST: Exception ' . $e->getMessage());
        }

        return $tx;
    }
    public static function getMarker(){
        return Registry::useStorage('CFG')->get('marker');
    }
    /**
     * 
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
     * 
     * @param string $url
     * @return string
     */
    public static function createHashLinkTransaction($url){
        set_time_limit(0);
        $tx = 'not created';
        $destination = PATH_TMP . "/" . md5($url) . '.tmp';
        if(!file_exists($destination)){
            // Download
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $data = curl_exec($ch);
            curl_close ($ch);
            file_put_contents($destination, $data);
            chmod($destination, 0777);
        }
        if(!file_exists($destination)){
            die('Error downloading file ' . $url);
        }
        $hash = hash_file('sha256', $destination);
        $fileSize = filesize($destination);

        $protocol = (strpos($url, 'https://') === 0) ? self::URL_TYPE_HTTPS : self::URL_TYPE_HTTP;
        $url = substr($url, $protocol ? 8 : 7);

        $fileType = self::getFileType($url);

        $markerHex = pack('H*', self::getMarker());
        $sByte = str_pad(decbin(self::TX_TYPE_HASHLINK), 4, '0', STR_PAD_LEFT) . $protocol . self::PROTOCOL_VERSION;

        $opretStr = chr(bindec($sByte)) . $markerHex . chr($fileType) . pack('H*', $hash);

        $msigStr = $url . pack('H*', str_pad(dechex($fileSize), 8, '0', STR_PAD_LEFT));
        $sizeBytes = str_pad(pack('H*', dechex($fileSize)), 4, chr(0), STR_PAD_LEFT);

        unlink($destination);

        return self::sendChainyTransaction($opretStr, $msigStr);
    }
    /**
     * Returns file type by filename or url.
     *
     * @param string $url
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
     * @param int $int
     * @return string
     */
    public static function encodeBase58($int){
        $base58_string = "";
        $base = strlen(self::$alphabet);
        while($int >= $base) {
            $div = floor($int / $base);
            $mod = ($int - ($base * $div));
            $base58_string = self::$alphabet{$mod} . $base58_string;
            $int = $div;
        }
        if($int) $base58_string = self::$alphabet{$int} . $base58_string;
        return $base58_string;
    }
    /**
     * Decodes base58 string into int.
     *
     * @param string $base58
     * @return int
     */
    public static function decodeBase58($base58){
        $int_val = 0;
        for($i=strlen($base58)-1,$j=1,$base=strlen(self::$alphabet);$i>=0;$i--,$j*=$base) {
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
        $dataLength  = hexdec(substr($outHex, 4, 2));
        $outHex = substr($outHex, 6);
        $hex1 = substr($outHex, 0, 64);
        $hex2 = substr($outHex, 66, 66);
        $hex3 = substr($outHex, 134, 66);

        $data = ltrim($hex1 . $hex2 . $hex3, '0');
        $data = substr($data, 0, $dataLength);
        $data = pack('H*', $data);

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
