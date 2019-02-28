<?php

namespace TranslAPI\Response;


use \Symfony\Component\Yaml\Yaml;
use \TranslAPI\Exception\StructureSkipFieldException as SkipException;

class Interpreter extends \ArrayObject {
  
  use \TranslAPI\Validator\Validates;
  use \TranslAPI\Filter\Filters;
  use \TranslAPI\Helper\Structure;

  protected 
    $response_instance = NULL,
    $response_schema   = NULL,
    $schema            = NULL,
    $security_info     = [],
    $errors            = [],
    $warnings          = [],
    $succeeded         = FALSE,
    $debug_info        = NULL;


  function __construct($key = NULL, $response = NULL, $helper = []) {
    if (isset($key) && isset($response)) $this->loadResponse($key, $response, $helper);
  }


  function succeeded()    { return $this->succeeded; }
 
  function hasIssues()    { return $this->hasErrors() || $this->hasWarnings(); } 

  function hasErrors()    { return count($this->getErrors()) > 0; }

  function getErrors()    { return $this->errors; }

  function hasWarnings()  { return count($this->getWarnings()) > 0; }

  function getWarnings()  { return $this->warnings; }

  function hasDebugInfo() { return is_null($this->debug_info); }

  function hasBinaries() {
    foreach ($this->getBinaryKeys() as $bin) if (! empty($this[$bin])) return TRUE;

    return FALSE;
  }


  function getSecurityInfo() {
    return $this->security_info;
  }


  function asArray()     { return $this->getResponse(); }
  function getResponse() {
    return $this->getArrayCopy();
  }


  function getRawResponse() { return $this->response_instance; }


  function loadResponse($key, $response, $helper = []) {
    if ($response instanceof \TranslAPI\Exception\ResponseException) return $this->connectionError($response);

    $this->response_instance = $response;
    $this->response_schema   = self::getResponseSchema($key);

    $result = self::build($this->response_schema, $response, $helper);

    // TODO: Hardcoded for NetDespatch.
    if (! empty($result['ERRORS'])) {
      $this->errors[] = array_shift($result['ERRORS']['code']) . ' : ' . array_shift($result['ERRORS']['message']);
    }

    if (isset($result['META']['success']))              $this->succeeded     = $result['META']['success'];
    if (isset($result['META']['security']))             $this->security_info = $result['META']['security'];
    if (isset($result['META']['messages']['errors']))   $this->errors        = array_merge($this->errors, $result['META']['messages']['errors']);
    if (isset($result['META']['messages']['warnings'])) $this->warnings      = $result['META']['messages']['warnings'];

    if (isset($result['RESPONSE']) && is_array($result['RESPONSE'])) $this->exchangeArray($result['RESPONSE']);

    if ($this->hasErrors()) $this->succeeded = FALSE;

    return $this;
  }



  static function build($schema, $response, $helper = NULL) {
    if (empty($response) && isset($helper['source'])) $response = $helper['source'];

    if (is_scalar($schema)) $schema = self::getResponseSchema($schema);

    return self::processSchema($schema, $response, $helper);
  }


  static function processSchema($schema, $response, $helper = []) {
    $built  = [];
    $helper = is_array($helper) ? array_merge(['source' => $response], $helper) : ['source' => $helper];

    foreach ($schema['properties'] as $k => $map) {
      $built = self::addProperty($built, $map, $k, NULL, [], $helper);
    }

    return $built;
  }


  static function addProperty($arr, $schema, $key, $val, $defaults, $helper) {
    try {
      $val = (empty($schema['_extract'])) ? $val : self::extractValue($helper['source'], $schema);

    } catch (SkipException $e) {
      // pass for now - in some circumstances may be best to create an empty structure (defined in schema).
      return $arr;
    } 

    if (! empty($schema['_multiple']) && count($stripped = self::stripMeta($schema))) {
      $schema = array_diff_key($schema, $stripped); // FIXME: This is patching to bypass the default Structure multi property handling
      unset($schema['_multiple']);                  
      
      $nest = [];

      foreach ($val as $multi) {
        $nest[] = self::processSchema(['properties' => $stripped], $multi, array_merge($helper, ['source' => $multi]));
      }

      $val = $nest;
    }

    return self::doAddProperty($arr, $schema, $key, $val, $defaults, $helper);
  }


  static function extractValue($response, $map) {
    foreach (explode('/', $map['_extract']) as $step) {
      if (is_object($response)) { // Object, e.g. from SOAP connector.
        if (! isset($response->$step)) throw new SkipException('value not present in response');

        $response = $response->$step;

      } else { // Array, e.g. from XMLReader conversion
        if (! isset($response[$step])) throw new SkipException('value not present in response');

        $response = $response[$step];
      }
    }

    if (isset($map['_multiple']) && ! is_array($response)) $response = [$response]; // Single entries for multi-optional values in SOAP elide the array.

    return $response;
  }


  function getResponseEncoded() {
    $arr = $this->getResponse();

    if (isset($this->response_schema['binaries'])) foreach (array_keys($this->response_schema['binaries']) as $bin) {
      if (! empty($arr[$bin])) $arr[$bin] = base64_encode($arr[$bin]);
    }

    return $arr;
  }



  function getBinaryKeys() {
    return array_keys($this->getBinariesInfo());
  }


  function getBinariesInfo() {
    return @$this->response_schema['binaries'] ?: [];
  }


  function getDebugInfo() { return $this->debug_info; }
  
  function connectionError($exception) {
    $this->errors[] = ['message' => 'API Connection Error: more details available in debug info.'];

    $this->debug_info = $exception;

    return $this;
  }


  function serialise($json_opts = NULL) {
    return json_encode([
      'succeeded' => (int) $this->succeeded(),
      'response'  => $this->asArray(),
      'errors'    => $this->getErrors(),
      'warnings'  => $this->getWarnings(),
      'security'  => $this->getSecurityInfo(),
      'debug'     => $this->getDebugInfo()
    ], $json_opts);
  }


  function __toString() {
    return $this->serialise();
  }


  static function getResponseSchema($response_name) {
    return Yaml::parse(file_get_contents(dirname(__FILE__) . '/schema/' . $response_name . '.yml'));
  }
}