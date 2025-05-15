<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) ? $_POST['role'] : 'user';

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE login = ? AND role = ?");
        $stmt->execute([$login, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit;
        } else {
            $error = "Login, mot de passe ou rôle incorrect";
        }
    } catch (Exception $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <title>EasyStock - Connexion</title>
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="post">
            <div>
                <label for="login">Login</label>
                <input type="text" name="login" id="login" required>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="role">Rôle</label>
                <select name="role" id="role" required>
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            <button type="submit">Se connecter</button>
        </form>

        <p class="register-link">
            Pas de compte ?
            <a href="register.php">Inscrivez-vous ici</a>.
        </p>
    </div>
</body>
</html>