<?php

include ("../verify.php");


$productId = (int)$_GET['id'];
$stmt = $conn->prepare("
    SELECT p.*, c.nom AS categorie_nom 
    FROM produits p
    LEFT JOIN categories c ON p.categorie = c.id
    WHERE p.id = ?
");
$stmt->execute([$productId]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: index.php');
    exit;
}


$isInCart = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $cartStmt = $conn->prepare("SELECT * FROM panier WHERE user_id = ? AND produit_id = ?");
    $cartStmt->execute([$userId, $productId]);
    $isInCart = $cartStmt->fetch() ? true : false;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
    <link rel="stylesheet" href="userStyle.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main-content">
        <div class="product-details">
            <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="product-image">
            <h1 class="product-title"><?= htmlspecialchars($produit['nom']) ?></h1>
            <p class="product-description"><?= htmlspecialchars($produit['description']) ?></p>
            <p class="product-price"><?= number_format($produit['prix']) ?> CFA</p>
            <p class="product-category"><strong>Catégorie:</strong> <?= htmlspecialchars($produit['categorie']) ?></p>
            <form action="ajouter_panier.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                <button type="submit" class="add-to-cart-button" <?= $isInCart ? 'disabled' : '' ?>>
                    <?= $isInCart ? 'Déjà dans le panier' : 'Ajouter au panier' ?>
                </button>
            </form>
            <a href="index.php" class="back-button">Retour</a>
        </div>
    </main>
</body>
</html>
