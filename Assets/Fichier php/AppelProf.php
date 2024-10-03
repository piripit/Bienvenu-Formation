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
                                    <th>Présent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Affichage des étudiants dans la modal
                                foreach ($students as $student) {
                                    echo "<tr>";
                                    echo "<td>{$student['name']}</td>";
                                    echo "<td><input type='checkbox' name='attendance[]' value='{$student['id']}'></td>"; // Checkbox pour chaque étudiant
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="submitAttendance">Enregistrer l'appel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('submitAttendance').addEventListener('click', function() {
            // Récupérer les IDs des étudiants présents
            const form = document.getElementById('attendanceForm');
            const formData = new FormData(form);
            const presentStudents = formData.getAll('attendance');

            // Affichage des IDs des étudiants présents pour vérification
            console.log('Étudiants présents : ', presentStudents);

            // Mettre à jour les statuts dans le tableau principal
            const studentsTable = document.getElementById('studentsTable');
            const rows = studentsTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            // Réinitialiser tous les statuts à 'Absent' avant de mettre à jour
            for (let i = 0; i < rows.length; i++) {
                rows[i].getElementsByClassName('status')[0].innerText = 'Absent';
            }

            // Mettre à jour les étudiants présents
            presentStudents.forEach(studentId => {
                const studentRow = document.getElementById(`student-${studentId}`);
                const statusCell = studentRow.querySelector('.status');
                if (statusCell) {
                    statusCell.innerText = 'Présent'; // Mettre à jour le statut à "Présent"
                }
            });

            // Afficher un message avec les étudiants présents
            alert('Étudiants présents : ' + presentStudents.join(', '));

            // Fermer la modal
            const attendanceModal = bootstrap.Modal.getInstance(document.getElementById('attendanceModal'));
            attendanceModal.hide();
        });
    </script>
</body>

</html>