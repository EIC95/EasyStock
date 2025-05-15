<?php 
include ("../verify.php");
if($_SESSION['user_id']){
    
    
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT prenom, nom FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $prenom = $user['prenom'];
    $nom = $user['nom'];

    
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT SUM(pc.prix_total) as total_jour 
        FROM commandes c 
        JOIN produits_commandes pc ON c.id = pc.commande_id 
        WHERE DATE(c.date_commande) = ? AND c.etat = 'Livrée'");
    $stmt->execute([$today]);
    $ventes_jour = $stmt->fetchColumn() ?: 0;

    
    $month = date('Y-m');
    $stmt = $conn->prepare("SELECT SUM(pc.prix_total) as total_mois 
        FROM commandes c 
        JOIN produits_commandes pc ON c.id = pc.commande_id 
        WHERE DATE_FORMAT(c.date_commande, '%Y-%m') = ? AND c.etat = 'Livrée'");
    $stmt->execute([$month]);
    $ventes_mois = $stmt->fetchColumn() ?: 0;

    
    $stmt = $conn->query("SELECT COUNT(*) FROM commandes WHERE etat = 'Non pris en charge'");
    $cmd_non_pris = $stmt->fetchColumn();

    
    $stmt = $conn->query("SELECT COUNT(*) FROM commandes WHERE etat = 'En cours de livraison'");
    $livraisons_en_cours = $stmt->fetchColumn();

    
    $limit_stock = 10;
    $page_stock = isset($_GET['page_stock']) ? max((int)$_GET['page_stock'], 1) : 1;
    $offset_stock = ($page_stock - 1) * $limit_stock;

    
    $stmt = $conn->prepare("SELECT * FROM produits WHERE quantite < 50 ORDER BY quantite ASC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit_stock, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset_stock, PDO::PARAM_INT);
    $stmt->execute();
    $produits_stock_faible = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
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
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">
            Bienvenue <?php echo htmlspecialchars($prenom . ' ' . $nom); ?>
        </h1>
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <span class="text-muted">Ventes du jour</span>
                <span class="dashboard-value dashboard-green"><?= number_format($ventes_jour, 2) ?> CFA</span>
            </div>
            <div class="dashboard-card">
                <span class="text-muted">Ventes du mois</span>
                <span class="dashboard-value dashboard-blue"><?= number_format($ventes_mois, 2) ?> CFA</span>
            </div>
            <div class="dashboard-card">
                <span class="text-muted">Commandes non prises en charge</span>
                <span class="dashboard-value dashboard-yellow"><?= $cmd_non_pris ?></span>
            </div>
            <div class="dashboard-card">
                <span class="text-muted">Livraisons en cours</span>
                <span class="dashboard-value dashboard-orange"><?= $livraisons_en_cours ?></span>
            </div>
        </div>

        <h2 class="section-title">Produits avec une quantité inférieure à 50</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Code Barre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits_stock_faible as $produit): ?>
                        <tr>
                            <td><?= htmlspecialchars($produit['nom']) ?></td>
                            <td class="text-danger"><?= htmlspecialchars($produit['quantite']) ?></td>
                            <td><?= htmlspecialchars($produit['prix']) ?> CFA</td>
                            <td><?= htmlspecialchars($produit['code_barre']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($produits_stock_faible)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Aucun produit avec une quantité inférieure à 50.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages_stock; $i++): ?>
                <a href="?page_stock=<?= $i ?>" class="pagination-link <?= $i == $page_stock ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
