<?php

namespace RoyalMail\Response

class baseResponse {





  function hasIssues() { return $this->hasErrors() || $this->hasWarnings(); }


  function hasErrors() { return count($this->getErrors()); }

  function getErrors() {
    return [];
  }

  
  function hasWarnings() { return count($this->getWarnings()); }

  function getWarnings() {
    return [];
  }



  /**
   * Return the formatted response encoded to JSON
   * 
   * @return string 
   */
  function toJSON() {

  }
}