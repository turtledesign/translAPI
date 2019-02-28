<?php

namespace TranslAPI\Exception;

abstract class TranslAPIException extends \Exception {

  protected $error_list = [];


  function withErrors($errors) { 
    $this->error_list = $errors; 

    $this->message = "Errors found with the following request keys: " . implode(', ', $this->error_list);

    return $this;
  }


  function getErrors() { return $this->error_list; }


  function toJSON() {

  }
}
