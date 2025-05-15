<?php
    
    include ("../verify.php");
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    $limit = 10;
    $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
    $offset = ($page - 1) * $limit;

    $where = "1=1";
    $params = [];
    if ($search !== '') {
        $where .= " AND (nom LIKE :search OR tel LIKE :search OR email LIKE :search OR ville LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $stmt = $conn->prepare("SELECT * FROM fournisseurs WHERE $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_stmt = $conn->prepare("SELECT COUNT(*) FROM fournisseurs WHERE $where");
    foreach ($params as $k => $v) $total_stmt->bindValue($k, $v);
    $total_stmt->execute();
    $total = $total_stmt->fetchColumn();
    $pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fournisseurs</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Gestion des Fournisseurs</h1>

        <form method="get" class="mb-4">
            <input type="text" name="search" placeholder="Recherche nom, téléphone, email ou ville" value="<?= htmlspecialchars($search) ?>" class="input" />
            <button type="submit" class="btn-primary">Rechercher</button>
        </form>

        <?php if (isset($_SESSION['fournisseur_errors'])): ?>
            <div class="alert-error">
                <ul>
                    <?php foreach ($_SESSION['fournisseur_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['fournisseur_errors']); ?>
        <?php endif; ?>

        <form method="POST" action="add_fournisseur.php" class="form-add-product">
            <input type="text" name="nom" placeholder="Nom" required class="input">
            <input type="text" name="tel" placeholder="Téléphone" required class="input">
            <input type="text" name="adresse" placeholder="Adresse" required class="input">
            <input type="text" name="ville" placeholder="Ville" required class="input">
            <input type="text" name="pays" placeholder="Pays" required class="input">
            <input type="email" name="email" placeholder="Email" required class="input">
            <button type="submit" class="btn-primary btn-block">Ajouter le fournisseur</button>
        </form>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fournisseurs as $fournisseur): ?>
                        <tr>
                            <form method="POST" action="edit_fournisseur.php?page=<?= $page ?>&search=<?= urlencode($search) ?>">
                                <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
                                <td><input type="text" name="nom" value="<?= htmlspecialchars($fournisseur['nom']) ?>" class="input"></td>
                                <td><input type="text" name="tel" value="<?= htmlspecialchars($fournisseur['tel']) ?>" class="input"></td>
                                <td><input type="text" name="adresse" value="<?= htmlspecialchars($fournisseur['adresse']) ?>" class="input"></td>
                                <td><input type="text" name="ville" value="<?= htmlspecialchars($fournisseur['ville']) ?>" class="input"></td>
                                <td><input type="text" name="pays" value="<?= htmlspecialchars($fournisseur['pays']) ?>" class="input"></td>
                                <td><input type="email" name="email" value="<?= htmlspecialchars($fournisseur['email']) ?>" class="input"></td>
                                <td>
                                    <button type="submit" class="link-details">Modifier</button>
                            </form>
                            <form method="POST" action="delete_fournisseur.php?page=<?= $page ?>&search=<?= urlencode($search) ?>" class="form-inline">
                                <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
                                <button type="submit" class="link-delete">Supprimer</button>
                            </form>
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
