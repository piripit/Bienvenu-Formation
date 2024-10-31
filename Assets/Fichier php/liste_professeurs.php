<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestion_cours');
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Message de modal
$modalMessage = '';
$modalType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_prof'])) {
    $prof_id = $_POST['prof_id'];

    // Supprimer les associations et le professeur
    $conn->query("DELETE FROM professeur_groupes WHERE id_professeur = $prof_id");
    $conn->query("DELETE FROM professeur_cours WHERE id_professeur = $prof_id");

    if ($conn->query("DELETE FROM professeurs WHERE id = $prof_id")) {
        $modalMessage = "Professeur supprimé avec succès.";
        $modalType = 'success';
    } else {
        $modalMessage = "Erreur lors de la suppression du professeur : " . $conn->error;
        $modalType = 'error';
    }
}

// Récupération des professeurs
$professeurs = $conn->query("SELECT * FROM professeurs");

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Professeurs - Gestion d'Assiduité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
                        <a class="nav-link" href="administration.php">Administration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="liste_professeurs.php">Liste Professeurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="addProf.php">Ajouter Professeur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emploi du temps général</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Liste des Professeurs</h2>
        <table class="table table-striped mt-4">
            <thead class="table-primary">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Cours Assignés</th>
                    <th>Groupes Assignés</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($professeurs->num_rows > 0): ?>
                    <?php while ($prof = $professeurs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $prof['nom']; ?></td>
                            <td><?php echo $prof['email']; ?></td>
                            <td>
                                <?php
                                $cours = $conn->query("SELECT nom FROM cours 
                                JOIN professeur_cours ON cours.id = professeur_cours.id_cours 
                                WHERE professeur_cours.id_professeur = {$prof['id']}");
                                while ($course = $cours->fetch_assoc()) {
                                    echo $course['nom'] . "<br>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $groupes = $conn->query("SELECT nom FROM groupes 
                                JOIN professeur_groupes ON groupes.id = professeur_groupes.id_groupe 
                                WHERE professeur_groupes.id_professeur = {$prof['id']}");
                                while ($group = $groupes->fetch_assoc()) {
                                    echo $group['nom'] . "<br>";
                                }
                                ?>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-prof-id="<?php echo $prof['id']; ?>">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun professeur inscrit</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- Modal pour confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Voulez-vous vraiment supprimer ce professeur ?
                </div>
                <div class="modal-footer">
                    <form method="POST" action="">
                        <input type="hidden" id="prof_id" name="prof_id" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="delete_prof" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour gérer l'ID du professeur dans le modal de suppression
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var profId = button.getAttribute('data-prof-id');
            var inputProfId = deleteModal.querySelector('#prof_id');
            inputProfId.value = profId;
        });
    </script>

    <!-- Script pour afficher automatiquement la modal si un message est défini -->
    <?php if ($modalMessage): ?>
        <script>
            var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
            resultModal.show();
        </script>
    <?php endif; ?>
</body>

</html>