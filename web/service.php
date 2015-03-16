<?php
/**
 * Backend
 */
define('APP_NAME', 'service');

require_once 'config.php';

\AmiLabs\DevKit\ApplicationServiceCounterparty::getInstance()->run();
