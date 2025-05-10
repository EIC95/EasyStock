<?php
    session_start();

    include("../connection.php");

    // Pagination
    $limit = 10;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    // Filter by supplier
    $filter_supplier = isset($_GET['fournisseur']) ? $_GET['fournisseur'] : '';

    // Fetch categories for the category dropdown
    $categories_stmt = $conn->query("SELECT id, name FROM categories");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT produits.*, fournisseurs.nom AS fournisseur_nom, categories.name AS categorie_nom 
            FROM produits 
            LEFT JOIN fournisseurs ON produits.fournisseur = fournisseurs.id
            LEFT JOIN categories ON produits.categorie = categories.id";
    if ($filter_supplier) {
        $query .= " WHERE produits.fournisseur = :fournisseur";
    }
    $query .= " ORDER BY produits.id DESC LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);
    if ($filter_supplier) {
        $stmt->bindParam(':fournisseur', $filter_supplier, PDO::PARAM_INT);
    }
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_query = "SELECT COUNT(*) FROM produits";
    if ($filter_supplier) {
        $total_query .= " WHERE fournisseur = :fournisseur";
    }
    $total_stmt = $conn->prepare($total_query);
    if ($filter_supplier) {
        $total_stmt->bindParam(':fournisseur', $filter_supplier, PDO::PARAM_INT);
    }
    $total_stmt->execute();
    $total = $total_stmt->fetchColumn();
    $pages = ceil($total / $limit);

    // Fetch suppliers for the filter dropdown
    $suppliers_stmt = $conn->query("SELECT id, nom FROM fournisseurs");
    $suppliers = $suppliers_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Gestion des Stocks</h1>

        <!-- Filter by supplier -->
        <form method="GET" class="mb-4">
            <select name="fournisseur" class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <option value="">Tous les fournisseurs</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id'] ?>" <?= $filter_supplier == $supplier['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($supplier['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-violet-700 text-white rounded px-4 py-2 hover:bg-violet-800">Filtrer</button>
        </form>

        <!-- Formulaire d'ajout -->
        <form method="POST" action="add_product.php" enctype="multipart/form-data" class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800 p-6 rounded-lg shadow">
            <input type="text" name="nom" placeholder="Nom" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="number" name="quantite" placeholder="Quantité" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="number" step="0.01" name="prix" placeholder="Prix" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="text" name="code_barre" placeholder="Code Barre" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <select name="fournisseur" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <option value="">Sélectionner un fournisseur</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="categorie" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <option value="">Sélectionner une catégorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="description" placeholder="Description" class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2"></textarea>
            <input type="file" name="photo" accept="image/*" class="file:hidden bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <button type="submit" class="col-span-1 md:col-span-2 mt-4 bg-violet-700 text-white rounded px-4 py-2 hover:bg-violet-800">Ajouter le produit</button>
        </form>

        <!-- Liste des produits -->
        <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Quantité</th>
                        <th class="px-4 py-2">Prix</th>
                        <th class="px-4 py-2">Code Barre</th>
                        <th class="px-4 py-2">Fournisseur</th>
                        <th class="px-4 py-2">Catégorie</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $produit): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-700">
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['nom']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['quantite']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['prix']) ?> CFA</td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['code_barre']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['fournisseur_nom']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['categorie_nom']) ?></td>
                            <td class="px-4 py-2 flex flex-col gap-2">
                                <a href="product_details.php?id=<?= $produit['id'] ?>" class="text-sm text-blue-400 hover:underline">Détails</a>
                                <form method="POST" action="delete_product.php?page=<?= $page ?>">
                                    <input type="hidden" name="id" value="<?= $produit['id'] ?>">
                                    <button type="submit" class="text-sm text-red-400 hover:underline">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>&fournisseur=<?= $filter_supplier ?>" class="px-3 py-1 rounded text-sm border <?= $i == $page ? 'bg-violet-700 text-white border-violet-700' : 'bg-gray-800 text-violet-400 border-gray-600 hover:bg-gray-700' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
