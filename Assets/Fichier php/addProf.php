<?php
// Connex$host = 'localhost';
$dbname = 'gestion_cours';
$username = 'root';
$password = 'momo22';
$host = 'localhost';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
class FormsSent
{
    public $modalMessage = '';
    public $modalType = '';
    private $pdo;

    private $insertTeachers = "INSERT INTO professeurs (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)";
    private $insertTeachersCours = "INSERT INTO professeur_cours (id_professeur, id_cours) VALUES (:id_professeur, :id_cours)";
    private $insertTeachersClass = "INSERT INTO professeur_groupes (id_professeur, id_groupe) VALUES (:id_professeur, :id_groupe)";

    public function __construct($dbConn)
    {
        $this->pdo = $dbConn; // Récupérer la connexion PDO passée en paramètre
    }

    public function formssent()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                // Récupérer les données du formulaire
                $name = $_POST['name'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $cours = $_POST['cours'] ?? []; // Cours sélectionnés
                $groupes = $_POST['groupes'] ?? []; // Groupes sélectionnés

                // Hash du mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insérer le professeur
                $id_professeur = $this->insertTeachers($name, $email, $hashed_password);
                if (!$id_professeur) {
                    throw new Exception("Erreur lors de l'insertion du professeur.");
                }

                // Assigner le professeur aux cours
                foreach ($cours as $cours) {
                    $this->insertTeachersCours($id_professeur, $cours);
                }

                // Assigner le professeur aux groupes
                foreach ($groupes as $groupes) {
                    $this->insertTeachersClass($id_professeur, $groupes);
                }

                $this->modalMessage = "Le professeur a été ajouté avec succès.";
                $this->modalType = 'success';
            } catch (Exception $e) {
                $this->modalMessage = "Erreur : " . $e->getMessage();
                $this->modalType = 'error';
            }
        }
    }

    private function insertTeachers($nom, $email, $mot_de_passe)
    {
        $stmt = $this->pdo->prepare($this->insertTeachers);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId(); // Retourner l'ID du professeur inséré
        }
        return false;
    }

    private function insertTeachersCours($id_professeur, $id_cours)
    {
        $stmt = $this->pdo->prepare($this->insertTeachersCours);
        $stmt->bindParam(':id_professeur', $id_professeur);
        $stmt->bindParam(':id_cours', $id_cours);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'association professeur-cours.");
        }
    }

    private function insertTeachersClass($id_professeur, $id_groupe)
    {
        $stmt = $this->pdo->prepare($this->insertTeachersClass);
        $stmt->bindParam(':id_professeur', $id_professeur);
        $stmt->bindParam(':id_groupe', $id_groupe);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'association professeur-groupe.");
        }
    }
}
$formHandler = new FormsSent($pdo);
$formHandler->formssent();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Professeur - Gestion d'Assiduité</title>
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
                        <a class="nav-link active" href="addprof.php">Ajouter Professeur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="liste_professeurs.php">Liste Professeurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">Ajouter un Professeur</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nom du Professeur</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cours</label>
                <div>
                    <?php
                    $stmt = $pdo->query("SELECT id, nom FROM cours");
                    while ($course = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='form-check'>
                    <input class='form-check-input' type='checkbox' name='cours[]' value='{$course['id']}' id='course{$course['id']}'>
                    <label class='form-check-label' for='course{$course['id']}'>{$course['nom']}</label>
                  </div>";
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Groupes</label>
                <div>
                    <?php
                    $stmt = $pdo->query("SELECT id, nom FROM groupes");
                    while ($group = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<div class='form-check'>
                    <input class='form-check-input' type='checkbox' name='groupes[]' value='{$group['id']}' id='group{$group['id']}'>
                    <label class='form-check-label' for='group{$group['id']}'>{$group['nom']}</label>
                  </div>";
                    }
                    ?>
                </div>
            </div>


            <button type="submit" class="btn btn-primary w-100">Ajouter Professeur</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>