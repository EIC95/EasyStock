<?php
session_start();
include("../connection.php");

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT * FROM categories ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_stmt = $conn->query("SELECT COUNT(*) FROM categories");
$total = $total_stmt->fetchColumn();
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Gestion des Catégories</h1>

        <!-- Formulaire d'ajout -->
        <?php if (isset($_SESSION['categorie_errors'])): ?>
            <div class="bg-red-200 border border-red-400 text-red-900 px-4 py-2 rounded mb-4">
                <ul class="list-disc ml-5">
                    <?php foreach ($_SESSION['categorie_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['categorie_errors']); ?>
        <?php endif; ?>

        <form method="POST" action="/admin/add_categorie.php" class="mb-8 grid grid-cols-1 gap-4 bg-gray-800 p-6 rounded-lg shadow">
            <input type="text" name="nom" placeholder="Nom de la catégorie" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <button type="submit" class="mt-4 bg-violet-700 text-white rounded px-4 py-2 hover:bg-violet-800">Ajouter la catégorie</button>
        </form>

        <!-- Liste des catégories -->
        <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-700">
                            <form method="POST" action="/admin/edit_categorie.php?page=<?= $page ?>">
                                <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
                                <td class="px-4 py-2"><input type="text" name="nom" value="<?= htmlspecialchars($categorie['name']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2 flex flex-col gap-2">
                                    <button type="submit" class="text-sm text-green-400 hover:underline">Modifier</button>
                                </form>
                                <form method="POST" action="/admin/delete_categorie.php?page=<?= $page ?>">
                                    <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
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
                <a href="?page=<?= $i ?>" class="px-3 py-1 rounded text-sm border <?= $i == $page ? 'bg-violet-700 text-white border-violet-700' : 'bg-gray-800 text-violet-400 border-gray-600 hover:bg-gray-700' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
