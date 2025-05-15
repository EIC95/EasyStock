<?php 
    
    include ("../verify.php");
    

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $limit = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    $offset = ($page - 1) * $limit;

    $where = "WHERE role = 'user'";
    $params = [];

    if ($search !== '') {
        $where .= " AND (prenom LIKE :search OR nom LIKE :search OR login LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $total_stmt = $conn->prepare("SELECT COUNT(*) FROM users $where");
    foreach ($params as $k => $v) $total_stmt->bindValue($k, $v);
    $total_stmt->execute();
    $total_clients = (int)$total_stmt->fetchColumn();
    $total_pages = ceil($total_clients / $limit);

    $stmt = $conn->prepare("SELECT id, prenom, nom, login, tel, adresse FROM users $where LIMIT :limit OFFSET :offset");
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
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
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Liste des clients</h1>

        <form method="get" class="mb-4">
            <input type="text" name="search" placeholder="Recherche prénom, nom ou login" value="<?= htmlspecialchars($search) ?>" class="input" />
            <button type="submit" class="btn-primary">Rechercher</button>
        </form>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>login</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars($client['prenom']) ?></td>
                        <td><?= htmlspecialchars($client['nom']) ?></td>
                        <td><?= htmlspecialchars($client['login']) ?></td>
                        <td><?= htmlspecialchars($client['tel']) ?></td>
                        <td><?= htmlspecialchars($client['adresse']) ?></td>
                        <td>
                            <form action="delete_client.php?page=<?= $page ?>" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="form-inline">
                                <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                                <button type="submit" class="link-delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>

