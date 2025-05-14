<?php
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $login = trim($_POST['login']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $tel = trim($_POST['tel']);
    $adresse = trim($_POST['adresse']);

    $photo = "../uploads/profile/default.svg";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profile/';
        $photo = basename($_FILES['photo']['name']);
        $targetFile = $uploadDir . $photo;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $error = "Erreur lors du téléchargement de la photo.";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (nom, prenom, login, password, tel, adresse, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $login, $password, $tel, $adresse, $photo]);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <title>EasyStock - Inscription</title>
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" required>
            </div>
            <div>
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>
            <div>
                <label for="login">Login</label>
                <input type="text" name="login" id="login" required>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="tel">Téléphone</label>
                <input type="text" name="tel" id="tel" required>
            </div>
            <div>
                <label for="adresse">Adresse</label>
                <input type="text" name="adresse" id="adresse" required>
            </div>
            <div>
                <label for="photo">Photo</label>
                <input type="file" name="photo" id="photo">
            </div>
            <button type="submit">S'inscrire</button>
        </form>

        <p class="register-link">
            Déjà un compte ?
            <a href="index.php">Connectez-vous ici</a>.
        </p>
    </div>
</body>
</html>
