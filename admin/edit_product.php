<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    include("../connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $quantite = $_POST['quantite'];
        $prix = $_POST['prix'];
        $code_barre = $_POST['code_barre'];
        $description = $_POST['description'];
        $photo = null;

        if (!empty($_FILES['photo']['name'])) {
            $photo = time() . '_' . $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photo);
        }

        $query = "UPDATE produits SET nom = :nom, quantite = :quantite, prix = :prix, code_barre = :code_barre, description = :description";
        if ($photo) {
            $query .= ", photo = :photo";
        }
        $query .= " WHERE id = :id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':code_barre', $code_barre);
        $stmt->bindParam(':description', $description);
        if ($photo) {
            $stmt->bindParam(':photo', $photo);
        }
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header("Location: product_details.php?id=$id");
        } else {
            $_SESSION['product_errors'] = ["Erreur lors de la modification du produit."];
            header("Location: product_details.php?id=$id");
        }
    }
?>
