<?php

namespace AmiLabs\Chainy\Frontend;

use \AmiLabs\DevKit\RequestURI as RequestURI;
use \AmiLabs\DevKit\IRequestDriver as IRequestDriver;

/**
 * Request with parameters ent through URI string driver.
 */
class RequestCHAINY extends RequestURI implements IRequestDriver {
    /**
     * Constructor.
     */
    public function __construct(){
        parent::__construct();
        array_unshift($this->aData, $this->actionName);
        $this->actionName = $this->controllerName = 'index';
    }
}
