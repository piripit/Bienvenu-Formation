<?php
// Connexion à la base de données
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
class VerifyDataSent
{
    private $pdo;
    private $updateGroupStudent = "UPDATE etudiants SET id_groupe = :id_groupe WHERE id = :id";

    public function __construct($dbConn)
    {
        $this->pdo = $dbConn; // Récupérer la connexion PDO passée en paramètre
    }

    public function updateGroupStudent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id_groupe = $_POST['id_groupe'];

            if (!empty($id_groupe)) {
                $stmt = $this->pdo->prepare($this->updateGroupStudent);
                $stmt->bindParam(':id_groupe', $id_groupe, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                try {
                    if ($stmt->execute()) {
                        echo "L'étudiant a été affecté avec succès au groupe.";
                    } else {
                        echo "Erreur lors de l'affectation de l'étudiant au groupe.";
                    }
                } catch (Exception $e) {
                    echo "Erreur : " . $e->getMessage();
                }
            } else {
                echo "Veuillez sélectionner un groupe.";
            }
        }
    }
}

// Fermeture de la connexion à la base de données
$conn->close();
