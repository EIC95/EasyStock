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
    $role = 'user';

    // Handle file upload
    $photo = "uploads/profile/default.svg";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profile/';
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
            $stmt = $conn->prepare("INSERT INTO users (nom, prenom, login, password, tel, adresse, photo, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $login, $password, $tel, $adresse, $photo, $role]);
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
    <script src="https://cdn.tailwindcss.com"></script>
    <title>EasyStock - Inscription</title>
</head>
<body class="bg-gray-900 text-gray-200 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md px-6 py-10 bg-gray-800 rounded-lg shadow-lg">
        <h1 class="text-3xl font-medium mb-6 text-center text-violet-400">Inscription</h1>

        <?php if ($error): ?>
            <div class="bg-red-200 text-red-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" class="space-y-5" method="post" enctype="multipart/form-data">
            <div>
                <label for="nom" class="block text-sm text-gray-400 mb-1">Nom</label>
                <input type="text" name="nom" id="nom" required
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="prenom" class="block text-sm text-gray-400 mb-1">Prénom</label>
                <input type="text" name="prenom" id="prenom" required
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="login" class="block text-sm text-gray-400 mb-1">Login</label>
                <input type="text" name="login" id="login" required
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="password" class="block text-sm text-gray-400 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password" required
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="tel" class="block text-sm text-gray-400 mb-1">Téléphone</label>
                <input type="text" name="tel" id="tel" required
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="adresse" class="block text-sm text-gray-400 mb-1">Adresse</label>
                <input type="text" name="adresse" id="adresse" required
                       class="w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label for="photo" class="block text-sm text-gray-400 mb-1">Photo (Fichier)</label>
                <input type="file" name="photo" id="photo"
                       class="file:hidden w-full bg-gray-700 border border-gray-600 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <button type="submit"
                    class="w-full bg-violet-600 hover:bg-violet-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
                S'inscrire
            </button>
        </form>

        <p class="text-sm text-gray-400 mt-4 text-center">
            Déjà un compte ?
            <a href="index.php" class="text-violet-400 hover:underline">Connectez-vous ici</a>.
        </p>
    </div>
</body>
</html>
