<?php
session_start();

// Vérification de la session professeur
if (!isset($_SESSION['prof_id'])) {
    header("Location: loginProf.php");
    exit;
}

$prof_id = $_SESSION['prof_id'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=gestion_cours', 'root', 'momo22');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les cours du professeur
    $stmt = $conn->prepare("
        SELECT c.id, c.nom 
        FROM professeur_cours pc
        JOIN cours c ON pc.id_cours = c.id
        WHERE pc.id_professeur = :prof_id
    ");
    $stmt->execute([':prof_id' => $prof_id]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['prof_nom']); ?></h2>
        <h4>Vos cours :</h4>
        <ul class="list-group">
            <?php foreach ($cours as $cour): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($cour['nom']); ?>
                    <a href="appel.php?id_cours=<?= $cour['id']; ?>" class="btn btn-primary btn-sm float-end">Faire l'appel</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>