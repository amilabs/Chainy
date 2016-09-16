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

namespace AmiLabs\Chainy;

use AmiLabs\CryptoKit\RPC;
use AmiLabs\CryptoKit\BlockchainIO;
use AmiLabs\DevKit\Registry;
use AmiLabs\DevKit\Cache;
use AmiLabs\DevKit\Application;
use Moontoast\Math\BigNumber;
use AmiLabs\CryptoKit\Blockchain\EthereumDB;

/**
 * Chainy transaction class.
 */
class TX extends \AmiLabs\CryptoKit\TX {
    /**
     * Maximum allowed file size
     */
    const MAX_FILE_SIZE = 50000000;
    /**
     * Transaction types
     */
    const TX_TYPE_INVALID   = '';
    const TX_TYPE_REDIRECT  = 'R';
    const TX_TYPE_HASH      = 'H';
    const TX_TYPE_TEXT      = 'T';
    const TX_TYPE_HASHLINK  = 'L';
    const TX_TYPE_ENCRYPTED = 'E';

    /**
     * Supported file types
     */
    const FILE_TYPE_UNKNOWN = 'raw';
    const FILE_TYPE_PDF     = 'pdf';
    const FILE_TYPE_ARCHIVE = 'arc';
    const FILE_TYPE_TEXT    = 'txt';
    const FILE_TYPE_IMAGE   = 'img';

    /**
     * Chainy protocol version
     */
    const PROTOCOL_VERSION = '1';

    /**
     * Base58 alphabet
     *
     * @var string
     */
    protected static $alphabet = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";

    /**
     * Default sender address
     *
     * @var string
     */
    protected static $sender = FALSE;

    /**
     * Returns Chainy transaction type.
     *
     * @param string $tx  Transaction hash
     * @return int
     */
    public static function getTransactionType($tx){
        $result = self::TX_TYPE_INVALID;
        return $result;
    }
    /**
     * Decode Chainy transaction.
     *
     * @param string $tx  Transaction hash
     * @return array
     */
    public static function decodeChainyTransaction($code){
        $result = array();
        if(is_string($code)){
            $oCache = Cache::get('code-' . $code);
            if(!$oCache->exists()){
                $oCfg = Application::getInstance()->getConfig();
                $result = self::_callRPC("get", array($code));
                if(is_array($result) && isset($result['data']) && isset($result['timestamp']) && $result['timestamp']){
                    $result['data'] = json_decode($result['data'], JSON_OBJECT_AS_ARRAY);
                    $result['data']['date'] = date("d.m.Y H:i:s", $result['timestamp']);
                    $result['data']['tx'] = TX::_getTxByCode($code);
                    $result = $result['data'];
                    switch($result['type']){
                        case self::TX_TYPE_HASHLINK:
                            $result['filesize'] = self::getFileSize($result['filesize']);
                            switch($result['filetype']){
                                case TX::FILE_TYPE_PDF:
                                    $result['filetype'] = 'pdf';
                                    break;
                                case TX::FILE_TYPE_ARCHIVE:
                                    $result['filetype'] = 'archive';
                                    break;
                                case TX::FILE_TYPE_TEXT:
                                    $result['filetype'] = 'text';
                                    break;
                                case TX::FILE_TYPE_IMAGE:
                                    $result['filetype'] = 'image';
                                    break;
                                default:
                                    $result['filetype'] = '';
                            }
                            $result['block'] = FALSE;
                            $result['tx'] = FALSE;
                            break;
                    }
                    $oCache->save($result);
                }else{
                    return FALSE;
                }
            }else{
                $result = $oCache->load();
            }
        }
        return $result;
    }

    /**
     * Create Chainy transaction of "Redirect" type.
     *
     * @param string $url  URL of redirect
     * @return string
     */
    public static function createRedirectTransaction($url){
        $data = self::_getTxData(self::TX_TYPE_REDIRECT, array('url' => $url));
        return array('data' => $data);
    }

    /**
     * Create Chainy transaction of "Text" type.
     *
     * @param string $text  URL of redirect
     * @return string
     */
    public static function createTextTransaction($text){
        $text = str_replace("\r", "" , $text);
        $data = self::_getTxData(self::TX_TYPE_TEXT, array('description' => $text, 'hash' => hash("sha256", $text)));
        return array('data' => $data);
    }

    /**
     * Create Chainy transaction of "Hash" type.
     *
     * @param string $text  URL of redirect
     * @return string
     */
    public static function createHashTransaction($text){
        $data = self::_getTxData(self::TX_TYPE_HASH, array('hash' => hash("sha256", $text)));
        return array('data' => $data);
    }

    /**
     * Create Chainy transaction of "Encrypted" type.
     *
     * @param string $text  URL of redirect
     * @return string
     */
    public static function createEncryptedTextTransaction($encrypted, $hash){
        $data = self::_getTxData(self::TX_TYPE_ENCRYPTED, array('encrypted' => $encrypted, 'hash' => $hash));
        return array('data' => $data);
    }

