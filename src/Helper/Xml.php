<?php namespace RoyalMail\Helper;

// TODO: Would probably make sense to remove the dependency on Sabre as we're only making use of a very small part of it & could elide out the conversion.

use \Sabre\Xml\Writer;

class Xml {

  protected 
    $cfg = [
      'indent_str' => '  ',
      'xml_vers'   => '1.0',
      'xml_i18n'   => 'UTF-8',
      'dtd_name'   => 'ndxml',
      'dtd_public' => '-//NETDESPATCH//ENTITIES/Latin',
      'dtd_system' => 'ndentity.ent',
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


  static function toSabreArray($arr, $cfg = []) {
    $sabre = [];
    
    foreach ($arr as $k => $v) {
      $node = ['name' => $k];

      if (! is_array($v)) $v = ['_content' => $v];

      if (! empty($v['_attrs'])) $node['attributes'] = $v['_attrs'];  
      
      if (! empty($v['_content'])) $node['value'] = $v['_content'];
      elseif ($children = array_diff_key($v, array_flip(['_attrs']))) {
        $node['value'] = self::toSabreArray($children);
      }

      $sabre[] = $node;
    }

    return $sabre; 
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