<?php
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
    
    if (isset($_POST['logout'])) {
        session_start();
        unset($_SESSION['user_id']);
        session_destroy();
        header("Location: ../index.php");
    }
?>

<aside class="h-screen w-64 bg-gray-800 border-r border-gray-700 fixed top-0 left-0 flex flex-col justify-between">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-violet-400 mb-8">EasyStock</h2>
        <nav class="space-y-4 text-sm text-gray-300">
            <a href="home.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Acceuil</a>
            <a href="clients.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des clients</a>
            <a href="fournisseurs.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des fournisseurs</a>
            <a href="commandes.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des commandes</a>
            <a href="stocks.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des stocks</a>
            <a href="livraisons.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des livraisons</a>
            <a href="factures.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des facturations</a>
            <a href="categories.php" class="block px-2 py-1 rounded hover:bg-violet-500 hover:text-white transition">Gestion des catégories</a>
        </nav>
    </div>
    <div class="p-6">
        <form action="/admin/sidebar.php" method="POST">
            <button type="submit" name="logout" class="w-full px-4 py-2 text-sm text-white bg-violet-600 rounded hover:bg-violet-700 transition">
                Déconnexion
            </button>
        </form>
    </div>
</aside>
