<?php 
    session_start();

    if($_SESSION['user_id']){
        include("../connection.php");
        
        // Fetch user details
        $user_id = $_SESSION['user_id'];
        $query = "SELECT prenom, nom FROM admins WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $prenom = $user['prenom'];
        $nom = $user['nom'];
    }else{
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyStock - Accueil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <?php include("sidebar.php") ?>

    <main class="ml-64 flex flex-col items-center justify-center min-h-screen text-center px-4">
    <img src="../assets/logo.svg" alt="Logo EasyStock" class="w-50 h-20 mb-6 opacity-80" />
    <h1 class="text-4xl font-semibold text-violet-600">Bienvenue <?php echo $prenom . ' ' . $nom; ?></h1>
    </main>
</body>
</html>
