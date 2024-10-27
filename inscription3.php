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
    <link rel="stylesheet" href="styleinscription3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsinscription3.js" defer></script>
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
                <li class="h-10 text-center hover:bg-stone-200"><a href="index.php" class="waitkey">Rechercher un trajet</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="typetrajet.php" class="waitkey">Publier un trajet</a></li>
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
                <li class="h-10 text-center hover:bg-stone-200"><a href="profil.php" class="waitkey">Mon profil</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="mestrajets.php" class="waitkey">Mes trajets</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="chat.php" class="waitkey">Messagerie</a></li><br><br>

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
        <form class="registration-form" action="traitement_inscription3.php" method="post" enctype="multipart/form-data">
            <h2>Inscrivez-vous comme conducteur</h2>
            <?php /*verifie si erreur=5*/
            if (isset($_GET['erreur'])  && empty($_GET['erreur']) == 0) {
                $erreur = (int) $_GET['erreur']; /*verifie si l'erreur existe*/
                if ($erreur == 5) {
            ?>
                    <br>
                    <p class="text-red-600 text-3xl text-center">La plaque d'immatriculation ne semble pas correcte</p>
            <?php
                }
            }
            ?>
            <div class="group">
                <label for="marque_du_vehicule">Marque du véhicule</label>
                <input placeholder="ex : Peugeot" name="marque_du_vehicule" type="text" class="input" required>
            </div>
            <div class="group">
                <label for="modele_du_vehicule">Modèle du véhicule</label>
                <input placeholder="ex : 2008" name="modele_du_vehicule" type="text" class="input">
            </div>

            <div class="group">
                <label for="immatriculation">Immatriculation</label>
                <input placeholder="ex : AA-123-BB" name="immatriculation" type="text" class="input" required>
            </div>

            <div class="group">
                <label for="Permis_de_conduire">Permis de conduire</label>
                <input id="Permis_de_conduire" name="Permis_de_conduire" type="file" accept="image/*" onchange="previewImage(event)" required>
            </div>
            <!-- Balise img pour prévisualiser l'image -->
            <img id="imagePreview" src="#" alt="Prévisualisation de l'image" style="display: none; max-width: 100%;">
            <button type="submit" class="inscript3">Valider</button>

        </form>
    </div>

</body>

</html>