    /**
     * Create Chainy transaction of "Hash and Link" type.
     *
     * @param string $url  URL of file
     * @return string
     */
    public static function createLocalFileHashLinkTransaction($filename, $filesize, $hash, $description = FALSE){
        $data = self::_getTxData(self::TX_TYPE_HASHLINK, array(
            'filename'  => $filename,
            'hash'      => $hash,
            'filetype'  => self::getFileType($filename),
            'filesize'  => $filesize,
        ));
        if(FALSE !== $description){
            $description = str_replace("\r", "" , $description);
            $data['description'] = $description;
        }
        $result = array('data' => $data);
        return $result;
    }

    /**
     * Create Chainy transaction of "Hash and Link" type.
     *
     * @param string $url  URL of file
     * @return string
     */
    public static function createHashLinkTransaction($url, $description = FALSE){
        set_time_limit(0);
        $oCache = Cache::get(md5($url));
        $oCfg = Application::getInstance()->getConfig();
        $error = FALSE;
        if(!$oCache->exists()){
            // Download file from web
            // @todo: partial downloads of big files
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);
            $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            if($size < 0){
                $data = curl_exec($ch);
                $aMatches = array();
                if(preg_match("/Content\-Length.*(\d+)\s/U", $data, $aMatches)){
                    $size = (int)$aMatches[1];
                }
            }
            $data = FALSE;
            if(($size > 0) && ($size < self::MAX_FILE_SIZE)){
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_NOBODY, FALSE);
                $data = curl_exec($ch);
                if(FALSE !== $data){
                    $oCache->save($data);
                }else{
                    $error = 'Unable to download file';
                }
            }elseif($size < 0){
                $error = 'File not found';
            }else{
                $error = 'File size ' . self::getFileSize($size) . ' exeeds maximum allowed ' . self::getFileSize(self::MAX_FILE_SIZE);
            }
            curl_close ($ch);
        }
        $result = FALSE;
        if(!$error && $oCache->exists()){
            $data = self::_getTxData(self::TX_TYPE_HASHLINK, array(
                'url'       => $url,
                'hash'      => hash_file('sha256', $oCache->getFilename()),
                'filetype'  => self::getFileType($url),
                'filesize'  => filesize($oCache->getFilename()),
            ));
            if(FALSE !== $description){
                $description = str_replace("\r", "" , $description);
                $data['description'] = $description;
            }
            $result = array('data' => $data);
        }elseif(!$error){
            // File found but was not downloaded
            $error = "File reading error";
        }
        if($error){
            $result = array('error' => $error);
        }
        $oCache->clear();
        return $result;
    }

    /**
     * Returns Chainy link by transaction code.
     *
     * @param string $txHash
     * @return string
     */
    public static function getChainyLink($txHash){
        $result = FALSE;
        if(is_string($txHash) && (66 === strlen($txHash))){
            $result = self::_callRPC("getLink", array($txHash));
        }
        return $result;
    }

    /**
     * Publishes chainy TX on blockchain.
     *
     * @param array $data
     * @return array
     */
    public static function publishData(array $data){
        $result = FALSE;
        $oCfg = Application::getInstance()->getConfig();
        $sender = (FALSE !== self::$sender) ? self::$sender : $oCfg->get('sender');
        if($oCfg->get('autopublish', FALSE)){
            $tx = self::_callRPC("add", array($sender, json_encode($data, JSON_UNESCAPED_SLASHES)));
            // Transaction hash length should be 66 bytes
            if(strlen($tx) == 66){
                $result = array('hash' => $tx);
            }elseif(strlen($tx) > 66){
                $result = array('transaction' => $tx);
            }
        }
        return $result;
    }

    /**
     * Sets default seder address.
     *
     * @param string $sender
     */
    public static function setDefaultSender($sender){
        // @todo: check format
        self::$sender = $sender;
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
     * Returns formatted Chainy data.
     *
     * @param string $type
     * @param array $data
     * @return array
     */
    protected static function _getTxData($type, array $data){
        // @todo: check type
        return array(
            'id'        => 'CHAINY',
            'version'   => 1,
            'type'      => $type,
        ) + $data;
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
     * JSON RPC request implementation.
     *
     * @param string $method  Method name
     * @param array $params   Parameters
     * @return array
     */
    protected static function _callRPC($method, $params = array()){
        $oCfg = Application::getInstance()->getConfig();
        $data = array(
            'jsonrpc' => "2.0",
            'id'      => time(),
            'method'  => $method,
            'params'  => $params
        );
        $result = false;
        $json = json_encode($data);
        $ch = curl_init($oCfg->get('service'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json))
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $rjson = curl_exec($ch);
        if($rjson && (is_string($rjson)) && ('{' === $rjson[0])){
            $json = json_decode($rjson, JSON_OBJECT_AS_ARRAY);
            if(isset($json["result"])){
                $result = $json["result"];
            }
        }
        return $result;
    }

    protected static function _getTxByCode($code){
        $result = self::_callRPC("getTx", array($code));
        return $result;
    }
}
