<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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
    $stmt = $conn->prepare("
        INSERT INTO commandes (user_id, produit_id, quantite, prix_total) 
        SELECT c.user_id, c.produit_id, c.quantite, (c.quantite * p.prix) 
        FROM panier c 
        JOIN produits p ON c.produit_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);

    $stmt = $conn->prepare("DELETE FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);

    header('Location: confirmation.php');
    exit;
}
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
    </main>
</body>
</html>
