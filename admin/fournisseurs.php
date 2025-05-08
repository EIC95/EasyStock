<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    include("../connection.php");

    // Pagination
    $limit = 10;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT * FROM fournisseurs ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_stmt = $conn->query("SELECT COUNT(*) FROM fournisseurs");
    $total = $total_stmt->fetchColumn();
    $pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fournisseurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold text-violet-400 mb-6">Gestion des Fournisseurs</h1>

        <!-- Formulaire d'ajout -->
        <?php if (isset($_SESSION['fournisseur_errors'])): ?>
            <div class="bg-red-200 border border-red-400 text-red-900 px-4 py-2 rounded mb-4">
                <ul class="list-disc ml-5">
                    <?php foreach ($_SESSION['fournisseur_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['fournisseur_errors']); ?>
        <?php endif; ?>

        <form method="POST" action="add_fournisseur.php" class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800 p-6 rounded-lg shadow">
            <input type="text" name="nom" placeholder="Nom" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="text" name="tel" placeholder="Téléphone" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="text" name="adresse" placeholder="Adresse" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="text" name="ville" placeholder="Ville" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="text" name="pays" placeholder="Pays" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <input type="email" name="email" placeholder="Email" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
            <button type="submit" class="col-span-1 md:col-span-2 mt-4 bg-violet-700 text-white rounded px-4 py-2 hover:bg-violet-800">Ajouter le fournisseur</button>
        </form>

        <!-- Liste des fournisseurs -->
        <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Téléphone</th>
                        <th class="px-4 py-2">Adresse</th>
                        <th class="px-4 py-2">Ville</th>
                        <th class="px-4 py-2">Pays</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fournisseurs as $fournisseur): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-700">
                            <form method="POST" action="edit_fournisseur.php?page=<?= $page ?>">
                                <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
                                <td class="px-4 py-2"><input type="text" name="nom" value="<?= htmlspecialchars($fournisseur['nom']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="text" name="tel" value="<?= htmlspecialchars($fournisseur['tel']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="text" name="adresse" value="<?= htmlspecialchars($fournisseur['adresse']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="text" name="ville" value="<?= htmlspecialchars($fournisseur['ville']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="text" name="pays" value="<?= htmlspecialchars($fournisseur['pays']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2"><input type="email" name="email" value="<?= htmlspecialchars($fournisseur['email']) ?>" class="bg-gray-700 border border-gray-600 text-white rounded px-2 py-1 w-full"></td>
                                <td class="px-4 py-2 flex flex-col gap-2">
                                    <button type="submit" class="text-sm text-green-400 hover:underline">Modifier</button>
                                </form>
                                <form method="POST" action="delete_fournisseur.php?page=<?= $page ?>">
                                    <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
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
