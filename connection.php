<?php
// Include configuration
include_once("config.php");

// Check if mysqli extension is loaded
if (!extension_loaded('mysqli')) {
    die("MySQLi extension is not loaded. Please enable it in your PHP configuration.");
}

$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$database = new mysqli($servername, $username, $password, $dbname);

if ($database->connect_error) {
    die("Tizimga kirishda xatolik yuz berdi :" . $database->connect_error);
}
?>

