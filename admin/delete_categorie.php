<?php
session_start();
include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // Delete the category
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
header("Location: categories.php?page=$page");
exit;
