<?php

namespace RoyalMail\tests\unit\Request;

use atoum;
use \Symfony\Component\Yaml\Yaml;
use \RoyalMail\Connector\xmlConnector;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class Builder extends atoum {

  function testSuccess() {
    $cfg    = Yaml::parse(file_get_contents(RESOURCES_DIR . '/guzzle_mocks.yml'))['success'];
    $client = new Client(['handler' => HandlerStack::create(new MockHandler([(new Response($cfg['code'], $cfg['headers'], $cfg['body']))]))]);

    $response = (new xmlConnector([]))->request('blah', ['client' => $client]);

    $this->string($response)->isEqualTo($cfg['body']);
  }


  function testFailure() {
    $cfg    = Yaml::parse(file_get_contents(RESOURCES_DIR . '/guzzle_mocks.yml'))['error'];
    $client = new Client([ 'handler' => HandlerStack::create( new MockHandler([ (new RequestException($cfg['message'], new Request('GET', 'test'))) ]) ) ]);

    $response = (new xmlConnector([]))->request('blah', ['client' => $client]);
    
    $this->string($response)->isEqualTo($cfg['body']);
  }


  function getMockHandler($payload) {
    
    return  ;
  }

}
