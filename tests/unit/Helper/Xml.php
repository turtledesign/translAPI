<?php namespace RoyalMail\tests\unit\Helper;

use atoum;
use \RoyalMail\Helper\Xml as XmlHelper;
use \Symfony\Component\Yaml\Yaml;

class Xml extends atoum {


  function testFullCancellation() {
    $pattern = self::getTestPattern('cancellation');
    $parsed  = XmlHelper::toSabreArray($pattern['test']);

    $this->array($parsed)->isEqualTo($pattern['result']);


    $xml = (new XmlHelper)->toXml($pattern['test']);

    $this->string($xml)->isEqualTo($pattern['xml_result']);
  }


  function testSabreConversion() {
    $pattern = self::getTestPattern('attrs_and_content');
    $parsed  = XmlHelper::toSabreArray($pattern['test']);

    $this->array($parsed)->isEqualTo($pattern['result']);

    $xml = (new XmlHelper)->toXml($pattern['test']);

    $this->string($xml)->isEqualTo($pattern['xml_result']);
  }


  static function getTestPattern($key) {
    return Yaml::parse(file_get_contents(RESOURCES_DIR . '/xml_tests.yml'))[$key];
  }
}