<?php

use \Auth0\SDK\API\Authentication;
use \Auth0\SDK\Exception\ApiException;
use \GuzzleHttp\Exception\ClientException;
use Auth0\SDK\API\Management;

function getManagementAccessToken() {
  $auth0_api = new Authentication( getenv('AUTH0_DOMAIN') );

  $config = [
      'client_secret' => getenv('AUTH0_CLIENT_SECRET'),
      'client_id' => getenv('AUTH0_CLIENT_ID'),
      'audience' => getenv('AUTH0_MANAGEMENT_AUDIENCE'),
  ];

  try {
      $result = $auth0_api->client_credentials($config);
      return $result['access_token'];
  } catch (ClientException $e) {
      echo 'Caught: ClientException - ' . $e->getMessage();
  } catch (ApiException $e) {
      echo 'Caught: ApiException - ' . $e->getMessage();
  }
}

$access_token = getManagementAccessToken();

$mgmt_api = new Management( $access_token, getenv('AUTH0_DOMAIN') );
