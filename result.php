<?php

require_once './includes.php';
require_once './config.php';

$username = $_POST{'username'};
$password = $_POST{'password'};
$client = $_POST{'client'};
$fedid = array();

// connect to ldap server
global $ldap_connection_string;
$ldapconn = ldap_connect($ldap_connection_string)
    or die("Could not connect to LDAP server.");

if ($ldapconn) {
#if (true) {
    $ldapbind = ldap_bind($ldapconn, $username, $password);
#    $ldapbind = true;
    if ($ldapbind) {
        $authenticated = true;
        $fedid = generate_fedid($client, $username);
        $sessionString = urlencode('{"sessionId":"'.$fedid['sessionId'].'","sessionKey":"'.$fedid['sessionKey'].'","sessionToken":"'.$fedid['sessionToken'].'"}');
        $url = "https://signin.aws.amazon.com/federation?Action=getSigninToken&Session=$sessionString";
        $json_response = http_parse_message(http_get($url))->body;
        if(preg_match("/{\"SigninToken\":\"(.*)\"}/", $json_response, $matches)) {
          $signinToken = $matches[1];
          $loginurl = "https://signin.aws.amazon.com/federation?Action=login&Issuer=" . urlencode("http://www.appliedtrust.com") . "&Destination=" . urlencode("https://console.aws.amazon.com") . "&SigninToken=" . $signinToken;
        }
    } else {
        $authenticated = false;
    }
}


?>

<html>
</body>
<?php if ($authenticated === true) {
  echo "<h3>authentication successful</h3>";
  echo "<a href=$loginurl>click here to sign in to the aws console</a>";
} else {
  echo "<h3>authentication failed</h3>";
  exit;
} ?>
</body>
</html>
