<?php 
    $error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        include '../connection.php';

        $prenom = trim($_POST['prenom']);
        $nom = trim($_POST['nom']);
        $email = trim($_POST['email']);
        $tel = trim($_POST['tel']);
        $password = trim($_POST['password']);
        $photo = $_FILES['photo'];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $photoPath = '../uploads/profile/default.svg'; 
        if ($photo['error'] === UPLOAD_ERR_OK) {
            $photoPath = '../uploads/profile/' . basename($photo['name']);
            if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
                $error = "Erreur lors du téléchargement de la photo.";
            }
        }

        if (empty($error)) {
            try {
                $checkStmt = $conn->prepare("SELECT COUNT(*) FROM clients WHERE email = ? OR tel = ?");
                $checkStmt->bindParam(1, $email);
                $checkStmt->bindParam(2, $tel);
                $checkStmt->execute();
                $exists = $checkStmt->fetchColumn();

                if ($exists > 0) {
                    $error = "L'email ou le téléphone est déjà utilisé.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO clients (prenom, nom, email, tel, password, photo) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bindParam(1, $prenom);
                    $stmt->bindParam(2, $nom);
                    $stmt->bindParam(3, $email);
                    $stmt->bindParam(4, $tel);
                    $stmt->bindParam(5, $hashed_password);
                    $stmt->bindParam(6, $photoPath);

                    if ($stmt->execute()) {
                        header(("Location: index.php"));
                    } else {
                        $error = "Erreur lors de l'inscription";
                    }
                }
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
    <div class="w-full max-w-md py-10 bg-gray-800 rounded-lg shadow-lg">
        <h1 class="text-3xl font-medium mb-5 text-gray-100">Créer un compte</h1>
        <p class="text-red-400 my-2">
            <?php echo $error; ?>
        </p>
        <form action="register.php" class="space-y-6" method="post" enctype="multipart/form-data">
            <div>
                <label for="prenom" class="text-sm text-gray-400">Prénom</label>
                <input type="text" name="prenom" id="prenom" placeholder="John" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <div>
                <label for="nom" class="text-sm text-gray-400">Nom</label>
                <input type="text" name="nom" id="nom" placeholder="Doe" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <div>
                <label for="email" class="text-sm text-gray-400">Email</label>
                <input type="email" name="email" id="email" placeholder="exemple@mail.com" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <div>
                <label for="tel" class="text-sm text-gray-400">Téléphone</label>
                <input type="text" name="tel" id="tel" placeholder="77 070 77 00" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <div>
                <label for="password" class="text-sm text-gray-400">Mot de passe</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <div>
                <label for="photo" class="text-sm text-gray-400">Photo</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                    class="w-full file:hidden bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <button 
                type="submit"
                class="w-full mt-8 py-2 text-center bg-violet-600 text-white text-sm rounded-md hover:bg-violet-700 transition"
            >
                Créer un compte
            </button>
        </form>
        <div class="flex gap-2 text-gray-400 justify-center mt-4">
            <p>Vous avez deja un compte ? </p>
            <a href="login.php" class="text-violet-500 hover:text-violet-400">Connectez-vous</a>
        </div>
    </div>
</body>
</html>
