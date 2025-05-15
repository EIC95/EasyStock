<?php
    
    include ("../verify.php");
    
    $limit = 10;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $filter_supplier = isset($_GET['fournisseur']) ? trim($_GET['fournisseur']) : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $categories_stmt = $conn->query("SELECT nom FROM categories");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

    $suppliers_stmt = $conn->query("SELECT nom FROM fournisseurs");
    $suppliers = $suppliers_stmt->fetchAll(PDO::FETCH_COLUMN);

    $where = [];
    $params = [];

    if ($filter_supplier !== '') {
        $where[] = "fournisseur = :fournisseur_nom";
        $params[':fournisseur_nom'] = $filter_supplier;
    }
    if ($search !== '') {
        $where[] = "(nom LIKE :search OR code_barre LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $where_sql = count($where) ? "WHERE " . implode(' AND ', $where) : "";

    $query = "SELECT * FROM produits $where_sql ORDER BY id DESC LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_query = "SELECT COUNT(*) FROM produits $where_sql";
    $total_stmt = $conn->prepare($total_query);
    foreach ($params as $k => $v) {
        $total_stmt->bindValue($k, $v);
    }
    $total_stmt->execute();
    $total = $total_stmt->fetchColumn();
    $pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Gestion des Stocks</h1>

        <form method="GET" class="form-filter">
            <select name="fournisseur" class="select">
                <option value="">Tous les fournisseurs</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= htmlspecialchars($supplier) ?>" <?= $filter_supplier == $supplier ? 'selected' : '' ?>>
                        <?= htmlspecialchars($supplier) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="search" placeholder="Recherche produit, code barre ou description" value="<?= htmlspecialchars($search) ?>" class="input" />
            <button type="submit" class="btn-primary">Filtrer / Rechercher</button>
        </form>

        <form method="POST" action="add_product.php" enctype="multipart/form-data" class="form-add-product">
            <input type="text" name="nom" placeholder="Nom" required class="input">
            <input type="number" name="quantite" placeholder="Quantité" required class="input">
            <input type="number" step="0.01" name="prix" placeholder="Prix" required class="input">
            <input type="text" name="code_barre" placeholder="Code Barre" required class="input">
            <select name="fournisseur" required class="select">
                <option value="">Sélectionner un fournisseur</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= htmlspecialchars($supplier) ?>"><?= htmlspecialchars($supplier) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="categorie" required class="select">
                <option value="">Sélectionner une catégorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie) ?>"><?= htmlspecialchars($categorie) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="description" placeholder="Description" class="textarea"></textarea>
            <input type="file" name="photo" accept="image/*" class="input-file">
            <button type="submit" class="btn-primary btn-block">Ajouter le produit</button>
        </form>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Code Barre</th>
                        <th>Fournisseur</th>
                        <th>Catégorie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $produit): ?>
                        <tr>
                            <td><?= htmlspecialchars($produit['nom']) ?></td>
                            <td><?= htmlspecialchars($produit['quantite']) ?></td>
                            <td><?= htmlspecialchars($produit['prix']) ?> CFA</td>
                            <td><?= htmlspecialchars($produit['code_barre']) ?></td>
                            <td><?= htmlspecialchars($produit['fournisseur']) ?></td>
                            <td><?= htmlspecialchars($produit['categorie']) ?></td>
                            <td>
                                <a href="product_details.php?id=<?= $produit['id'] ?>" class="link-details">Détails</a>
                                <form method="POST" action="delete_product.php?page=<?= $page ?>" class="form-inline">
                                    <input type="hidden" name="id" value="<?= $produit['id'] ?>">
                                    <button type="submit" class="link-delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>&fournisseur=<?= urlencode($filter_supplier) ?>&search=<?= urlencode($search) ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
