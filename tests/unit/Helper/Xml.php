<?php namespace TranslAPI\tests\unit\Helper;

use atoum;
use \TranslAPI\Helper\Xml as XmlHelper;
use \Symfony\Component\Yaml\Yaml;

class Xml extends atoum {


  function testCancellationResponses() {
    $data    = $this->getTestPattern('/responses/cancelJob.yml');

    $this->array((new XmlHelper)->fromXml($data['success_xml']))->isEqualTo($data['success_arr']);
    $this->array((new XmlHelper)->fromXml($data['error_xml']))->isEqualTo($data['error_arr']);
    $this->array((new XmlHelper)->fromXml($data['rejection_xml']))->isEqualTo($data['rejection_arr']);
  }


  function testFullCancellation() {
    $pattern = self::getTestPattern('/xml_tests.yml', 'cancellation');
    $parsed  = XmlHelper::toSabreArray($pattern['test']);

    $this->array($parsed)->isEqualTo($pattern['result']);

    $xml = (new XmlHelper)->toXml($pattern['test']);

    $this->string($xml)->isEqualTo($pattern['xml_result']);
  }


  function testSabreConversion() {
    $pattern = self::getTestPattern('/xml_tests.yml', 'attrs_and_content');
    $parsed  = XmlHelper::toSabreArray($pattern['test']);

    $this->array($parsed)->isEqualTo($pattern['result']);

    $xml = (new XmlHelper)->toXml($pattern['test']);

    $this->string($xml)->isEqualTo($pattern['xml_result']);
  }


  static function getTestPattern($file = '/xml_tests.yml', $key = NULL) {
    $loaded = Yaml::parse(file_get_contents(RESOURCES_DIR . $file));

    return ($key) ? $loaded[$key] : $loaded;
  }
}