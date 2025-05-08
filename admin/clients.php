<?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    include("../connection.php");

    $limit = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    $offset = ($page - 1) * $limit;

    $total_stmt = $conn->query("SELECT COUNT(*) FROM clients");
    $total_clients = (int)$total_stmt->fetchColumn();
    $total_pages = ceil($total_clients / $limit);

    $stmt = $conn->prepare("SELECT id, prenom, nom, email, tel FROM clients LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyStock - Clients</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 px-6 py-10">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Liste des clients</h1>

        <div class="overflow-x-auto rounded border border-gray-700 bg-gray-800 shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Prénom</th>
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Téléphone</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clients as $client): ?>
                    <tr class="border-t border-gray-700 hover:bg-gray-700">
                        <td class="px-4 py-2"><?= htmlspecialchars($client['prenom']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($client['nom']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($client['email']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($client['tel']) ?></td>
                        <td class="px-4 py-2">
                            <form action="delete_client.php?page=<?= $page ?>" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
                                <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                                <button type="submit" class="text-sm text-red-400 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>"
                    class="px-3 py-1 rounded text-sm border
                        <?= $i == $page 
                            ? 'bg-violet-700 text-white border-violet-700' 
                            : 'bg-gray-800 text-violet-400 border-gray-600 hover:bg-gray-700' 
                        ?>"
                >
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>

