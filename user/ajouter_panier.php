<?php

include ("../verify.php");



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produitId = $_POST['produit_id'] ?? null;

    if ($produitId && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        
        $stmt = $conn->prepare("SELECT * FROM panier WHERE user_id = ? AND produit_id = ?");
        $stmt->execute([$userId, $produitId]);
        $cartItem = $stmt->fetch();

        if ($cartItem) {
            
        } else {
            
            $stmt = $conn->prepare("INSERT INTO panier (user_id, produit_id, quantite) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $produitId, 1]);
        }
    }
}


header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
