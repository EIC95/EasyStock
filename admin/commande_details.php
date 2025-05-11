<?php
session_start();
include("../connection.php");

$commandeId = $_GET['id'];

// Fetch order details
$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, u.nom AS user_nom, u.login AS user_login 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la commande</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Détails de la commande #<?= htmlspecialchars($commande['id']) ?></h1>
        <p class="text-gray-400 mb-4">Date de commande : <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p class="text-gray-400 mb-4">Client : <?= htmlspecialchars($commande['user_nom']) ?> (<?= htmlspecialchars($commande['user_login']) ?>)</p>

        <?php if (empty($produits)): ?>
            <p class="text-gray-400">Aucun produit dans cette commande.</p>
        <?php else: ?>
            <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-700 text-gray-300">
                        <tr>
                            <th class="px-4 py-2">Produit</th>
                            <th class="px-4 py-2">Quantité</th>
                            <th class="px-4 py-2">Prix Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $produit): ?>
                            <tr class="border-t border-gray-700 hover:bg-gray-700">
                                <td class="px-4 py-2 flex items-center gap-4">
                                    <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="Photo du produit" class="w-16 h-16 object-cover rounded">
                                    <?= htmlspecialchars($produit['nom']) ?>
                                </td>
                                <td class="px-4 py-2"><?= htmlspecialchars($produit['quantite']) ?></td>
                                <td class="px-4 py-2"><?= number_format($produit['prix_total'], 2) ?> CFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="mt-6">
            <a href="commandes.php" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded">Retour à la liste des commandes</a>
        </div>
    </main>
</body>
</html>
