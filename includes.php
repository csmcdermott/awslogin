<?php

require_once './config.php';
require_once '/usr/local/aws/sdk/sdk.class.php';

function list_clients() {
  global $client_config_dir;
  $clients = array();
  if ($handle = opendir($client_config_dir)) {
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != "..") {
        array_push($clients, $entry);
      }
    }
  closedir($handle);
  }
  return $clients;
}

function generate_fedid($client, $username) {
  $options = read_client_config($client);
  $sts = new AmazonSTS($options);
  $federation_options = array(
    'Policy' => '{
      "Statement": [{
        "Effect": "Allow",
        "Action": "*",
        "Resource": "*"
      }]
    }',
  );
  $sts_result = $sts->get_federation_token($username, $federation_options);
  if(!$sts_result->isOK()) {
    echo "Failed to get federation token from Amazon: " . $sts_result->body->Error->Message[0] . "</br>";
    exit(2);
  }
  return array(
    'sessionId' => $sts_result->body->GetFederationTokenResult->Credentials->AccessKeyId,
    'sessionKey' => $sts_result->body->GetFederationTokenResult->Credentials->SecretAccessKey,
    'sessionToken'    => $sts_result->body->GetFederationTokenResult->Credentials->SessionToken,
  );
}

function read_client_config($client) {
  global $client_config_dir;
  $options = array();
  $access_key;
  $secret_key;
  if ($handle = fopen("$client_config_dir/$client", 'r')) {
    while (!feof($handle)) {
      $entry = fgets($handle);
      if (preg_match("/^export EC2_ACCESS_KEY=\"??(.*)\"??$/U", $entry, $matches)) {
        $access_key = $matches[1];
      } elseif (preg_match("/^export EC2_SECRET_ACCESS_KEY=\"??(.*)\"??$/U", $entry, $matches)) {
        $secret_key = $matches[1];
      }
    }
  } else {
    echo "Could not open $client_config_dir/$client for reading.";
    exit(2);
  }
  if (isset($access_key) && isset($secret_key)) {
    $options = array(
      'key' => $access_key,
      'secret' => $secret_key,
    );
  } else {
    echo "Could not parse key and secret from $client_config_dir/$client.";
    exit(2);
  }
  fclose($handle);
  return $options;
}

?>
