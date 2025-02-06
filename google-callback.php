<?php
require 'vendor/autoload.php';
session_start();
require 'db.php'; // Include your database connection

$client = new Google\Client();
$client->setClientId('72486144331-gsohvk6unirrepcer5g2hekughpmf72l.apps.googleusercontent.com'); 
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/expense-tracker/google-callback.php');

if (isset($_GET['code'])) {
    // Fetch the access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Handle any errors with the token
    if (isset($token['error'])) {
        die('Error fetching the access token: ' . $token['error']);
    }

    // Set the access token
    $client->setAccessToken($token);

    // Fetch user info from Google
    $oauth = new Google\Service\Oauth2($client);
    $user_info = $oauth->userinfo->get();

    $google_id = $user_info->id;
    $name = $user_info->name;
    $email = $user_info->email;
    $picture = $user_info->picture;

    // Check if the user exists in the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = :google_id");
    $stmt->execute(['google_id' => $google_id]);
    $user = $stmt->fetch();

    if (!$user) {
        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, picture) VALUES (:google_id, :name, :email, :picture)");
        $stmt->execute(['google_id' => $google_id, 'name' => $name, 'email' => $email, 'picture' => $picture]);
        $user_id = $pdo->lastInsertId(); // Get the new user's ID
    } else {
        // User already exists, fetch their user_id
        $user_id = $user['id'];
    }

    // Store the user data in the session
    $_SESSION['user'] = [
        'id' => $user_id,
        'name' => $name,
        'email' => $email,
        'picture' => $picture
    ];

    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);

    // Redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>
