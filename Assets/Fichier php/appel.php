<?php
session_start();

// Vérification de la session professeur
if (!isset($_SESSION['prof_id'])) {
    header("Location: login.php");
    exit;
}

$id_cours = $_GET['id_cours'];
$prof_id = $_SESSION['prof_id'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=gestion_cours', 'root', 'momo22');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification que le cours appartient au professeur
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM professeur_cours 
        WHERE id_professeur = :prof_id AND id_cours = :id_cours
    ");
    $stmt->execute([':prof_id' => $prof_id, ':id_cours' => $id_cours]);
    if ($stmt->fetchColumn() == 0) {
        die("Accès non autorisé.");
    }

    // Récupération des étudiants inscrits au cours
    $stmt = $conn->prepare("
        SELECT e.id, e.nom 
        FROM etudiants e
        JOIN etudiant_cours ec ON e.id = ec.id_etudiant
        WHERE ec.id_cours = :id_cours
    ");
    $stmt->execute([':id_cours' => $id_cours]);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Enregistrement des présences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['status'] as $id_etudiant => $status) {
        $retard = isset($_POST['retard'][$id_etudiant]) ? 1 : 0;

        $stmt = $conn->prepare("
            INSERT INTO emploi_du_temps_etudiant (id_etudiant, id_cours, date, status, retard)
            VALUES (:id_etudiant, :id_cours, NOW(), :status, :retard)
        ");
        $stmt->execute([
            ':id_etudiant' => $id_etudiant,
            ':id_cours' => $id_cours,
            ':status' => $status,
            ':retard' => $retard,
        ]);
    }
    header("Location: prof_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Appel des étudiants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Appel pour le cours</h2>
        <form method="POST">
            <table class="table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Présence</th>
                        <th>Retard</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <tr>
                            <td><?= htmlspecialchars($etudiant['nom']); ?></td>
                            <td>
                                <select name="status[<?= $etudiant['id']; ?>]" class="form-select">
                                    <option value="P">Présent</option>
                                    <option value="A">Absent</option>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" name="retard[<?= $etudiant['id']; ?>]" value="1">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Enregistrer</button>
        </form>
    </div>
</body>

</html>