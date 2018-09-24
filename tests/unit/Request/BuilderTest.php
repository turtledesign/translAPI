<?php

namespace RoyalMail\tests\unit\Request;

use atoum;
use \RoyalMail\Request\Builder as ReqBuilder;
use \Symfony\Component\Yaml\Yaml;
use \RoyalMail\Helper\Xml as XmlHelper;

class Builder extends atoum {

  use \RoyalMail\tests\lib\TestDataLoader;

  function testValueDefaulting() {
    $this->string(ReqBuilder::processSingleProperty(['_default' => 'foo'], @$not_defined))->isEqualTo('foo');
    $this->string(ReqBuilder::processSingleProperty(['_default' => 'bar'], '0'))->isEqualTo('bar'); // Beware.
    $this->string(ReqBuilder::processSingleProperty(['_default' => '0044'], NULL))->isEqualTo('0044');
  }


  function testPathCreation() {
    $this
      ->array(ReqBuilder::addProperty(['us' => 'chickens'], ['_key' => 'foo/bar/baz'], 0, 'kaboom!'))
      ->isEqualTo(['us' => 'chickens', 'foo' => ['bar' => ['baz' => 'kaboom!']]]);

    $this
      ->array(ReqBuilder::addProperty([], ['_key' => 'foo/bar/baz'], 0, 'kaboom!'))
      ->isEqualTo(['foo' => ['bar' => ['baz' => 'kaboom!']]]);


    $this
      ->array(ReqBuilder::addProperty(['us' => 'chickens'], [], 'fizz', 'buzz'))
      ->isEqualTo(['us' => 'chickens', 'fizz' => 'buzz']);
  }


  function testCancelRequest() {
    $helper  = new \RoyalMail\Helper\Data(['override_defaults' => ['_disable_includes' => FALSE]]);
    $setup   = $this->getTestSchema('requests/cancelJob');

    $valid = $setup['valid'];
    $built = ReqBuilder::build('cancelJob', $valid['request'], $helper);

    unset($built['request']['trackingUpdate']['trackingDateTime']); // These are generated timestamps, so vary : test elsewhere

    $this->array($built)->isEqualTo($valid['expect']);
  }


  function testCreateRequest() {
    $helper  = new \RoyalMail\Helper\Data(['override_defaults' => ['_disable_includes' => FALSE]]);
    $setup   = $this->getTestSchema('requests/createNewJob');

    $valid = $setup['valid'];
    $built = ReqBuilder::build('createNewJob', $valid['request'], $helper);

    $this->array($built)->isEqualTo($valid['expect']);

    $parsed  = XmlHelper::toSabreArray($built);


    $xml = (new XmlHelper)->toXml($built);

    $this->string($xml)->isEqualTo($valid['xml']);
  }


  static function getTestConfigs($key) {
    return Yaml::parse(file_get_contents(RESOURCES_DIR . '/' . $key . '.yml'));
  }
}