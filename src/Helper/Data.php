<?php

namespace TranslAPI\Helper;

use \Symfony\Component\Yaml\Yaml;

/**
 * Helper to get value options &c.
 * 
 * 
 */
class Data extends \ArrayObject {
  function __construct($init = []) {
    return parent::__construct(array_merge([
      'shipment_types' => ['Delivery' => 'Delivery', 'Return' => 'Return'],
    ], $init));
  }


  function offsetGet($key) {
    if (! $this->offsetExists($key)) $this->loadData($key);

    return parent::offsetGet($key);
  }


  function setDefaultOverride($key, $value) {
    if (empty($this['override_defaults'])) $this['override_defaults'] = [];

    $this['override_defaults'][$key] = $value;

    return $this;
  }


  protected function loadData($key) {
    $this->offsetSet($key, Yaml::parse(file_get_contents(dirname(__FILE__) . '/../../data/' . $key . '.yml')));

    return $this;
  }
}