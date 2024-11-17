<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Paramètres d'emploi du temps
$jours_semaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
$heure_debut = new DateTime('08:30');
$interval = new DateInterval('PT2H'); // Créneaux de 2 heures
$heure_fin = new DateTime('17:30');
$pause_debut = new DateTime('12:30');
$pause_fin = new DateTime('13:30');

// Génération de l'emploi du temps pour chaque professeur
$professeurs = $conn->query("SELECT id FROM professeurs");

while ($professeur = $professeurs->fetch_assoc()) {
    $heures_total = 0;
    foreach ($jours_semaine as $jour) {
        $heure_cours = clone $heure_debut;

        // Boucle pour ajouter des créneaux jusqu'à atteindre les 15h de cours
        while ($heure_cours < $heure_fin && $heures_total < 15) {
            // Vérifier si l'heure actuelle est pendant la pause
            if ($heure_cours >= $pause_debut && $heure_cours < $pause_fin) {
                $heure_cours->add($interval);
                continue;
            }

            // Sélection d'un cours pour le créneau associé au professeur
            $cours_result = $conn->query("
                SELECT id FROM cours 
                WHERE id_professeur = {$professeur['id']} 
                ORDER BY RAND() LIMIT 1
            ");
            $cours = $cours_result->fetch_assoc();

            // Insérer le créneau dans l'emploi du temps
            $date_heure = $heure_cours->format('Y-m-d H:i:s');
            $conn->query("
                INSERT INTO emploi_du_temps_professeur (id_professeur, id_cours, date_heure) 
                VALUES ({$professeur['id']}, {$cours['id']}, '$date_heure')
            ");

            $heure_cours->add($interval);
            $heures_total += 2; // Créneau de 2 heures
        }

        if ($heures_total >= 15) {
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #0056b3;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Gestion d'assiduité</a>
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
                        <a class="nav-link active" href="emploi_du_temps_admin.php">Emploi du Temps</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal : Emploi du Temps -->
    <div class="container mt-5">
        <h2 class="text-center">Emploi du Temps Général</h2>

        <!-- Sélectionner un groupe -->
        <div class="mb-4">
            <form method="GET" action="emploi_du_temps_admin.php">
                <label for="groupe" class="form-label">Choisir un Groupe d'Étudiants</label>
                <select class="form-select" id="groupe" name="groupe" required>
                    <option value="">Sélectionnez un groupe</option>
                    <?php
                    $groupes = $conn->query("SELECT * FROM groupes");
                    while ($row = $groupes->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nom']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary mt-3">Voir l'emploi du temps</button>
            </form>
        </div>

        <!-- Affichage de l'emploi du temps du groupe sélectionné -->
        <?php
        if (isset($_GET['groupe']) && !empty($_GET['groupe'])) {
            $id_groupe = $_GET['groupe'];
            echo "<h4 class='mt-4'>Emploi du temps du groupe sélectionné</h4>";

            $emploi = $conn->query("
                SELECT c.nom AS cours, p.nom AS professeur, edp.date_heure 
                FROM emploi_du_temps_professeur edp
                JOIN cours c ON edp.id_cours = c.id
                JOIN professeurs p ON edp.id_professeur = p.id
                WHERE edp.id_cours IN (
                    SELECT edt.id_cours 
                    FROM emploi_du_temps_etudiant edt
                    JOIN etudiants e ON edt.id_etudiant = e.id
                    WHERE e.id_groupe = {$id_groupe}
                )
                ORDER BY edp.date_heure
            ");

            if ($emploi->num_rows > 0) {
                echo "<table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>Cours</th>
                                <th>Professeur</th>
                                <th>Date et Heure</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $emploi->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['cours']}</td>
                            <td>{$row['professeur']}</td>
                            <td>" . date('d/m/Y H:i', strtotime($row['date_heure'])) . "</td>
                          </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>Aucun emploi du temps disponible pour ce groupe.</p>";
            }
        }

        $conn->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>