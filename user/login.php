<?php 
    $error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        include '../connection.php';

        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        try {
            $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ?");
            $stmt->bindParam(1, $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {                
                session_start();
                $_SESSION['user_id'] = $user['ID'];
                header("Location: home.php");
            } else {
                $error = "Email ou mot de passe incorrect";
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
    <div class="w-full max-w-md py-10 rounded-lg">
        <h1 class="text-3xl font-medium mb-5 text-gray-100">Connexion</h1>
        <p class="text-red-400 my-2">
            <?php echo $error; ?>
        </p>
        <form action="/user/index.php" class="space-y-6" method="post">
            <div>
                <label for="email" class="text-sm text-gray-400">Email</label>
                <input type="email" name="email" id="email" placeholder="exemple@mail.com" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <div>
                <label for="password" class="text-sm text-gray-400">Mot de passe</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required
                    class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-violet-500 text-base py-1 placeholder-gray-500" 
                />
            </div>
            <button 
                type="submit"
                class="w-full mt-8 py-2 text-center bg-violet-600 text-white text-sm rounded-md hover:bg-violet-700 transition"
            >
                Se connecter
            </button>
        </form>
        <div class="flex gap-2 mt-4 justify-center text-gray-400">
            <p>Vous n'avez pas de compte ? </p>
            <a href="register.php" class="text-violet-500 hover:text-violet-400">Inscrivez-vous</a>
        </div>
    </div>
</body>
</html>
