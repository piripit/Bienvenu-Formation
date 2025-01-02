<?php
// Connexion à la base de données avec PDO
try {
    $conn = new PDO('mysql:host=localhost;dbname=gestion_cours', 'root', 'momo22');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérification si un formulaire a été soumis
$feedback = '';
$feedback_class = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $professeur = $_POST['professeur'];
    $cours = $_POST['cours'];
    $date_heure = $_POST['date_heure'];

    // Validation des champs
    if (!$professeur || !$cours || !$date_heure) {
        $feedback = "Tous les champs sont obligatoires !";
        $feedback_class = "alert-danger";
    } else {
        // Vérification si le créneau est déjà occupé
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM emploi_du_temps_professeur 
            WHERE id_professeur = :professeur AND date_heure = :date_heure
        ");
        $stmt->execute([
            ':professeur' => $professeur,
            ':date_heure' => $date_heure
        ]);

        if ($stmt->fetchColumn() > 0) {
            $feedback = "Ce créneau est déjà occupé pour ce professeur.";
            $feedback_class = "alert-warning";
        } else {
            // Insertion dans la base de données
            $stmt = $conn->prepare("
                INSERT INTO emploi_du_temps_professeur (id_professeur, id_cours, date_heure) 
                VALUES (:professeur, :cours, :date_heure)
            ");
            if ($stmt->execute([
                ':professeur' => $professeur,
                ':cours' => $cours,
                ':date_heure' => $date_heure
            ])) {
                $feedback = "Cours ajouté avec succès !";
                $feedback_class = "alert-success";
            } else {
                $feedback = "Erreur lors de l'ajout du cours.";
                $feedback_class = "alert-danger";
            }
        }
    }
}

// Récupération des données pour le calendrier
$events = [];
$stmt = $conn->query("
    SELECT edt.id, c.nom AS cours, p.nom AS professeur, edt.date_heure
    FROM emploi_du_temps_professeur edt
    JOIN cours c ON edt.id_cours = c.id
    JOIN professeurs p ON edt.id_professeur = p.id
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

        <?php if ($feedback): ?>
            <div class="alert <?= $feedback_class; ?>"><?= $feedback; ?></div>
        <?php endif; ?>

        <!-- Bouton pour ajouter un événement -->
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addEventModal">Ajouter un Cours</button>

        <!-- Calendrier -->
        <div id="calendar"></div>
    </div>

    <!-- Modal pour ajouter un événement -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
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
                                while ($row = $professeurs->fetch(PDO::FETCH_ASSOC)):
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

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                events: <?= $events_json; ?>
            });
            calendar.render();
        });
    </script>
</body>

</html>