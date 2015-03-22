<?php

namespace AmiLabs\Chainy\Frontend;

use \AmiLabs\DevKit\RPC;

/**
 * Chainy transaction class.
 */
class TX extends \AmiLabs\DevKit\TX {
    /**
     * Chainy transaction marker
     */
    const MARKER = '444556434841'; // DEVCHA
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
     * Returns block and position inside a block by hash of the transaction.
     *
     * @param string $txHash
     * @return boolean|array
     */
    public static function getBlockPositionByTransaction($txHash){
        if(!$txHash){
            return false;
        }
        $block = null;
        $txPosition = null;
        $oRPC = new RPC();
        try{
            $aResult = $oRPC->execBitcoind('getrawtransaction', array($txHash, 1), false, true);
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
    public static function getTransactionByBlockPosition($block, $position){
        if(!$block || !$position){
            return false;
        }
        $txHash = null;
        $oRPC = new RPC();
        try{
            $aResult = $oRPC->execCounterpartyd('get_block_info', array('block_index' => $block), false, true);
            if(is_array($aResult)){
                $blockHash = $aResult['blockhash'];
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
        $oRPC = new RPC();
        $result = $oRPC->execBitcoind('getrawtransaction', array($tx), false, true);
        return (strlen($result) && (strpos($result, self::MARKER) !== false));
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
        switch($txType){
            case self::TX_TYPE_HASHLINK:
            default:
                $aTX += self::decodeHashLinkTransaction($opReturnData, $data);
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
        $aTX['hash'] = substr($opData, 16);
        // Filetype
        $fileType = ord(substr($opData, 15, 1));
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
        // Url protocol
        $isHttps = (int)substr(decbin(hexdec(substr($opData, 1, 1))), 0, 1);
        $aTX['link'] = 'http' . ($isHttps ? 's' : '') . '://' . $aTX['link'];

        //$msigData = self::
        return $aTX;
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
        }else{
            $data = file_get_contents($destination);
        }
        $protocol = (strpos($url, 'https://') === 0) ? self::URL_TYPE_HTTPS : self::URL_TYPE_HTTP;
        $url = substr($url, $protocol ? 8 : 7);

        $fileType = self::getFileType($url);
        
        $markerHex = pack('H*', self::MARKER);
        $sByte = str_pad(decbin(self::TX_TYPE_HASHLINK), 4, '0', STR_PAD_LEFT) . $protocol . self::PROTOCOL_VERSION;
        var_dump($sByte);
        $opretStr = chr(bindec($sByte)) . $markerHex . chr($fileType) . pack('H*', hash('sha256', $data));
        
        var_dump(reset(unpack('H*', $opretStr)));
        
        //var_dump(filesize($destination));
        // unlink($destination);
        return $tx;
    
    }
    /**
     * Returns file type by filename or url.
     *
     * @param string $url
     * @return int
     */
    protected static function getFileType($url){
        $urlNoParams = substr($url, 0, strpos($url, '?'));
        $ext = strtolower(substr($url, strrpos($urlNoParams, '.') + 1));
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
}