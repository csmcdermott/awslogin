<?php

function list_clients() {
  echo "\n";
  echo "The following is a list of all supported clients:\n";
  $clients = array();
  if ($handle = opendir('/usr/local/aws/configs/envs')) {
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != "..") {
        array_push($clients, $entry);
      }
    }
  closedir($handle);
  }
  return $clients;
}

#function read_client_config($client) {
#  $options = array();
#  $access_key;
#  $secret_key;
#  if ($handle = fopen("/usr/local/aws/configs/envs/$client", 'r')) {
#    while (!feof($handle)) {
#      $entry = fgets($handle);
#      if (preg_match("/^export EC2_ACCESS_KEY=\"??(.*)\"??$/U", $entry, $matches)) {
#        $access_key = $matches[1];
#      } elseif (preg_match("/^export EC2_SECRET_ACCESS_KEY=\"??(.*)\"??$/U", $entry, $matches)) {
#        $secret_key = $matches[1];
#      }
#    }
#  } else {
#    logthis("ERROR", "Could not open $client config file - /usr/local/aws/configs/envs/$client!");
#    exit(2);
#  }
#  if (isset($access_key) && isset($secret_key)) {
#    $options = array(
#      'key' => $access_key,
#      'secret' => $secret_key,
#    );
#  } else {
#    logthis("ERROR", "Could not parse access key and secret access key from client config file!");
#    exit(2);
#  }
#  fclose($handle);
#  return $options;
#}

?>
