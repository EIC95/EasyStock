<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    include("../connection.php");

    if (!isset($_GET['id'])) {
        header("Location: stocks.php");
        exit();
    }

    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT produits.*, fournisseurs.nom AS fournisseur_nom 
                            FROM produits 
                            LEFT JOIN fournisseurs ON produits.fournisseur = fournisseurs.id 
                            WHERE produits.id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        header("Location: stocks.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Produit</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Détails du Produit</h1>

        <a href="stocks.php" class="text-sm text-blue-400 hover:underline mb-4 inline-block">← Retour à la liste des produits</a>

        <div class="bg-gray-800 p-6 rounded-lg shadow mb-6 flex justify-evenly">
            <?php if ($produit['photo']): ?>
                <img src="../uploads/produits/<?= htmlspecialchars($produit['photo']) ?>" alt="Photo du produit" class="mt-4 max-w-xs">
            <?php endif; ?>
            <div>
                <h2 class="text-xl font-semibold mb-4"><?= htmlspecialchars($produit['nom']) ?></h2>
                <p><strong>Quantité :</strong> <?= htmlspecialchars($produit['quantite']) ?></p>
                <p><strong>Prix :</strong> <?= htmlspecialchars($produit['prix']) ?> CFA</p>
                <p><strong>Code Barre :</strong> <?= htmlspecialchars($produit['code_barre']) ?></p>
                <p><strong>Fournisseur :</strong> <?= htmlspecialchars($produit['fournisseur_nom']) ?></p>
                <p><strong>Description :</strong> <?= htmlspecialchars($produit['description']) ?></p>
            </div>
        </div>

        <form method="POST" action="edit_product.php" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800 p-6 rounded-lg shadow">
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">
            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="number" name="quantite" value="<?= htmlspecialchars($produit['quantite']) ?>" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="text" name="code_barre" value="<?= htmlspecialchars($produit['code_barre']) ?>" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <textarea name="description" class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2"><?= htmlspecialchars($produit['description']) ?></textarea>
            <input type="file" name="photo" accept="image/*" class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <button type="submit" class="col-span-1 md:col-span-2 mt-4 bg-violet-700 text-white rounded px-4 py-2 hover:bg-violet-800">Modifier le produit</button>
        </form>

        <form method="POST" action="delete_product.php" class="mt-6">
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">
            <button type="submit" class="bg-red-700 text-white rounded px-4 py-2 hover:bg-red-800">Supprimer le produit</button>
        </form>
    </main>
</body>
</html>
