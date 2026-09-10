<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "vatancom";

// MySQLi bağlantısı
$conn = new mysqli($host, $username, $password, $database);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Türkçe karakter desteği
$conn->set_charset("utf8");

// Oturum kontrolü
function checkAuth()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function checkAdmin()
{
    if ($_SESSION['rol'] !== 'admin') {
        header("Location: login.php");
        exit();
    }
}
