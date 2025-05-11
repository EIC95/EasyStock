<?php
include '../connection.php';
session_start();

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <?php include 'navbar.php'; ?>

    <main class="p-8">
        <h1 class="text-2xl text-violet-400 font-semibold mb-6">Votre panier</h1>

        <?php if (empty($produits)): ?>
            <p class="text-gray-400">Votre panier est vide.</p>
        <?php else: ?>
            <form method="POST">
                <table class="w-full text-left bg-gray-800 rounded-lg shadow border border-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Produit</th>
                            <th class="px-4 py-2">Prix</th>
                            <th class="px-4 py-2">Quantité</th>
                            <th class="px-4 py-2">Disponible</th>
                            <th class="px-4 py-2">Total</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $produit): ?>
                            <tr>
                                <td class="px-4 py-2"><?= htmlspecialchars($produit['nom']) ?></td>
                                <td class="px-4 py-2"><?= number_format($produit['prix'], 2) ?> CFA</td>
                                <td class="px-4 py-2">
                                    <input type="number" name="quantite[<?= $produit['id'] ?>]" value="<?= $produit['quantite'] ?>" min="1" max="<?= $produit['disponible'] ?>" class="w-16 bg-gray-700 text-white rounded">
                                </td>
                                <td class="px-4 py-2"><?= htmlspecialchars($produit['disponible']) ?></td>
                                <td class="px-4 py-2"><?= number_format($produit['prix'] * $produit['quantite'], 2) ?> CFA</td>
                                <td class="px-4 py-2">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                                        <button type="submit" name="delete_product" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="mt-6 flex justify-between">
                    <a href="index.php" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded">Retour à l'accueil</a>
                    <div class="flex gap-4">
                        <button type="submit" name="valider_commande" class="bg-green-600 hover:bg-green-700 px-6 py-3 rounded">Valider la commande</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <h2 class="text-xl text-violet-400 font-semibold mt-12 mb-4">Vos commandes</h2>
        <?php if (empty($commandes)): ?>
            <p class="text-gray-400">Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-700 text-gray-300">
                        <tr>
                            <th class="px-4 py-2">ID Commande</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $commande): ?>
                            <tr class="border-t border-gray-700 hover:bg-gray-700">
                                <td class="px-4 py-2"><?= htmlspecialchars($commande['id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($commande['date_commande']) ?></td>
                                <td class="px-4 py-2">
                                    <a href="commande_details.php?id=<?= $commande['id'] ?>" class="text-blue-400 hover:underline">Voir les détails</a>
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
