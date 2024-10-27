<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deconnexion'])) {
        // Démarrer la session
        session_start();

        // Libérer toutes les variables de session
        session_unset();

        // Détruire la session
        session_destroy();
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>BlaBla Omnes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/26275dc0b6.js" crossorigin="anonymous" defer></script>
    <link rel="stylesheet" href="styleinscription1.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsinscription1.js" defer></script>
</head>

<body class="h-screen">
    <div class="header duration-500 flex justify-between bg-gradient-to-r from-slate-500 to-slate-500/75 text-center sticky top-0 text-4xl h-10 sm:h-1/5 sm:py-2 md:h-20 md:text-1xl lg:text-2xl lg:h-20 xl:text-2xl xl:h-20 z-20">
        <!-- Icône de Menu Burger -->
        <div id="icone">
            <i class="fas fa-bars"></i>
        </div>

        <div id="logo1">
            <img src="images/file.png" alt="Logo" class="h-10">
        </div>

        <div id="menu1">
            <div id="close">
                <i class="fa-solid fa-xmark"></i>
            </div>

            <div id="logo">
                <img src="images/file.png" alt="Logo" class="h-10">
            </div>

            <ul>
            <?php
                // Démarrez la session
                session_start();

                // Vérifiez si l'utilisateur est connecté
                if (isset($_SESSION['username'])) {
                    // Affichez le lien pour publier un trajet seulement si l'utilisateur est connecté
                ?>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="index.php" class="waitkey">Rechercher un trajet</a></li><br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="typetrajet.php" class="waitkey">Publier un trajet</a></li>
                <?php
                }
                ?>
            </ul>
        </div>

        <div id="profil">
            <i class="fa-solid fa-user"></i>
        </div>

        <div id="menu2">
            <div id="close2">
                <i class="fa-solid fa-xmark"></i>
            </div>

            <div id="logo">
                <img src="images/file.png" alt="Logo" class="h-10">
            </div>

            <ul>
            <?php

                // Vérifiez si l'utilisateur est connecté
                if (isset($_SESSION['username'])) {
                    // Affichez le lien pour publier un trajet seulement si l'utilisateur est connecté
                ?>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="profil.php" class="waitkey">Mon profil</a></li><br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="mestrajets.php" class="waitkey">Mes trajets</a></li><br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="chat.php" class="waitkey">Messagerie</a></li><br><br>

                <?php
                } else {
                ?>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="connexion.php" class="waitkey">Connexion</a></li><br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="inscription1.php" class="waitkey">Inscription</a></li><br><br>
                <?php
                }
                ?>
            </ul>
            <form method="POST" action="index.php">
                <button type="submit" class = "deco" name="deconnexion" action=""> Deconnexion </button>
                <div>
                    <input type="hidden" name="deconnexion" value="deconnexion">
                </div>
            </form>
        </div>
    </div>



    <div class="container">
        <form class="registration-form" action="traitement_inscription1.php" method="post">

            <h2>Inscription</h2>

            <?php /*verifie si erreur=2*/
            if (isset($_GET['erreur'])  && empty($_GET['erreur']) == 0) {
                $erreur = (int) $_GET['erreur']; /*verifie si l'erreur existe*/
                if ($erreur == 2) {
                    ?>
                <p class="attention">Verifiez votre email (adresse Omnes)</p>
                <?php
            }
                elseif ($erreur == 1) {/*verifie si erreur=1*/
                    ?>
                <br>
                <p class="attention">Le mot de passe doit etre le même</p>
                <br>
                <?php
                }
                elseif ($erreur == 20) {/*verifie si erreur=20*/
                    ?>
                <br>
                <p class="attention">Email deja utilisée</p>
                <br>
                <?php
                }
            }
            ?>

            <div class="group">
                <div>
                    <label for="email">E-mail</label>
                </div>
                <div>
                    <input id="email" name="email" placeholder="ex : pierre.dupont@edu.ece.fr" type="email" class="input" required>
                </div>
            </div>

            <div class="group">
                <div>
                    <label for="password">Mot de passe</label>
                </div>
                <div>
                    <input id="password" name="password" placeholder="************" type="password" class="input" required>
                </div>
            </div>

            <div class="group">
                <div>
                    <label for="confirm-password">Confirmez le mot de passe</label>
                </div>
                <div>
                    <input id="confirm-password" name="confirm-password" placeholder="************" type="password" class="input" required>
                </div>
            </div>

            <button type="submit" class="inscript1">Suivant</button>

            <p class="login-link">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>

        </form>
    </div>
</body>

</html>