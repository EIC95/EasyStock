<nav class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center shadow-md">
    <div class="flex items-center gap-4">
        <a href="index.php" class="text-xl font-semibold text-violet-400 hover:underline">EasyStock</a>
    </div>
    <div class="flex items-center gap-4">
        <a href="panier.php" class="hover:text-violet-400">Panier</a>
        <a href="profil.php" class="flex items-center gap-2 hover:text-violet-400">
          <?php
          session_start();
          include '../connection.php';
          if (isset($_SESSION['user_id'])) {
               $stmt = $conn->prepare("SELECT photo FROM users WHERE id = ?");
               $stmt->execute([$_SESSION['user_id']]);
               $user = $stmt->fetch();
               if ($user) {
                    echo '<img src="../' . htmlspecialchars($user['photo']) . '" alt="Profil" class="w-8 h-8 rounded-full border-2 border-violet-500">';
               } else {
                    echo '<img src="../uploads/profile/default.svg" alt="Profil" class="w-8 h-8 rounded-full border-2 border-violet-500">';
               }
          } else {
               echo '<img src="../uploads/profile/default.svg" alt="Profil" class="w-8 h-8 rounded-full border-2 border-violet-500">';
          }
          ?>
          <span>Profil</span>
        </a>
    </div>
</nav>
