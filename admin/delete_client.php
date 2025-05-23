<?php
    include ("../verify.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_id'])) {
        

        $client_id = (int) $_POST['client_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $client_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: clients.php?page=" . $page);
    exit();
?>