<?php

include ("../verify.php");

$commandeId = $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_commande'])) {
    // Récupérer les produits et quantités de la commande
    $stmt = $conn->prepare("SELECT produit_id, quantite FROM produits_commandes WHERE commande_id = ?");
    $stmt->execute([$commandeId]);
    $produits_commande = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Remettre à jour les quantités dans la table produits
    foreach ($produits_commande as $item) {
        $stmt_update = $conn->prepare("UPDATE produits SET quantite = quantite + :qte WHERE id = :pid");
        $stmt_update->bindValue(':qte', $item['quantite'], PDO::PARAM_INT);
        $stmt_update->bindValue(':pid', $item['produit_id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    // Supprimer la commande
    $stmt = $conn->prepare("DELETE FROM commandes WHERE id = ?");
    $stmt->execute([$commandeId]);
    header('Location: commandes.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etat'])) {
    $etat = $_POST['etat'];
    $stmt = $conn->prepare("UPDATE commandes SET etat = ? WHERE id = ?");
    $stmt->execute([$etat, $commandeId]);
}


$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, c.etat, u.nom AS user_nom, u.login AS user_login 
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header('Location: commandes.php');
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
    <title>Détails de la commande</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Détails de la commande #<?= htmlspecialchars($commande['id']) ?></h1>
        <p class="text-muted mb-4">Date de commande : <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p class="text-muted mb-4">Client : <?= htmlspecialchars($commande['user_nom']) ?> (<?= htmlspecialchars($commande['user_login']) ?>)</p>
        <?php if ($commande['etat'] !== 'Livrée' && $commande['etat'] !== 'Annulée'): ?>
        <form method="POST" class="form-inline mb-4">
            <label for="etat" class="text-muted">État de la commande :</label>
            <select name="etat" id="etat" class="select">
                <option value="Non pris en charge" <?= $commande['etat'] === 'Non pris en charge' ? 'selected' : '' ?>>Non pris en charge</option>
                <option value="En cours de livraison" <?= $commande['etat'] === 'En cours de livraison' ? 'selected' : '' ?>>En cours de livraison</option>
            </select>
            <button type="submit" class="btn-primary">Mettre à jour l'état</button>
        </form>
        <?php else: ?>
        <div class="order-status mb-4">
            <span class="text-muted">État de la commande :</span>
            <span class="badge <?= $commande['etat'] === 'Livrée' ? 'badge-success' : 'badge-danger' ?>">
                <?= htmlspecialchars($commande['etat']) ?>
            </span>
        </div>
        <?php endif; ?>
        <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette commande ?');" class="mb-6">
            <button type="submit" name="delete_commande" class="btn-danger">Supprimer la commande</button>
        </form>

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
            <a href="commandes.php" class="btn-secondary">Retour à la liste des commandes</a>
        </div>
    </main>
</body>
</html>
