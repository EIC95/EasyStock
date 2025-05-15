<?php
    
    include ("../verify.php");
    

    $id = $_POST['id'] ?? null;
    $page = $_GET['page'] ?? 1;

    if ($id) {
        $query = "DELETE FROM fournisseurs WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
    }

    header("Location: fournisseurs.php?page=$page");
    exit();
?>
