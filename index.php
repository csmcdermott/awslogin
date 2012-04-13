<?php

putenv('TZ=America/Denver');
require_once './config.php';
require_once './includes.php';
require_once '/usr/local/aws/php-sdk/sdk.class.php';

$clients = list_clients();

?>

<html>
<body>
  <h2>awslogin</h2></br>
  <h3>select an aws account:</h3></br>
  <select>
    <?php foreach ($clients as $client) {
      echo "<option>$client</option></br>";
    } ?>
  </select></br>
  <h3>please enter your active directory credentials</h3></br>
  <form method="post" action="/result.php">
    username: <input type="text" size="20" maxlength="40" name="username"></br>
    password: <input type="password" size="20" maxlength="75" name="password"></br>
    <input type="submit" value="authenticate">
  </form>
</body>
</html>
