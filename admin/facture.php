<?php
session_start();
include("../connection.php");

$commandeId = $_GET['id'] ?? null;
if (!$commandeId) {
    die("Commande introuvable.");
}

// Fetch order details
$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, u.nom AS user_nom, u.prenom AS user_prenom, u.login AS user_login, u.adresse, u.tel
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ? AND c.etat = 'Livrée'
");
$stmt->execute([$commandeId]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    die("Commande non trouvée ou non livrée.");
}

// Fetch products in the order
$stmt = $conn->prepare("
    SELECT p.nom, pc.quantite, pc.prix_total 
    FROM produits_commandes pc 
    JOIN produits p ON pc.produit_id = p.id 
    WHERE pc.commande_id = ?
");
$stmt->execute([$commandeId]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total
$total = 0;
foreach ($produits as $produit) {
    $total += $produit['prix_total'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #<?= htmlspecialchars($commande['id']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #fff; color: #222; margin: 0; padding: 0; }
        .facture-container { max-width: 700px; margin: 30px auto; background: #f9f9f9; border: 1px solid #ddd; padding: 32px; border-radius: 8px; }
        h1 { color: #7c3aed; }
        .info, .total { margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; }
        th { background: #eee; }
        .total { font-size: 1.2em; font-weight: bold; }
        .print-btn { display: inline-block; margin-bottom: 24px; padding: 8px 24px; background: #7c3aed; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="facture-container">
        <button class="print-btn" onclick="window.print()">Imprimer la facture</button>
        <h1>Facture #<?= htmlspecialchars($commande['id']) ?></h1>
        <div class="info">
            <strong>Date :</strong> <?= htmlspecialchars($commande['date_commande']) ?><br>
            <strong>Client :</strong> <?= htmlspecialchars($commande['user_prenom']) . " " . htmlspecialchars($commande['user_nom']) ?><br>
            <strong>Login :</strong> <?= htmlspecialchars($commande['user_login']) ?><br>
            <strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse']) ?><br>
            <strong>Téléphone :</strong> <?= htmlspecialchars($commande['tel']) ?><br>
        </div>
        <table>
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
                        <td><?= htmlspecialchars($produit['nom']) ?></td>
                        <td><?= htmlspecialchars($produit['quantite']) ?></td>
                        <td><?= number_format($produit['prix_total'], 2) ?> CFA</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">
            Total à payer : <?= number_format($total, 2) ?> CFA
        </div>
    </div>
</body>
</html>
