<?php
include 'navbar.php';

$userId = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? $user['nom'];
    $tel = $_POST['tel'] ?? $user['tel'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Handle profile picture upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profile/';
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
    $stmt = $conn->prepare("UPDATE users SET nom = ?, tel = ?, photo = ? WHERE id = ?");
    $stmt->execute([$nom, $tel, $photo, $userId]);

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <main class="p-8">
        <h1 class="text-2xl text-violet-400 font-semibold mb-6">Mon Profil</h1>

        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex flex-col items-center">
                <img src="../<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" class="w-32 h-32 rounded-full border-2 border-violet-500 mb-4">
                <input type="file" name="photo" class="text-sm text-gray-400">
            </div>
            <div class="grid grid-cols-1 gap-4">
                <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" placeholder="Nom" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <input type="text" name="tel" value="<?= htmlspecialchars($user['tel']) ?>" placeholder="Téléphone" required class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <input type="password" name="password" placeholder="Nouveau mot de passe" class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" class="bg-gray-700 border border-gray-600 text-white rounded px-4 py-2">
                <button type="submit" class="mt-4 bg-violet-700 text-white rounded px-4 py-2 hover:bg-violet-800">Mettre à jour</button>
            </div>
        </form>
    </main>
</body>
</html>
