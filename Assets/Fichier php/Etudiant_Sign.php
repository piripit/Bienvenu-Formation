<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Simulation de l'identifiant étudiant et cours
$id_etudiant = 2;
$id_cours = 1;

// Vérifier si l'appel est clôturé
$cloture_result = $conn->query("SELECT cloture FROM appel WHERE id_cours = $id_cours AND id_etudiant = $id_etudiant");
$is_clotured = $cloture_result->fetch_assoc()['cloture'];

// Si l'appel n'est pas clôturé, permettre la signature
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sign'])) {
    if (!$is_clotured) {
        $conn->query("UPDATE appel SET signe = 1 WHERE id_cours = $id_cours AND id_etudiant = $id_etudiant");
    }
}

// Récupérer le statut de signature
$sign_result = $conn->query("SELECT signe FROM appel WHERE id_cours = $id_cours AND id_etudiant = $id_etudiant");
$signe = $sign_result->fetch_assoc()['signe'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Étudiant - Signature de Présence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 500px;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Signature de Présence</h2>
        <?php if ($is_clotured): ?>
            <div class="alert alert-warning text-center">L'appel est clôturé. Vous ne pouvez plus signer.</div>
        <?php else: ?>
            <form action="" method="POST">
                <div class="text-center mt-4">
                    <button type="submit" name="sign" class="btn btn-primary" <?php echo ($signe ? 'disabled' : ''); ?>>
                        <?php echo ($signe ? 'Signé' : 'Signer la Présence'); ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>