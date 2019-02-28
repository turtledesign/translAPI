<?php namespace TranslAPI\Helper;

// TODO: Would probably make sense to remove the dependency on Sabre as we're only making use of a very small part of it & could elide out the conversion.

use \Sabre\Xml\Writer;
use \Sabre\Xml\Service;

class Xml {

  protected 
    $cfg = [
      'indent_str' => '  ',
      'xml_vers'   => '1.0',
      'xml_i18n'   => 'UTF-8',
      'dtd_name'   => 'ndxml', // FIXME: these are hardcoded to netdespatch, but should be from input.
      'dtd_public' => '-//NETDESPATCH//ENTITIES/Latin',
      'dtd_system' => 'ndentity.ent',
      'wrap'       => ['name' => 'ndxml', 'attributes' => ['version' => '2.0']], 
    ];


  function __construct($cfg = []) {
    $this->cfg = array_merge($this->cfg, $cfg);
  }


  function toXml($arr, $cfg = []) {
    $cfg = array_merge($this->cfg, $cfg);
    $writer = self::getLoadedWriter($cfg);

    \Sabre\Xml\Serializer\standardSerializer($writer, self::toSabreArray($arr, $cfg));

    return $writer->outputMemory();
  }


  function fromXml($xml, $cfg = []) {
    $service = new Service;

    return self::fromSabreArray((new Service)->parse($xml, $ns = '')); // blank namespace to strip from responses
  }


  static function toSabreArray($arr, $cfg = []) {
    $sabre = [];
    
    foreach ($arr as $k => $v) {
      $node = ['name' => preg_replace('/\|\d+$/', '', $k)]; // Required to properly allow multiples of the same XML tag.

      if (! is_array($v)) $v = ['_content' => $v];

      if (! empty($v['_attrs'])) $node['attributes'] = array_map(function ($attr) { return (string) $attr; }, $v['_attrs']);  // writeAttribute fails with non-string values.
      
      if (isset($v['_content'])) $node['value'] = $v['_content'];
      elseif ($children = array_diff_key($v, array_flip(['_attrs']))) {
        $node['value'] = self::toSabreArray($children); // Need to watch if adding $cfg here ('wrap' will repeat...)
      }

      $sabre[] = $node;
    }

    return (isset($cfg['wrap'])) 
      ? array_merge($cfg['wrap'], ['value' => $sabre])
      : $sabre; 
  }


  static function fromSabreArray($sabre, $cfg = []) {
    $arr = [];

    foreach ($sabre as $v) {
      $node = ['_attrs' => $v['attributes']];
      $node['_content'] = (is_array($v['value']))
        ? self::fromSabreArray($v['value'])
        : $v['value'];

      $arr[preg_replace('/^{}/', '', $v['name'])] = $node;
    }

    return $arr;
  }


  static function getLoadedWriter($cfg = []) {
    $cfg = array_merge($cfg);

    $writer = new Writer();

    $writer->openMemory();
    $writer->setIndent(true);
    $writer->startDocument($cfg['xml_vers'], $cfg['xml_i18n']);

    if (! empty($cfg['dtd_name'])) {
      $writer->startDTD($cfg['dtd_name'], $cfg['dtd_public'], $cfg['dtd_system']);
      $writer->endDTD();
    }

    return $writer;
  }
}