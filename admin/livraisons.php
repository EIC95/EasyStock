<?php

include ("../verify.php");


$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;


$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, c.etat, u.nom AS user_nom, u.prenom AS user_prenom, u.login AS user_login 
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    WHERE c.etat = 'En cours de livraison'
    ORDER BY c.date_commande DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_stmt = $conn->query("SELECT COUNT(*) FROM commandes WHERE etat = 'En cours de livraison'");
$total = $total_stmt->fetchColumn();
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraisons en cours</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Livraisons en cours</h1>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><?= htmlspecialchars($commande['id']) ?></td>
                            <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                            <td><?php echo htmlspecialchars($commande['user_prenom']) . " " . htmlspecialchars($commande['user_nom']); ?></td>
                            <td><?= htmlspecialchars($commande['user_login']) ?></td>
                            <td>
                                <a href="livraison_details.php?id=<?= $commande['id'] ?>" class="link-details">Voir les détails</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
