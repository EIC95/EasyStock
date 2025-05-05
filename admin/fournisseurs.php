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
<body class="bg-gray-50 text-gray-800">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-2xl font-semibold mb-6">Gestion des Fournisseurs</h1>

        <!-- Formulaire d'ajout -->
        <?php if (isset($_SESSION['fournisseur_errors'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                <ul class="list-disc ml-5">
                    <?php foreach ($_SESSION['fournisseur_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['fournisseur_errors']); ?>
        <?php endif; ?>

        <form method="POST" action="add_fournisseur.php" class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-lg shadow">
            <input type="text" name="nom" placeholder="Nom" required class="border rounded px-4 py-2">
            <input type="text" name="tel" placeholder="Téléphone" required class="border rounded px-4 py-2">
            <input type="text" name="adresse" placeholder="Adresse" required class="border rounded px-4 py-2">
            <input type="text" name="ville" placeholder="Ville" required class="border rounded px-4 py-2">
            <input type="text" name="pays" placeholder="Pays" required class="border rounded px-4 py-2">
            <input type="email" name="email" placeholder="Email" required class="border rounded px-4 py-2">
            <button type="submit" class="col-span-1 md:col-span-2 mt-4 bg-purple-600 text-white rounded px-4 py-2 hover:bg-purple-700">Ajouter le fournisseur</button>
        </form>

        <!-- Liste des fournisseurs -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Téléphone</th>
                        <th class="px-4 py-2">Adresse</th>
                        <th class="px-4 py-2">Ville</th>
                        <th class="px-4 py-2">Pays</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fournisseurs as $fournisseur): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($fournisseur['nom']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($fournisseur['tel']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($fournisseur['adresse']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($fournisseur['ville']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($fournisseur['pays']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($fournisseur['email']); ?></td>
                            <td class="px-4 py-2">
                                <form method="POST" action="delete_fournisseur.php?page=<?php echo $page; ?>">
                                    <input type="hidden" name="id" value="<?php echo $fournisseur['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:underline">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center space-x-2">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="px-3 py-1 rounded <?php echo $i === $page ? 'bg-purple-600 text-white' : 'bg-gray-200'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
