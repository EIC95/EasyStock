<?php
    
    include ("../verify.php");

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productId = $_POST['id'];
    } else {
        $productId = $_GET['id'];
    }

    $stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $quantite = $_POST['quantite'];
        $prix = $_POST['prix'];
        $code_barre = $_POST['code_barre'];
        $categorie = $_POST['categorie'];
        $fournisseur = $_POST['fournisseur'];
        $description = $_POST['description'];
        $photo = $product['photo'];

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

        $stmt = $conn->prepare("UPDATE produits 
                                SET nom = :nom, quantite = :quantite, prix = :prix, code_barre = :code_barre, 
                                    categorie = :categorie, fournisseur = :fournisseur, description = :description, photo = :photo 
                                WHERE id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':code_barre', $code_barre);
        $stmt->bindParam(':categorie', $categorie);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':photo', $photo);
        $stmt->bindParam(':id', $productId);

        if ($stmt->execute()) {
            header("Location: product_details.php?id=$productId");
        } else {
            $_SESSION['product_errors'] = ["Erreur lors de la mise à jour du produit."];
            header("Location: edit_product.php?id=$productId");
        }
    }
?>
