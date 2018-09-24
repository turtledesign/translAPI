<?php namespace RoyalMail;

if (! defined('MODULE_ROOT')) define('MODULE_ROOT', dirname(__FILE__) . '/../');


use \RoyalMail\Connector\xmlConnector  as Connector;
use \RoyalMail\Helper\Data             as Store;
use \RoyalMail\Helper\Development      as DevHelper;
use \RoyalMail\Request\Builder         as Builder;
use \RoyalMail\Response\Interpreter    as Interpreter;


class RoyalMail {

  /**
   * Create New
   * 
   * @param array $args This should contain security details and config default overrides.
   * 
   */
  function __construct($args = []) {
  }


  function createNewJob($params, $config = []) {
    return $this->processAction('createNewJob', $params, $config);
  }


  function cancelJob($params, $config = []) {
    return $this->processAction('cancelJob', $params, $config);
  }


  function processAction($action, $params, $config = []) {
    return  $this->interpretResponse($action,
              $this->send(
                $this->buildRequest($action, $params, $config),
                $config
              )
            );
  }


  function buildRequest($action, $params, $config = []) {
    return Builder::build($action, $params, $this->getDataHelper($config));
  }


  function send($arr, $config) {
    return (new Connector([]))->request($arr, $config);
  }


  function interpretResponse($action, $response) {
    return (new Interpreter)->loadResponse($action, $response, []);
  }


  function getDataHelper($config = []) {
    $config = array_merge(['override_defaults' => ['_disable_includes' => FALSE]], $config);

    if (empty($this->data_helper)) $this->data_helper = new Store($config);

    return $this->data_helper;
  }
} 
