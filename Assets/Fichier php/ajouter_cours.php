<?php
// Connex$host = 'localhost';
$dbname = 'gestion_cours';
$username = 'root';
$password = '';
$host = 'localhost';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
class FormSent
{
    public $modalMessage = '';
    public $modalType = '';
    private $pdo;
    private $insertCours = "INSERT INTO cours (nom) VALUES (:nom)";

    public function __construct($dbconn)
    {
        $this->pdo = $dbconn; // Connexion PDO
    }

    public function insertCours()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nom = $_POST['nom'] ?? '';

            if (!empty($nom)) {
                try {
                    $stmt = $this->pdo->prepare($this->insertCours);
                    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        $this->modalMessage = "Le cours a été ajouté avec succès.";
                        $this->modalType = 'success';
                    } else {
                        $this->modalMessage = "Erreur lors de l'ajout du cours.";
                        $this->modalType = 'error';
                    }
                } catch (Exception $e) {
                    $this->modalMessage = "Erreur lors de l'ajout du cours : " . $e->getMessage();
                    $this->modalType = 'error';
                }
            } else {
                $this->modalMessage = "Le champ nom est requis.";
                $this->modalType = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Cours - Gestion d'Assiduité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            background-color: #0056b3;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .active {
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestion d'assiduité</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="ajouter_cours.php">Ajouter Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_cours.php">Liste des Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Ajouter un Cours</h2>

        <!-- Formulaire d'ajout de cours -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom du Cours</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ajouter</button>
        </form>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">
                        <?php echo ($modalType === 'success') ? 'Succès' : 'Erreur'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $modalMessage; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script pour afficher automatiquement la modal si une action a eu lieu -->
    <?php if ($modalMessage): ?>
        <script>
            var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            resultModal.show();
        </script>
    <?php endif; ?>
</body>

</html>