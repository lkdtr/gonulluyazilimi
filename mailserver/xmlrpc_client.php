<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');

use Laminas\XmlRpc\Client as Laminas_XmlRpc_Client;
$xmlrpc = new Laminas_XmlRpc_Client('https://POSTFİXADMIN/xmlrpc.php');


$email_address = "API USER";
$password = "API USER PASS";

$email = "ALİAS EMAİL ADDRESS";
$forward = "TARGET";

$http_client = $xmlrpc->getHttpClient();
//$http_client->setCookieJar();

$login_object = $xmlrpc->getProxy('login');
$success = $login_object->login($email_address, $password);

if($success) {
echo "We're logged in\n";
}
else {
die("Auth failed\n");
}

$alias = $xmlrpc->getProxy('alias');

$result = $alias->create($email, $forward);
var_dump($result);


