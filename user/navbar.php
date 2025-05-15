<?php
include ("../verify.php");
?>
<nav class="navbar">
    <div class="navbar-left">
        <a href="index.php" class="brand">EasyStock</a>
    </div>
    <div class="navbar-right">
        <a href="index.php" class="nav-link">Acceuil</a>
        <a href="panier.php" class="nav-link">Panier</a>
        <a href="profil.php" class="nav-link profile-link">
            <?php
            $stmt = $conn->prepare("SELECT photo FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            if ($user) {
                echo '<img src="' . htmlspecialchars($user['photo']) . '" alt="Profil" class="profile-img">';
            } else {
                echo '<img src="../uploads/profile/default.svg" alt="Profil" class="profile-img">';
            }
            ?>
            <span>Profil</span>
        </a>
        <a href="logout.php" class="nav-link logout">Déconnexion</a>
    </div>
</nav>
