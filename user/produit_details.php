<?php
session_start();
include '../connection.php';

$productId = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$productId]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: index.php');
    exit;
}

// Check if the product is already in the cart from the database
$isInCart = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $cartStmt = $conn->prepare("SELECT * FROM panier WHERE user_id = ? AND produit_id = ?");
    $cartStmt->execute([$userId, $productId]);
    $isInCart = $cartStmt->fetch() ? true : false;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du produit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <?php include 'navbar.php'; ?>

    <main class="p-8">
        <div class="max-w-4xl mx-auto bg-gray-800 rounded-lg p-6 shadow border border-gray-700">
            <img src="../<?= htmlspecialchars($produit['photo']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="w-full h-64 object-cover rounded mb-6">
            <h1 class="text-3xl font-bold text-violet-400 mb-4"><?= htmlspecialchars($produit['nom']) ?></h1>
            <p class="text-gray-400 mb-4"><?= htmlspecialchars($produit['description']) ?></p>
            <p class="text-2xl text-violet-400 font-semibold mb-6"><?= number_format($produit['prix']) ?> CFA</p>
            <form action="ajouter_panier.php" method="POST" class="mb-4">
                <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                <button type="submit" class="bg-violet-600 hover:bg-violet-700 px-6 py-3 rounded" <?= $isInCart ? 'disabled' : '' ?>>
                    <?= $isInCart ? 'Déjà dans le panier' : 'Ajouter au panier' ?>
                </button>
            </form>
            <a href="index.php" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded inline-block">Retour</a>
        </div>
    </main>
</body>
</html>
