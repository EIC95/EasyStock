<?php

include ("../verify.php");


$userId = $_SESSION['user_id'];


$stmt = $conn->prepare("
    SELECT p.id, p.nom, p.prix, p.photo, p.quantite AS disponible, c.quantite 
    FROM panier c 
    JOIN produits p ON c.produit_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$userId]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $produitId = $_POST['produit_id'];
    $stmt = $conn->prepare("DELETE FROM panier WHERE user_id = ? AND produit_id = ?");
    $stmt->execute([$userId, $produitId]);
    header('Location: panier.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    
    $stmt = $conn->prepare("INSERT INTO commandes (user_id, date_commande) VALUES (?, NOW())");
    $stmt->execute([$userId]);
    $commandeId = $conn->lastInsertId();

    $stmt = $conn->prepare("
        INSERT INTO produits_commandes (commande_id, produit_id, quantite, prix_total) 
        SELECT ?, c.produit_id, c.quantite, (c.quantite * p.prix) 
        FROM panier c 
        JOIN produits p ON c.produit_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$commandeId, $userId]);

    // Mettre à jour la quantité des produits
    $stmt = $conn->prepare("SELECT produit_id, quantite FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);
    $panier_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($panier_items as $item) {
        $stmt_update = $conn->prepare("UPDATE produits SET quantite = quantite - :qte WHERE id = :pid");
        $stmt_update->bindValue(':qte', $item['quantite'], PDO::PARAM_INT);
        $stmt_update->bindValue(':pid', $item['produit_id'], PDO::PARAM_INT);
        $stmt_update->execute();
    }

    $stmt = $conn->prepare("DELETE FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);

    header('Location: panier.php');
    exit;
}


$stmt = $conn->prepare("SELECT id, date_commande, etat FROM commandes WHERE user_id = ? AND (etat = 'Non pris en charge' OR etat = 'En cours de livraison') ORDER BY date_commande DESC");
$stmt->execute([$userId]);
$commandes_en_cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, date_commande, etat FROM commandes WHERE user_id = ? AND etat = 'Livrée' ORDER BY date_commande DESC");
$stmt->execute([$userId]);
$commandes_livrees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="userStyle.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main-content">
        <h1 class="page-title">Votre panier</h1>

        <?php if (empty($produits)): ?>
            <p class="empty-message">Votre panier est vide.</p>
        <?php else: ?>
            <form class="cart-form" method="POST">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Disponible</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produits as $produit): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produit['nom']) ?></td>
                                    <td><?= number_format($produit['prix'], 2) ?> CFA</td>
                                    <td>
                                        <input type="number" name="quantite[<?= $produit['id'] ?>]" value="<?= $produit['quantite'] ?>" min="1" max="<?= $produit['disponible'] ?>" class="input-number">
                                    </td>
                                    <td><?= htmlspecialchars($produit['disponible']) ?></td>
                                    <td><?= number_format($produit['prix'] * $produit['quantite'], 2) ?> CFA</td>
                                    <td>
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
                                        <button type="submit" name="delete_product" class="delete-button">Supprimer</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="actions">
                    <a href="index.php" class="back-button">Retour à l'accueil</a>
                    <button type="submit" name="valider_commande" class="validate-button">Valider la commande</button>
                </div>
            </form>
        <?php endif; ?>

        <h2 class="section-title">Commandes en cours</h2>
        <?php if (empty($commandes_en_cours)): ?>
            <p class="empty-message">Aucune commande en cours.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Date</th>
                            <th>État</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes_en_cours as $commande): ?>
                            <tr>
                                <td><?= htmlspecialchars($commande['id']) ?></td>
                                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                                <td><?= htmlspecialchars($commande['etat']) ?></td>
                                <td>
                                    <a href="commande_details.php?id=<?= $commande['id'] ?>" class="details-link">Voir les détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <h2 class="section-title">Commandes livrées</h2>
        <?php if (empty($commandes_livrees)): ?>
            <p class="empty-message">Aucune commande livrée.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Date</th>
                            <th>État</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes_livrees as $commande): ?>
                            <tr>
                                <td><?= htmlspecialchars($commande['id']) ?></td>
                                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                                <td><?= htmlspecialchars($commande['etat']) ?></td>
                                <td>
                                    <a href="commande_details.php?id=<?= $commande['id'] ?>" class="details-link">Voir les détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
