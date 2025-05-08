<?php
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit;
        } else {
            $error = "Login ou mot de passe incorrect";
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
    <script src="https://cdn.tailwindcss.com"></script>
    <title>EasyStock - Connexion</title>
</head>
<body class="bg-gray-900 text-gray-200 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md py-10 px-6 bg-gray-800 rounded-lg shadow-lg">
        <h1 class="text-3xl font-medium mb-6 text-center text-violet-400">Connexion</h1>

        <?php if ($error): ?>
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="index.php" class="space-y-6" method="post">
            <div>
                <label for="login" class="block text-sm text-gray-400 mb-1">Login</label>
                <input type="text" name="login" id="login" required
                    class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="password" class="block text-sm text-gray-400 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password" required
                    class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <button type="submit"
                class="w-full bg-violet-600 hover:bg-violet-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
                Se connecter
            </button>
        </form>

        <p class="text-sm text-gray-400 mt-4 text-center">
            Pas de compte ?
            <a href="register.php" class="text-violet-400 hover:underline">Inscrivez-vous ici</a>.
        </p>
    </div>
</body>
</html>