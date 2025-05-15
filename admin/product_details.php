<?php
    include ("../verify.php");

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

    // Récupérer les catégories depuis la table categories
    $categories = $conn->query("SELECT nom FROM categories")->fetchAll(PDO::FETCH_COLUMN);

    // Récupérer les fournisseurs depuis la table fournisseurs
    $fournisseurs = $conn->query("SELECT id, nom FROM fournisseurs")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Produit</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Détails du Produit</h1>

        <a href="stocks.php" class="link-details mb-4">← Retour à la liste des produits</a>

        <div class="product-details-container">
            <?php if ($produit['photo']): ?>
                <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="Photo du produit" class="product-photo">
            <?php endif; ?>
            <div>
                <h2 class="product-title"><?= htmlspecialchars($produit['nom']) ?></h2>
                <p><strong>Quantité :</strong> <?= htmlspecialchars($produit['quantite']) ?></p>
                <p><strong>Prix :</strong> <?= htmlspecialchars($produit['prix']) ?> CFA</p>
                <p><strong>Code Barre :</strong> <?= htmlspecialchars($produit['code_barre']) ?></p>
                <p><strong>Fournisseur :</strong> <?= htmlspecialchars($produit['fournisseur_nom']) ?></p>
                <p><strong>Description :</strong> <?= htmlspecialchars($produit['description']) ?></p>
                <p><strong>Categorie :</strong> <?= htmlspecialchars($produit['categorie']) ?></p>
            </div>
        </div>

        <form method="POST" action="edit_product.php" enctype="multipart/form-data" class="form-add-product">
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">
            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" required class="input">
            <input type="number" name="quantite" value="<?= htmlspecialchars($produit['quantite']) ?>" required class="input">
            <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" required class="input">
            <input type="text" name="code_barre" value="<?= htmlspecialchars($produit['code_barre']) ?>" required class="input">
            <select name="categorie" class="input" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $produit['categorie'] == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="fournisseur" class="input" required>
                <?php foreach ($fournisseurs as $f): ?>
                    <option value="<?= $f['nom'] ?>" <?= $produit['fournisseur'] == $f['nom'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <textarea name="description" class="textarea"><?= htmlspecialchars($produit['description']) ?></textarea>
            <input type="file" name="photo" accept="image/*" class="input-file">
            <button type="submit" class="btn-primary btn-block">Modifier le produit</button>
        </form>

        <form method="POST" action="delete_product.php" class="mt-6">
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">
            <button type="submit" class="btn-danger">Supprimer le produit</button>
        </form>
    </main>
</body>
</html>
