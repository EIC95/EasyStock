<?php
session_start();
require_once "../verify.php";
include '../connection.php';

$userId = $_SESSION['user_id'];
$commandeId = $_GET['id'];

// Fetch order details
$stmt = $conn->prepare("
    SELECT c.id, c.date_commande 
    FROM commandes c 
    WHERE c.id = ? AND c.user_id = ?
");
$stmt->execute([$commandeId, $userId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header('Location: panier.php');
    exit;
}

// Fetch products in the order
$stmt = $conn->prepare("
    SELECT p.nom, p.photo, pc.quantite, pc.prix_total 
    FROM produits_commandes pc 
    JOIN produits p ON pc.produit_id = p.id 
    WHERE pc.commande_id = ?
");
$stmt->execute([$commandeId]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la commande</title>
    <link rel="stylesheet" href="user-style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main-content">
        <h1 class="page-title">Détails de la commande #<?= htmlspecialchars($commande['id']) ?></h1>
        <p class="order-date">Date de commande : <?= htmlspecialchars($commande['date_commande']) ?></p>

        <?php if (empty($produits)): ?>
            <p class="empty-message">Aucun produit dans cette commande.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td class="product-info">
                                    <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="Photo du produit" class="product-image">
                                    <?= htmlspecialchars($produit['nom']) ?>
                                </td>
                                <td><?= htmlspecialchars($produit['quantite']) ?></td>
                                <td><?= number_format($produit['prix_total'], 2) ?> CFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="panier.php" class="back-button">Retour au panier</a>
        </div>
    </main>
</body>
</html>
