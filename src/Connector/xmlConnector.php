<?php namespace TranslAPI\Connector;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use \TranslAPI\Helper\Xml as XmlHelper;

class xmlConnector extends baseConnector {

  function request($request, $params = [], $config = []) {
    $xml = new XmlHelper;

    $request = $xml->toXml($request);

    if (! isset($params['client'])) $params['client'] = $this->getTransport();

    return $xml->fromXml($this->doRequest($request, $params));
  }





  function doRequest($request, $params = [], $config = []) {
    try {
      $response = $params['client']->request('POST', $this->getEndpoint(), [
        'body'    => $request,
        'headers' => [
          'User-Agent'   => 'NDHttp-PHP/0.5 (compatible; MSIE 5.5; Linux)',
          'Accept'       => '*/*',
          'Pragma'       => 'no-cache',
          'Connection'   => 'Close',
          'Content-Type' => 'text/xml; charset=utf-8',
          'Referer'      => '3Linx;ShippingBackend',
        ],
      ]);

      return (string) $response->getBody();

    } catch (RequestException $e) { // Duplicate the ND xml format, but with the connection error.
      return '<?xml version="1.0" encoding="UTF-8"?><ndxml version="2.0"><status code="ERROR" errorCode="' . $e->getCode() . '">' . htmlspecialchars($e->getMessage()) . '</status></ndxml>';
    }
  }


  function getTransport() {
    return new Client();
  }


  function getEndpoint() {
    return (defined('ND_TEST_MODE'))
      ? 'http://www.vtp.netdespatch.com/NDServe/XAServer'
      : 'http://xmlapi.emea.netdespatch.com/NDServe/XAServer';
  }

  function getAPIFormattedRequest() {
  }
  
}
