<?php
session_start();
require_once "../verify.php";
include '../connection.php';

// Pagination setup
$itemsPerPage = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Search and filter
$search = $_GET['search'] ?? '';
$categorie = $_GET['categorie'] ?? '';

$query = "SELECT * FROM produits WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND nom LIKE ?";
    $params[] = "%" . $search . "%";
}
if ($categorie !== '') {
    $query .= " AND categorie = ?";
    $params[] = $categorie;
}

$totalQuery = $conn->prepare($query);
$totalQuery->execute($params);
$totalItems = $totalQuery->rowCount();

$query .= " LIMIT $itemsPerPage OFFSET $offset";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$produits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique</title>
    <link rel="stylesheet" href="user-style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <h1>Nos produits</h1>

        <!-- Search and filter form -->
        <form method="GET">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un produit...">
            <button type="submit" name="action" value="search">Rechercher</button>
            <select name="categorie">
                <option value="">Toutes les catégories</option>
                <?php
                $categories = $conn->query("SELECT DISTINCT categorie FROM produits")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $cat) {
                    $selected = $cat['categorie'] === $categorie ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($cat['categorie']) . '" ' . $selected . '>' . htmlspecialchars($cat['categorie']) . '</option>';
                }
                ?>
            </select>
            <button type="submit" name="action" value="filter">Filtrer</button>
        </form>

        <!-- Product listing -->
        <div>
            <?php foreach ($produits as $produit): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                    <h2><?= htmlspecialchars($produit['nom']) ?></h2>
                    <p><?= htmlspecialchars($produit['description']) ?></p>
                    <p class="price"><?= number_format($produit['prix']) ?> CFA</p>
                    <a href="produit_details.php?id=<?= $produit['id'] ?>">Voir détails</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= ceil($totalItems / $itemsPerPage); $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&categorie=<?= urlencode($categorie) ?>"
                   class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
