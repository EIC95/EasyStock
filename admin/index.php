<?php 
session_start();

if($_SESSION['user_id']){
    include("../connection.php");
    
    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $query = "SELECT prenom, nom FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $prenom = $user['prenom'];
    $nom = $user['nom'];

    // Ventes du jour
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT SUM(pc.prix_total) as total_jour 
        FROM commandes c 
        JOIN produits_commandes pc ON c.id = pc.commande_id 
        WHERE DATE(c.date_commande) = ? AND c.etat = 'Livrée'");
    $stmt->execute([$today]);
    $ventes_jour = $stmt->fetchColumn() ?: 0;

    // Ventes du mois
    $month = date('Y-m');
    $stmt = $conn->prepare("SELECT SUM(pc.prix_total) as total_mois 
        FROM commandes c 
        JOIN produits_commandes pc ON c.id = pc.commande_id 
        WHERE DATE_FORMAT(c.date_commande, '%Y-%m') = ? AND c.etat = 'Livrée'");
    $stmt->execute([$month]);
    $ventes_mois = $stmt->fetchColumn() ?: 0;

    // Commandes non prises en charge
    $stmt = $conn->query("SELECT COUNT(*) FROM commandes WHERE etat = 'Non pris en charge'");
    $cmd_non_pris = $stmt->fetchColumn();

    // Livraisons en cours
    $stmt = $conn->query("SELECT COUNT(*) FROM commandes WHERE etat = 'En cours de livraison'");
    $livraisons_en_cours = $stmt->fetchColumn();

    // Pagination pour produits stock faible
    $limit_stock = 10;
    $page_stock = isset($_GET['page_stock']) ? max((int)$_GET['page_stock'], 1) : 1;
    $offset_stock = ($page_stock - 1) * $limit_stock;

    // Produits avec quantité < 50 (pagination)
    $stmt = $conn->prepare("SELECT * FROM produits WHERE quantite < 50 ORDER BY quantite ASC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit_stock, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset_stock, PDO::PARAM_INT);
    $stmt->execute();
    $produits_stock_faible = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total pour pagination
    $stmt = $conn->query("SELECT COUNT(*) FROM produits WHERE quantite < 50");
    $total_stock_faible = $stmt->fetchColumn();
    $pages_stock = ceil($total_stock_faible / $limit_stock);
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyStock - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <?php include("sidebar.php") ?>

    <main class="ml-64 p-8">
        <h1 class="text-3xl font-semibold text-violet-400 mb-8">
            Bienvenue <?php echo htmlspecialchars($prenom . ' ' . $nom); ?>
        </h1>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-gray-800 rounded-lg p-6 shadow flex flex-col items-center">
                <span class="text-gray-400 mb-2">Ventes du jour</span>
                <span class="text-2xl font-bold text-green-400"><?= number_format($ventes_jour, 2) ?> CFA</span>
            </div>
            <div class="bg-gray-800 rounded-lg p-6 shadow flex flex-col items-center">
                <span class="text-gray-400 mb-2">Ventes du mois</span>
                <span class="text-2xl font-bold text-blue-400"><?= number_format($ventes_mois, 2) ?> CFA</span>
            </div>
            <div class="bg-gray-800 rounded-lg p-6 shadow flex flex-col items-center">
                <span class="text-gray-400 mb-2">Commandes non prises en charge</span>
                <span class="text-2xl font-bold text-yellow-400"><?= $cmd_non_pris ?></span>
            </div>
            <div class="bg-gray-800 rounded-lg p-6 shadow flex flex-col items-center">
                <span class="text-gray-400 mb-2">Livraisons en cours</span>
                <span class="text-2xl font-bold text-orange-400"><?= $livraisons_en_cours ?></span>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-violet-300 mb-4">Produits avec une quantité inférieure à 50</h2>
        <div class="overflow-x-auto bg-gray-800 rounded-lg shadow border border-gray-700">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-700 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-2">Nom</th>
                        <th class="px-4 py-2">Quantité</th>
                        <th class="px-4 py-2">Prix</th>
                        <th class="px-4 py-2">Code Barre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits_stock_faible as $produit): ?>
                        <tr class="border-t border-gray-700 hover:bg-gray-700">
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['nom']) ?></td>
                            <td class="px-4 py-2 text-red-400 font-bold"><?= htmlspecialchars($produit['quantite']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['prix']) ?> CFA</td>
                            <td class="px-4 py-2"><?= htmlspecialchars($produit['code_barre']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($produits_stock_faible)): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-400">Aucun produit avec une quantité inférieure à 50.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $pages_stock; $i++): ?>
                <a href="?page_stock=<?= $i ?>" class="px-3 py-1 rounded text-sm border <?= $i == $page_stock ? 'bg-violet-700 text-white border-violet-700' : 'bg-gray-800 text-violet-400 border-gray-600 hover:bg-gray-700' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
