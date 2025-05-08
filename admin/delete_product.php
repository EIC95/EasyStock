<?php
    session_start();

    include("../connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM produits WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header("Location: stocks.php");
        } else {
            $_SESSION['product_errors'] = ["Erreur lors de la suppression du produit."];
            header("Location: stocks.php");
        }
    }
?>
