<?php
session_start();

include("../connection.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom     = trim($_POST['nom'] ?? '');
    $tel     = trim($_POST['tel'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville   = trim($_POST['ville'] ?? '');
    $pays    = trim($_POST['pays'] ?? '');
    $email   = trim($_POST['email'] ?? '');

    if (!$nom || !$tel || !$adresse || !$ville || !$pays || !$email) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    $query = "SELECT COUNT(*) FROM fournisseurs WHERE nom = :nom OR tel = :tel OR email = :email";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ":nom"   => $nom,
        ":tel"   => $tel,
        ":email" => $email
    ]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $errors[] = "Un fournisseur avec le même nom, numéro ou email existe déjà.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO fournisseurs (nom, tel, adresse, ville, pays, email)
                VALUES (:nom, :tel, :adresse, :ville, :pays, :email)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ":nom"     => $nom,
            ":tel"     => $tel,
            ":adresse" => $adresse,
            ":ville"   => $ville,
            ":pays"    => $pays,
            ":email"   => $email
        ]);

        header("Location: fournisseurs.php");
        exit();
    } else {
        $_SESSION['fournisseur_errors'] = $errors;
        header("Location: fournisseurs.php");
        exit();
    }
}
