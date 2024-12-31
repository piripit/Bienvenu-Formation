<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Cours - Gestion d'Assiduité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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

<body class="bg-light">

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
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Liste des Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal : Liste des Cours -->
    <div class="container mt-5">
        <h2 class="text-center">Liste des Cours</h2>
        <br>
        <!-- Bouton pour ajouter un nouveau cours -->
        <div class="text-center mb-4">
            <a href="ajouter_cours.php" class="btn btn-success">Ajouter un Nouveau Cours</a>
        </div>

        <!-- Liste des Cours -->
        <ul class="list-group">
            <?php
            // Connexion à la base de données
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
            class VerifyShowSubject
            {
                private $pdo;
                private $showCours = "SELECT * FROM cours";

                public function __construct($dbconn)
                {
                    $this->pdo = $dbconn; // Connexion PDO
                }
                public function VerifyToShowSubject()
                {
                    try {
                        // Exécution de la requête
                        $stmt = $this->pdo->query($this->showCours);
                        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère les résultats sous forme de tableau associatif

                        // Vérifie s'il y a des cours
                        if (!empty($courses)) {
                            foreach ($courses as $course) {
                                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>{$course['nom']}
                                <form action='supprimer_cours.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='{$course['id']}'>
                                    <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                </form>
                                </li>";
                            }
                        } else {
                            echo "<li class='list-group-item'>Aucun cours n'a été ajouté pour le moment.</li>";
                        }
                    } catch (PDOException $e) {
                        die("Erreur lors de la récupération des cours : " . $e->getMessage());
                    }
                }
            }
            $subjectVerifier = new VerifyShowSubject($pdo);
            $subjectVerifier->VerifyToShowSubject();
            ?>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>