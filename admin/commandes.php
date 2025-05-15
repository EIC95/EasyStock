<?php

include ("../verify.php");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$where = "1=1";
$params = [];
if ($search !== '') {
    $where .= " AND c.id = :search_id";
    $params[':search_id'] = $search;
}

$stmt = $conn->prepare("
    SELECT c.id, c.date_commande, c.etat, u.nom AS user_nom, u.prenom AS user_prenom, u.login AS user_login 
    FROM commandes c
    JOIN users u ON c.user_id = u.id
    WHERE $where
    ORDER BY c.date_commande DESC
    LIMIT :limit OFFSET :offset
");
foreach ($params as $k => $v) $stmt->bindValue($k, $v, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_stmt = $conn->prepare("SELECT COUNT(*) FROM commandes c WHERE $where");
foreach ($params as $k => $v) $total_stmt->bindValue($k, $v, PDO::PARAM_INT);
$total_stmt->execute();
$total = $total_stmt->fetchColumn();
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Gestion des Commandes</h1>

        <form method="get" class="mb-4">
            <input type="number" name="search" placeholder="Recherche par ID commande" value="<?= htmlspecialchars($search) ?>" class="input" />
            <button type="submit" class="btn-primary">Rechercher</button>
        </form>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>login</th>
                        <th>État</th>
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
                            <td><?= htmlspecialchars($commande['etat']) ?></td>
                            <td>
                                <a href="commande_details.php?id=<?= $commande['id'] ?>" class="link-details">Voir les détails</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
