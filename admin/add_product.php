<?php
    
    include ("../verify.php");
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $quantite = $_POST['quantite'];
        $prix = $_POST['prix'];
        $code_barre = $_POST['code_barre'];
        $fournisseur = $_POST['fournisseur'];
        $categorie = $_POST['categorie'];
        $description = $_POST['description'];
        $photo = "../uploads/produits/default.jpg";

        if (!empty($_FILES['photo']['name'])) {
            $uploadDir = "../uploads/produits/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('prod_', true) . '.' . $extension;
            $photoPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
                $photo = $photoPath;
            }
        }

        $stmt = $conn->prepare("INSERT INTO produits (nom, quantite, prix, code_barre, fournisseur, categorie, description, photo) 
                                VALUES (:nom, :quantite, :prix, :code_barre, :fournisseur, :categorie, :description, :photo)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':code_barre', $code_barre);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':categorie', $categorie);
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
