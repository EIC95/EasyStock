<?php
     session_start();
     include("../connection.php");

     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $nom = trim($_POST['nom']);
     $errors = [];

     // Validation
     if (empty($nom)) {
          $errors[] = "Le nom de la catégorie est requis.";
     } elseif (strlen($nom) > 255) {
          $errors[] = "Le nom de la catégorie ne doit pas dépasser 255 caractères.";
     }

     if (!empty($errors)) {
          $_SESSION['categorie_errors'] = $errors;
          header("Location: categories.php");
          exit;
     }

     try {
          // Insertion dans la base de données
          $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
          $stmt->bindParam(':name', $nom, PDO::PARAM_STR);
          $stmt->execute();

          $_SESSION['success_message'] = "Catégorie ajoutée avec succès.";
     } catch (PDOException $e) {
          $_SESSION['categorie_errors'] = ["Erreur lors de l'ajout de la catégorie : " . $e->getMessage()];
     }

     header("Location: categories.php");
     exit;
     }
?>