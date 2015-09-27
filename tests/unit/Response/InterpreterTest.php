<?php

namespace RoyalMail\tests\unit\Response;

use atoum;
use \RoyalMail\Response\Interpreter as Inter;
use \RoyalMail\Connector\soapConnector as Soap;
use \Symfony\Component\Yaml\Yaml;
use \stdClass as Obj;

class Interpreter extends atoum {

  use \RoyalMail\tests\lib\TestDataLoader;

  function testValueExtraction() {
    $mock_response = $this->getMockResponse();

    $this->string(Inter::extractValue($mock_response, ['_extract' => 'first']))->isEqualTo('Who');
    $this->array(Inter::extractValue($mock_response,  ['_extract' => 'first', '_multiple' => TRUE]))->hasSize(1)->contains('Who');
    $this->array(Inter::extractValue($mock_response,  ['_extract' => 'field', '_multiple' => TRUE]))->hasSize(2);

    $this->array(Inter::addProperty(
      [], 
      ['where' => ['_extract' => 'position'], 'who'   => ['_extract' => 'player']], 'field', 
      NULL, 
      [], 
      ['source' => $mock_response->field[0]]
    ))->isEqualTo(['field' => ['where' => 'left', 'who' => 'Why']]);


  }


  function testIntepretation() {
    $schema = [
      'properties' => ['First'     => ['_extract' => 'first']],
    ];

    $this->array(Inter::processSchema($schema, $this->getMockResponse()))->isEqualTo(['First' => 'Who']);

    $mock_response = $this->getMockResponse();

    $this->array(Inter::processSchema(
      ['properties' => ['where' => ['_extract' => 'position'], 'who'   => ['_extract' => 'player']]],
      $mock_response->field[0]
    ))->isEqualTo(['where' => 'left', 'who' => 'Why']);


    $nested_schema = [
      'properties' => [
        'field' => [
          '_extract' => 'field', 
          '_multiple' => TRUE,
          'where' => ['_extract' => 'position'],
          'who'   => ['_extract' => 'player'],
        ]
      ]
    ];

    $this
      ->array(Inter::processSchema($nested_schema, $mock_response))
      ->isEqualTo([
        'field' => [
          ['where' => 'left', 'who' => 'Why'],
          ['where' => 'center', 'who' => 'Because'],
        ]
      ]);
  }


  function testResponseConversions() {
    $requests = ['cancelShipment', 'createManifest'];
    $verify   = $this->getTestSchema('response_interpretation');

    foreach ($requests as $req) {
      $expect = $verify[$req];
      $test   = $this->getTestRequest($req);

      $response = (new Soap())
                    ->setSoapClient($this->getMockSoapClient())
                    ->doRequest($req, $test['request']);
      
      $this->string($response->integrationHeader->version)->isEqualTo("2");

      $this
        ->given($this->newTestedInstance)
        ->object($response = $this->testedInstance->loadResponse($req, $response, ['params' => ['text_only' => TRUE]]));

      $this->array($response->getSecurityInfo())->isEqualTo($expect['security']);
      $this->array($response->getResponse())->isEqualTo($expect['response']);

    }
  }



  function testPostFilters() {
    $dates_schema = [
      'properties' => [
        'today'    => ['_extract' => 'roles/catcher', '_post_filter' => 'ObjectifyDate'],
        'tomorrow' => ['_extract' => 'roles/catcher', '_post_filter' => 'ObjectifyDate'],
      ]
    ];

    $this->array($response = Inter::processSchema($dates_schema, $this->getMockResponse()))->hasSize(2);

    $this->object($response['today'])->string($response['today']->format('d'))->isEqualTo(date_create()->format('d'));
  }



  function getMockResponse() {
    $field_left = new Obj;
    $field_left->position = 'left';
    $field_left->player   = 'Why';

    $field_center = new Obj;
    $field_center->position = 'center';
    $field_center->player   = 'Because';

    $response = new Obj;
    $response->first  = 'Who';
    $response->second = 'What';
    $response->third  = 'I Don\'t know';
    
    $response->field = [$field_left, $field_center];

    $response->roles = new Obj;
    $response->roles->pitcher   = date_create()->add(new \DateInterval('P1D'))->format('Y-m-d');
    $response->roles->catcher   = date_create()->format('Y-m-d');
    $response->roles->shortstop = 'I Don\'t Give a Darn';

    return $response;
  }
}