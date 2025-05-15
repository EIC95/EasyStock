<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include("../connection.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
?>
