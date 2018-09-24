<?php

namespace RoyalMail\tests\unit\Connector;

use atoum;
use \Symfony\Component\Yaml\Yaml;
use \RoyalMail\Connector\xmlConnector as xmlCon;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class xmlConnector extends atoum {

  function testSuccess() {
    $cfg    = Yaml::parse(file_get_contents(RESOURCES_DIR . '/guzzle_mocks.yml'))['success'];
    $client = new Client(['handler' => HandlerStack::create(new MockHandler([(new Response($cfg['code'], $cfg['headers'], $cfg['body']))]))]);

    $response = (new xmlCon([]))->doRequest('blah', ['client' => $client]);

    $this->string($response)->isEqualTo($cfg['body']);
  }


  function testFailure() {
    $cfg    = Yaml::parse(file_get_contents(RESOURCES_DIR . '/guzzle_mocks.yml'))['error'];
    $client = new Client([ 'handler' => HandlerStack::create( new MockHandler([ (new RequestException($cfg['message'], new Request('GET', 'test'))) ]) ) ]);

    $response = (new xmlCon([]))->doRequest('blah', ['client' => $client]);
    
    $this->string($response)->isEqualTo($cfg['body']);
  }


  function getMockHandler($payload) {
    
    return  ;
  }

}
