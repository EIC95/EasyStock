<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    include("../connection.php");

    $id = $_POST['id'] ?? null;
    $page = $_GET['page'] ?? 1;

    if ($id) {
        $query = "DELETE FROM fournisseurs WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
    }

    header("Location: fournisseurs.php?page=$page");
    exit();
?>
