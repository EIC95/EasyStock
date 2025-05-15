<?php

include ("../verify.php");


$userId = $_SESSION['user_id'];
$commandeId = $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler_commande'])) {
    // Remettre à jour les quantités des produits de la commande
    $stmt = $conn->prepare("SELECT produit_id, quantite FROM produits_commandes WHERE commande_id = ?");
    $stmt->execute([$commandeId]);
    $produits_commande = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($produits_commande as $item) {
        $stmt_update = $conn->prepare("UPDATE produits SET quantite = quantite + :qte WHERE id = :pid");
        $stmt_update->bindValue(':qte', $item['quantite'], PDO::PARAM_INT);
        $stmt_update->bindValue(':pid', $item['produit_id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    $stmt = $conn->prepare("UPDATE commandes SET etat = 'Annulée' WHERE id = ? AND user_id = ?");
    $stmt->execute([$commandeId, $userId]);
    header("Location: panier.php");
    exit;
}


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
    <link rel="stylesheet" href="userStyle.css">
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
            <?php
            
            $stmt = $conn->prepare("SELECT etat FROM commandes WHERE id = ? AND user_id = ?");
            $stmt->execute([$commandeId, $userId]);
            $etat = $stmt->fetchColumn();
            if ($etat !== 'Annulée' && $etat !== 'Livrée') : ?>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="annuler_commande" class="delete-button" onclick="return confirm('Voulez-vous vraiment annuler cette commande ?');">
                        Annuler la commande
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
