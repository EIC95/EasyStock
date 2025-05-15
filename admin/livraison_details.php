<?php

include ("../verify.php");

$commandeId = $_GET['id'] ?? null;
if (!$commandeId) {
    header('Location: livraisons.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_livree'])) {
    $stmt = $conn->prepare("UPDATE commandes SET etat = 'Livrée' WHERE id = ?");
    $stmt->execute([$commandeId]);
    header("Location: livraisons.php");
    exit;
}


$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, c.etat, u.nom AS user_nom, u.prenom AS user_prenom, u.login AS user_login 
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header('Location: livraisons.php');
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la livraison</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Livraison commande #<?= htmlspecialchars($commande['id']) ?></h1>
        <p class="text-muted mb-4">Date de commande : <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p class="text-muted mb-4">Client : <?= htmlspecialchars($commande['user_prenom']) . " " . htmlspecialchars($commande['user_nom']) ?> (<?= htmlspecialchars($commande['user_login']) ?>)</p>
        <div class="order-status mb-4">
            <span class="text-muted">État de la commande :</span>
            <span class="badge badge-warning"><?= htmlspecialchars($commande['etat']) ?></span>
        </div>
        <?php if ($commande['etat'] === 'En cours de livraison'): ?>
        <form method="POST" class="mb-6">
            <button type="submit" name="set_livree" class="btn-success">Marquer comme Livrée</button>
        </form>
        <?php endif; ?>

        <?php if (empty($produits)): ?>
            <p class="text-muted">Aucun produit dans cette commande.</p>
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
                                <td>
                                    <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="Photo du produit" class="product-photo-small">
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

        <div class="mt-6">
            <a href="livraisons.php" class="btn-secondary">Retour à la liste des livraisons</a>
        </div>
    </main>
</body>
</html>
