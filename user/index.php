<?php
include '../connection.php';
session_start();

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <?php include 'navbar.php'; ?>

    <main class="p-8">
        <h1 class="text-2xl text-violet-400 font-semibold mb-6">Nos produits</h1>

        <!-- Search and filter form -->
        <form method="GET" class="mb-6 flex gap-4">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un produit..." class="px-4 py-2 rounded bg-gray-800 text-white">
            <button type="submit" name="action" value="search" class="bg-violet-600 hover:bg-violet-700 px-4 py-2 rounded">Rechercher</button>
            <select name="categorie" class="px-4 py-2 bg-gray-800 text-white rounded">
                <option value="">Toutes les catégories</option>
                <?php
                $categories = $conn->query("SELECT DISTINCT categorie FROM produits")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $cat) {
                    $selected = $cat['categorie'] === $categorie ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($cat['categorie']) . '" ' . $selected . '>' . htmlspecialchars($cat['categorie']) . '</option>';
                }
                ?>
            </select>
            <button type="submit" name="action" value="filter" class="bg-violet-600 hover:bg-violet-700 px-4 py-2 rounded">Filtrer</button>
        </form>

        <!-- Product listing -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($produits as $produit): ?>
                <div class="bg-gray-800 rounded-lg p-3 shadow border border-gray-700">
                    <img src="<?= htmlspecialchars($produit['photo']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" class="w-full h-32 object-cover rounded mb-2">
                    <h2 class="text-lg font-bold truncate"><?= htmlspecialchars($produit['nom']) ?></h2>
                    <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($produit['description']) ?></p>
                    <p class="mt-1 text-violet-400 font-semibold"><?= number_format($produit['prix']) ?> CFA</p>
                    <a href="produit_details.php?id=<?= $produit['id'] ?>" class="mt-2 inline-block bg-violet-600 hover:bg-violet-700 px-3 py-1 rounded text-sm">Voir détails</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center gap-4">
            <?php for ($i = 1; $i <= ceil($totalItems / $itemsPerPage); $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&categorie=<?= urlencode($categorie) ?>"
                   class="px-4 py-2 rounded <?= $i === $page ? 'bg-violet-600' : 'bg-gray-800 hover:bg-gray-700' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
