<?php
    if (isset($_POST['logout'])) {
        session_start();
        unset($_SESSION['user_id']);
        session_destroy();
        header("Location: ../index.php");
    }
?>

<aside class="h-screen w-64 bg-white border-r border-gray-200 fixed top-0 left-0 flex flex-col justify-between">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-violet-600 mb-8">EasyStock</h2>
        <nav class="space-y-4 text-sm text-gray-700">
            <a href="home.php" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Acceuil</a>
            <a href="clients.php" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Gestion des clients</a>
            <a href="/fournisseurs" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Gestion des fournisseurs</a>
            <a href="/commandes" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Gestion des commandes</a>
            <a href="/stocks" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Gestion des stocks</a>
            <a href="/livraisons" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Gestion des livraisons</a>
            <a href="/factures" class="block px-2 py-1 rounded hover:bg-violet-100 hover:text-violet-600 transition">Gestion des facturations</a>
        </nav>
    </div>
    <div class="p-6">
        <form action="sidebar.php" method="POST">
            <button type="submit" name="logout" class="w-full px-4 py-2 text-sm text-white bg-violet-500 rounded hover:bg-violet-600 transition">
                Déconnexion
            </button>
        </form>
    </div>
</aside>
