<?php
session_start();
require_once "../verify.php";
include '../connection.php';

$userId = $_SESSION['user_id'];

// Fetch product details for items in the cart
$stmt = $conn->prepare("
    SELECT p.id, p.nom, p.prix, p.photo, p.quantite AS disponible, c.quantite 
    FROM panier c 
    JOIN produits p ON c.produit_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle single product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $produitId = $_POST['produit_id'];
    $stmt = $conn->prepare("DELETE FROM panier WHERE user_id = ? AND produit_id = ?");
    $stmt->execute([$userId, $produitId]);
    header('Location: panier.php');
    exit;
}

// Handle order validation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    // Insert a new order into the commandes table
    $stmt = $conn->prepare("INSERT INTO commandes (user_id, date_commande) VALUES (?, NOW())");
    $stmt->execute([$userId]);
    $commandeId = $conn->lastInsertId();

    // Insert products into produits_commandes table
    $stmt = $conn->prepare("
        INSERT INTO produits_commandes (commande_id, produit_id, quantite, prix_total) 
        SELECT ?, c.produit_id, c.quantite, (c.quantite * p.prix) 
        FROM panier c 
        JOIN produits p ON c.produit_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$commandeId, $userId]);

    // Clear the cart
    $stmt = $conn->prepare("DELETE FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);

    header('Location: panier.php');
    exit;
}

// Fetch the user's orders
$stmt = $conn->prepare("SELECT id, date_commande FROM commandes WHERE user_id = ? ORDER BY date_commande DESC");
$stmt->execute([$userId]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="user-style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main-content">
        <h1 class="page-title">Votre panier</h1>

        <?php if (empty($produits)): ?>
            <p class="empty-message">Votre panier est vide.</p>
        <?php else: ?>
            <form class="cart-form" method="POST">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Disponible</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produits as $produit): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produit['nom']) ?></td>
                                    <td><?= number_format($produit['prix'], 2) ?> CFA</td>
                                    <td>
                                        <input type="number" name="quantite[<?= $produit['id'] ?>]" value="<?= $produit['quantite'] ?>" min="1" max="<?= $produit['disponible'] ?>" class="input-number">
                                    </td>
                                    <td><?= htmlspecialchars($produit['disponible']) ?></td>
                                    <td><?= number_format($produit['prix'] * $produit['quantite'], 2) ?> CFA</td>
                                    <td>
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                                        <button type="submit" name="delete_product" class="delete-button">Supprimer</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="actions">
                    <a href="index.php" class="back-button">Retour à l'accueil</a>
                    <button type="submit" name="valider_commande" class="validate-button">Valider la commande</button>
                </div>
            </form>
        <?php endif; ?>

        <h2 class="section-title">Vos commandes</h2>
        <?php if (empty($commandes)): ?>
            <p class="empty-message">Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td><?= htmlspecialchars($commande['id']) ?></td>
                                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                                <td>
                                    <a href="commande_details.php?id=<?= $commande['id'] ?>" class="details-link">Voir les détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
