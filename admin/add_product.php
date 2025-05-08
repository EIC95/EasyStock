<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    include("../connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $quantite = $_POST['quantite'];
        $prix = $_POST['prix'];
        $code_barre = $_POST['code_barre'];
        $fournisseur = $_POST['fournisseur'];
        $description = $_POST['description'];
        $photo = null;

        if (!empty($_FILES['photo']['name'])) {
            $photo = $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/produits/" . $photo);
        }

        $stmt = $conn->prepare("INSERT INTO produits (nom, quantite, prix, code_barre, fournisseur, description, photo) 
                                VALUES (:nom, :quantite, :prix, :code_barre, :fournisseur, :description, :photo)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':code_barre', $code_barre);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':photo', $photo);

        if ($stmt->execute()) {
            header("Location: stocks.php");
        } else {
            $_SESSION['product_errors'] = ["Erreur lors de l'ajout du produit."];
            header("Location: stocks.php");
        }
    }
?>
