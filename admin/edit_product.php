<?php
    session_start();

    include("../connection.php");

    $productId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $quantite = $_POST['quantite'];
        $prix = $_POST['prix'];
        $code_barre = $_POST['code_barre'];
        $categorie = $_POST['categorie'];
        $description = $_POST['description'];
        $photo = $product['photo'];

        if (!empty($_FILES['photo']['name'])) {
            $photo = "../uploads/produits/" . $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
        }

        $stmt = $conn->prepare("UPDATE produits 
                                SET nom = :nom, quantite = :quantite, prix = :prix, code_barre = :code_barre, 
                                    categorie = :categorie, description = :description, photo = :photo 
                                WHERE id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':code_barre', $code_barre);
        $stmt->bindParam(':categorie', $categorie);
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

    // Fetch categories for the dropdown
    $categories_stmt = $conn->query("SELECT id, nom FROM categories");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
