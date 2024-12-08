<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération des données pour le calendrier
$events = [];
$result = $conn->query("
    SELECT edt.id, c.nom AS cours, p.nom AS professeur, edt.date_heure
    FROM emploi_du_temps_professeur edt
    JOIN cours c ON edt.id_cours = c.id
    JOIN professeurs p ON edt.id_professeur = p.id
");
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['cours'] . " - " . $row['professeur'],
        'start' => $row['date_heure'],
    ];
}

// Conversion en JSON
$events_json = json_encode($events);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps - Calendrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Emploi du Temps - Vue Calendrier</h2>

        <!-- Bouton pour ajouter un événement -->
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addEventModal">Ajouter un Cours</button>

        <!-- Calendrier -->
        <div id="calendar"></div>
    </div>

    <!-- Modal pour ajouter un événement -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="emploi_du_temps_admin.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventModalLabel">Ajouter un Cours</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="professeur" class="form-label">Professeur</label>
                            <select class="form-select" id="professeur" name="professeur" required>
                                <option value="">Sélectionnez un professeur</option>
                                <?php
                                $professeurs = $conn->query("SELECT * FROM professeurs");
                                while ($row = $professeurs->fetch_assoc()):
                                ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['nom'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cours" class="form-label">Cours</label>
                            <select class="form-select" id="cours" name="cours" required>
                                <option value="">Sélectionnez un cours</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_heure" class="form-label">Date et Heure</label>
                            <input type="datetime-local" class="form-control" id="date_heure" name="date_heure" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="action" value="ajouter">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.getElementById('professeur').addEventListener('change', function() {
            const professeurId = this.value;

            if (professeurId) {
                fetch(`get_courses.php?professeur_id=${professeurId}`)
                    .then(response => response.json())
                    .then(data => {
                        const coursSelect = document.getElementById('cours');
                        coursSelect.innerHTML = '<option value="">Sélectionnez un cours</option>';

                        data.forEach(cours => {
                            const option = document.createElement('option');
                            option.value = cours.id;
                            option.textContent = cours.nom;
                            coursSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        });
    </script>
</body>

</html>