<?php
require 'vendor/autoload.php'; // Load Google API Client Library
session_start();

$client = new Google\Client();
$client->setClientId('72486144331-gsohvk6unirrepcer5g2hekughpmf72l.apps.googleusercontent.com'); 
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/expense-tracker/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

$login_url = $client->createAuthUrl();
header("Location: " . $login_url);
exit;
?>
