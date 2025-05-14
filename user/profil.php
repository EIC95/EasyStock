<?php
session_start();
require_once "../verify.php";
include 'navbar.php';

$userId = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? $user['nom'];
    $prenom = $_POST['prenom'] ?? $user['prenom'];
    $tel = $_POST['tel'] ?? $user['tel'];
    $adresse = $_POST['adresse'] ?? $user['adresse'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Handle profile picture upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profile/';
        $fileName = basename($_FILES['photo']['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
            $photo = 'uploads/profile/' . $fileName;
        } else {
            $photo = $user['photo'];
        }
    } else {
        $photo = $user['photo'];
    }

    // Update user details
    $stmt = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, tel = ?, adresse = ?, photo = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $tel, $adresse, $photo, $userId]);

    // Update password if provided and matches confirmation
    if (!empty($password) && $password === $confirmPassword) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
    }

    header('Location: profil.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="user-style.css">
</head>
<body>
    <main class="main-content">
        <h1 class="page-title">Mon Profil</h1>

        <form method="POST" enctype="multipart/form-data" class="profile-form">
            <div class="profile-picture-section">
                <img src="<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" class="profile-picture">
                <input type="file" name="photo" class="file-input">
            </div>
            <div class="profile-details-section">
                <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" placeholder="Nom" required class="input-field">
                <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" placeholder="Prénom" required class="input-field">
                <input type="text" name="tel" value="<?= htmlspecialchars($user['tel']) ?>" placeholder="Téléphone" required class="input-field">
                <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>" placeholder="Adresse" required class="input-field">
                <input type="password" name="password" placeholder="Nouveau mot de passe" class="input-field">
                <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" class="input-field">
                <button type="submit" class="submit-button">Mettre à jour</button>
            </div>
        </form>
    </main>
</body>
</html>
