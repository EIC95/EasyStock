<?php
session_start();
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? null;
    $fields = [
        "nom" => "Le nom est obligatoire.",
        "tel" => "Le téléphone est obligatoire.",
        "adresse" => "L'adresse est obligatoire.",
        "ville" => "La ville est obligatoire.",
        "pays" => "Le pays est obligatoire.",
        "email" => "Email invalide."
    ];

    $errors = [];
    foreach ($fields as $field => $error) {
        $$field = trim($_POST[$field] ?? "");
        if (!$$field || ($field === "email" && !filter_var($$field, FILTER_VALIDATE_EMAIL))) {
            $errors[] = $error;
        }
    }

    if (!$id) {
        $errors[] = "ID du fournisseur manquant.";
    }

    if (!empty($errors)) {
        $_SESSION["fournisseur_errors"] = $errors;
        header("Location: fournisseurs.php?page=" . ($_GET['page'] ?? 1));
        exit;
    }

    $stmt = $conn->prepare("UPDATE fournisseurs SET nom = ?, tel = ?, adresse = ?, ville = ?, pays = ?, email = ? WHERE id = ?");
    $stmt->execute([$nom, $tel, $adresse, $ville, $pays, $email, $id]);

    $_SESSION['success_message'] = "Fournisseur modifié avec succès.";
    header("Location: fournisseurs.php?page=" . ($_GET['page'] ?? 1));
    exit;
} else {
    header("Location: fournisseurs.php");
    exit;
}
