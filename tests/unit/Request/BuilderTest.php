<?php


namespace RoyalMail\tests\unit\Request;

use atoum;
use \RoyalMail\Request\Builder as ReqBuilder;
use \Symfony\Component\Yaml\Yaml;

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


  function testValidRequests() {
    $helper   = new \RoyalMail\Helper\Data(['override_defaults' => ['_disable_includes' => TRUE]]);
    $requests = glob(RESOURCES_DIR . '/requests/*.yml');

    foreach ($requests as $req_file) {
      $req_name    = basename($req_file, '.yml');
      $setup = $this->getTestSchema('requests/' . $req_name);

      $valid = $setup['valid'];
      $built = ReqBuilder::build(preg_replace('/_\w+$/', '', $req_name), $valid['request'], $helper);

      $this
        ->array($built)
        ->isEqualTo($valid['expect']);
    }
  }


  static function getTestConfigs($key) {
    return Yaml::parse(file_get_contents(RESOURCES_DIR . '/' . $key . '.yml'));
  }
}