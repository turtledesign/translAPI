<?php

namespace TranslAPI\tests\unit;

use atoum;
use \TranslAPI\TranslAPI as Service;
use \Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class TranslAPI extends atoum {

  use \TranslAPI\tests\lib\TestDataLoader;


  function testBuild() {
    $setup   = $this->getTestSchema('requests/createNewJob');

    $valid = $setup['valid'];
    $built = (new Service)->buildRequest('createNewJob', $valid['request']);

    $this->array($built)->isEqualTo($valid['expect']);
  }


  function testAll() {
    $setup        = $this->getTestSchema('requests/createNewJob');
    $response_cfg = Yaml::parse(file_get_contents(RESOURCES_DIR . '/guzzle_mocks.yml'))['success'];
    $client       = new Client(['handler' => HandlerStack::create(new MockHandler([(new Response($response_cfg['code'], $response_cfg['headers'], $response_cfg['body']))]))]);

    $valid = $setup['valid'];
    $response = (new Service)->createNewJob($valid['request'], ['client' => $client]);

    $this->boolean($response->succeeded())->isTrue();

    $this->array($response->asArray())->isEqualTo(['id' => 1, 'reference' => '4574z1539', 'consignment' => 'XXXXXXXXXXXX', 'label_url' => 'http://........']);
  }

}
