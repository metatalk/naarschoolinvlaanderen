<?php

  // Require composer autoloader
  require __DIR__ . '/vendor/autoload.php';

  require __DIR__ . '/dotenv-loader.php';

  \Firebase\JWT\JWT::$leeway = 100;

  use Auth0\SDK\Auth0;
  use Auth0\SDK\API\Management;

  $domain        = getenv('AUTH0_DOMAIN');
  $client_id     = getenv('AUTH0_CLIENT_ID');
  $client_secret = getenv('AUTH0_CLIENT_SECRET');
  $redirect_uri  = getenv('AUTH0_CALLBACK_URL');
  $audience      = getenv('AUTH0_AUDIENCE');

  if($audience == ''){
    $audience = 'https://' . $domain . '/userinfo';
  }

  $auth0 = new Auth0([
    'domain' => $domain,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'audience' => $audience,
    'scope' => 'openid profile user_metadata user_id',
    'persist_id_token' => true,
    'persist_access_token' => true,
    'persist_refresh_token' => true,
  ]);

  $userInfo = $auth0->getUser();

  $isSuperAdmin = false;

  if($userInfo['name'] == 'bert@mast-agency.be' || $userInfo['name'] == 'arif.akgonul@ond.vlaanderen.be' || $userInfo['name'] == 'wim.verkammen@ond.vlaanderen.be') {
    $isSuperAdmin = true;
  }

  if(empty($userInfo)) {
    //die('Geen geldige gebruiker.');
  }

  require 'mgmtaccess.php';

  $userData = $mgmt_api->users->get( $userInfo['sub'] );
  $hasAccessTo = !empty($userData['user_metadata']['school']) ? $userData['user_metadata']['school'] : false;
  $canEditUsers = !empty($userData['user_metadata']['editusers']) && $userData['user_metadata']['editusers'] === "1" ? true : false;
  $canEditVestigingen = !empty($userData['user_metadata']['editvestigingen']) && $userData['user_metadata']['editvestigingen'] === "1" ? true : false;
  $userbelongsTo = !empty($userData['user_metadata']['belongsTo']) ? $userData['user_metadata']['belongsTo'] : false;

?>
