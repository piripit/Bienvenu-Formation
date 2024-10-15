<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Dégradé blanc sale pour le fond */
            background: linear-gradient(135deg, #f0f0f0, #d9d9d9);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .role-block {
            background-color: rgba(255, 255, 255, 0.8);
            /* Opacité sur les blocs */
            border-radius: 10px;
            padding: 55px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            

        }

        .role-block:hover {
            transform: scale(1.05);
        }

        .container {
            width: 100%;


        }

        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            /* Dégradé bleu doux */
            background-color: #0056b3;
        }

        .navbar-brand img {
            width: 40px;
            /* Taille du logo */
        }

        .navbar-nav .nav-item:last-child {
            margin-left: auto;
            /* Décale l'icône de point d'interrogation à droite */
        }

        .navbar-text {
            color: white;
            font-size: 20px;
        }

        .nav-link {
            color: white;
        }


        .page-content {
            margin-top: 100px;
            /* Ajustement pour laisser de la place à la nav en haut */
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
                margin: 0 auto;
                padding-top: 30px;
            }

            .role-block {
                margin-bottom: 30px;
                margin-left: 10px;
                /* Ajout du margin entre les deux blocs */
            }


        }
    </style>
</head>

<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Logo"> <!-- Remplacez par votre logo -->
            </a>
            <span class="navbar-text mx-auto">Bienvenue Formation</span>
            <ul class="navbar-nav d-flex">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-question-circle"></i> <!-- Icône de point d'interrogation -->
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="page-content">
        <div class="container">
            <div class="row w-100 justify-content-around">
                <div class="col-md-4 role-block" onclick="window.location.href='loginProf.php'">
                    <h2>Animateur</h2>
                </div>

                <div class="col-md-4 role-block" onclick="window.location.href='loginEtud.php'">
                    <h2>Etudiant</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-auto w-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">&copy; 2024 ESPL(MyDigitalSchool). Tous droits réservés.</p>
                    <p class="mb-1"><a href="#" class="text-white text-decoration-none">@Mentions légales</a></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">Contact : <a href="mailto:contact@entreprise.com" class="text-white text-decoration-none">contact@entreprise.com</a></p>
                    <p class="mb-1">Adresse : Pole Mandel, Angers, France</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optionally load Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
</body>

</html>