<?php

include ("../verify.php");

$commandeId = $_GET['id'] ?? null;
if (!$commandeId) {
    die("Commande introuvable.");
}


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


$stmt = $conn->prepare("
    SELECT p.nom, pc.quantite, pc.prix_total 
    FROM produits_commandes pc 
    JOIN produits p ON pc.produit_id = p.id 
    WHERE pc.commande_id = ?
");
$stmt->execute([$commandeId]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <link rel="stylesheet" href="adminStyle.css">
    <style>
        .facture-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            color: #222;
            border-radius: 12px;
            box-shadow: 0 4px 24px #0002;
            padding: 40px 36px 32px 36px;
            font-size: 1.05rem;
        }
        .facture-container h1 {
            color: #7c3aed;
            text-align: center;
            margin-bottom: 32px;
            font-size: 2.2rem;
        }
        .facture-container .info {
            margin-bottom: 32px;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 18px 22px;
            font-size: 1.08em;
            line-height: 1.7;
        }
        .facture-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .facture-container th, .facture-container td {
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 10px;
            text-align: left;
        }
        .facture-container th {
            background: #ede9fe;
            color: #7c3aed;
            font-size: 1.08em;
        }
        .facture-container tr:last-child td {
            border-bottom: none;
        }
        .facture-container .total {
            text-align: right;
            font-size: 1.25em;
            font-weight: bold;
            color: #7c3aed;
            margin-top: 18px;
        }
        .print-btn {
            display: inline-block;
            margin-bottom: 18px;
            background: #7c3aed;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 28px;
            font-size: 1.08em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .print-btn:hover {
            background: #6d28d9;
        }
        @media print {
            .print-btn {
                display: none !important;
            }
            body {
                background: #fff !important;
            }
            .facture-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 0 0 0 !important;
            }
        }
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
