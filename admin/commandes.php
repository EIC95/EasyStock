<?php
session_start();
include("../connection.php");

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Fetch orders
$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, u.nom AS user_nom, u.login AS user_login 
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.date_commande DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of orders
$total_stmt = $conn->query("SELECT COUNT(*) FROM commandes");
$total = $total_stmt->fetchColumn();
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Gestion des Commandes</h1>

        <!-- Liste des commandes -->
        <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-2">ID Commande</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Client</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-700">
                            <td class="px-4 py-2"><?= htmlspecialchars($commande['id']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($commande['date_commande']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($commande['user_nom']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($commande['user_login']) ?></td>
                            <td class="px-4 py-2">
                                <a href="commande_details.php?id=<?= $commande['id'] ?>" class="text-blue-400 hover:underline">Voir les détails</a>
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
