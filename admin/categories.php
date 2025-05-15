<?php

include ("../verify.php");


$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT * FROM categories ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_stmt = $conn->query("SELECT COUNT(*) FROM categories");
$total = $total_stmt->fetchColumn();
$pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <?php include("sidebar.php") ?>

    <main class="main-container">
        <h1 class="page-title">Gestion des Catégories</h1>

        
        <?php if (isset($_SESSION['categorie_errors'])): ?>
            <div class="alert-error">
                <ul>
                    <?php foreach ($_SESSION['categorie_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['categorie_errors']); ?>
        <?php endif; ?>

        <form method="POST" action="add_categorie.php" class="form-add-product">
            <input type="text" name="nom" placeholder="Nom de la catégorie" required class="input">
            <button type="submit" class="btn-primary btn-block">Ajouter la catégorie</button>
        </form>

        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <tr>
                            <form method="POST" action="edit_categorie.php?page=<?= $page ?>">
                                <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
                                <td><input type="text" name="nom" value="<?= htmlspecialchars($categorie['nom']) ?>" class="input"></td>
                                <td>
                                    <button type="submit" class="link-details">Modifier</button>
                            </form>
                            <form method="POST" action="delete_categorie.php?page=<?= $page ?>" class="form-inline">
                                <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
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
                <a href="?page=<?= $i ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>
