<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion d'assiduité - Professeur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #0056b3;
            /* Couleur de fond de la barre (bleu moins vif) */
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
            /* Couleur du texte en blanc */
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            /* Effet hover */
            border-radius: 4px;
            /* Coins arrondis pour l'effet hover */
        }

        .active {
            font-weight: bold;
            /* Met le lien actif en gras */
            background-color: rgba(255, 255, 255, 0.2);
            /* Couleur de fond pour le lien actif */
            border-radius: 4px;
            /* Coins arrondis pour le lien actif */
        }
    </style>
</head>

<body class="bg-light">

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestion d'assiduité</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Liste des étudiants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#attendanceModal">Faire l'appel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mon emploi du temps</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal : Liste des étudiants -->
    <div class="container mt-5">
        <h2 class="text-center">Liste des Étudiants</h2>
        <table class="table table-bordered mt-4" id="studentsTable">
            <thead class="table-primary">
                <tr>
                    <th>Nom de l'étudiant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Simulation des données d'étudiants
                $students = [
                    ['id' => 1, 'name' => 'Alice Dupont'],
                    ['id' => 2, 'name' => 'Bob Martin'],
                    ['id' => 3, 'name' => 'Chloé Durand'],
                    ['id' => 4, 'name' => 'David Lefevre'],
                    ['id' => 5, 'name' => 'Emma Moreau']
                ];

                // Affichage des étudiants avec un statut par défaut 'Absent'
                foreach ($students as $student) {
                    echo "<tr id='student-{$student['id']}'>";
                    echo "<td>{$student['name']}</td>";
                    echo "<td class='status'>Absent</td>"; // Statut par défaut
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal d'appel -->
    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Faire l'appel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="attendanceForm">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>Nom de l'étudiant</th>
                                    <th>Présence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Affichage des étudiants avec une case à cocher pour l'appel
                                foreach ($students as $student) {
                                    echo "<tr>";
                                    echo "<td>{$student['name']}</td>";
                                    echo "<td><input type='checkbox' name='present[]' value='{$student['id']}'></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="saveAttendance">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Récupérer les éléments du tableau principal
        const studentsTable = document.getElementById('studentsTable');
        const rows = studentsTable.querySelectorAll('tbody tr');

        // Réinitialiser tous les statuts à 'Absent' avant de mettre à jour
        rows.forEach(row => {
            const statusCell = row.querySelector('.status');
            if (statusCell) {
                statusCell.innerText = 'Absent';
            }
        });

        // Mettre à jour les étudiants présents
        const presentStudents = [];
        document.getElementById('saveAttendance').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll('input[name="present[]"]');
            presentStudents.length = 0; // Réinitialiser le tableau des étudiants présents

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    presentStudents.push(checkbox.value);
                }
            });

            // Mettre à jour les statuts dans le tableau principal
            rows.forEach(row => {
                const studentId = row.id.split('-')[1];
                const statusCell = row.querySelector('.status');
                if (statusCell) {
                    if (presentStudents.includes(studentId)) {
                        statusCell.innerText = 'Présent';
                    } else {
                        statusCell.innerText = 'Absent';
                    }
                }
            });

            // Fermer la fenêtre modale
            const modal = document.getElementById('attendanceModal');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        });
    </script>
</body>

</html>