<?php

namespace RoyalMail\tests\unit\Response;

use atoum;
use \RoyalMail\Response\Interpreter as Inter;
use \Symfony\Component\Yaml\Yaml;

class Interpreter extends atoum {

  use \RoyalMail\tests\lib\TestDataLoader;


  function testSuccessResponses() {
    $requests = glob(RESOURCES_DIR . '/responses/*.yml');
    $int = new Inter;

    foreach ($requests as $req) {
      $data = self::getTestPattern($req);
      $op   = basename($req, '.yml');
      $parsed = $int->loadResponse($op, $data['success_arr']);

      $this->array($parsed->asArray())->isEqualTo($data['success_response']);
      $this->boolean($parsed->succeeded())->isTrue();
    }
  }

  function testError() {
    $int    = new Inter;
    $data   = self::getTestPattern(RESOURCES_DIR . '/responses/cancelJob.yml');
    $parsed = $int->loadResponse('cancelJob', $data['error_arr']);

    $this->boolean($parsed->succeeded())->isFalse();
    $this->array($parsed->getErrors())->isEqualTo(['4003 : Access Denied (check credentials)']);
  }


  function testRejection() {
    $int    = new Inter;
    $data   = self::getTestPattern(RESOURCES_DIR . '/responses/cancelJob.yml');
    $parsed = $int->loadResponse('cancelJob', $data['rejection_arr']);

    $this->boolean($parsed->succeeded())->isFalse();

    $this->array($parsed->getErrors())->isEqualTo(['1000 : Error on line 12: Element type "tariff" must be followed by either attribute specifications, ">" or "/>".']);

  }


  static function getTestPattern($file, $key = NULL) {
    $loaded = Yaml::parse(file_get_contents($file));

    return ($key) ? $loaded[$key] : $loaded;
  }


}