<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $conn = new PDO('mysql:host=localhost;dbname=gestion_cours', 'root', 'momo22');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérification des informations d'identification
        $stmt = $conn->prepare("SELECT id, nom, mot_de_passe FROM professeurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $professeur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($professeur && password_verify($password, $professeur['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['prof_id'] = $professeur['id'];
            $_SESSION['prof_nom'] = $professeur['nom'];
            header("Location: prof_dashboard.php"); // Redirection vers le tableau de bord
            exit;
        } else {
            // Échec de la connexion
            $message = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Erreur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <?= isset($message) ? htmlspecialchars($message) : "Une erreur s'est produite." ?>
        </div>
        <a href="loginprof.php" class="btn btn-primary">Retour à la page de connexion</a>
    </div>
</body>

</html>