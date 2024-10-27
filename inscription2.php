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
    <link rel="stylesheet" href="styleinscription2.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsinscription2.js" defer></script>
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
                <li class="h-10 text-center hover:bg-stone-200"><a href="connexion.php" class="waitkey">Connexion</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="inscription1.php" class="waitkey">Inscription</a></li><br><br>
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
        <form class="registration-form" action="traitement_inscription2.php" method="post" enctype="multipart/form-data">
            <h2>Inscription</h2>
            <?php /*verifie si erreur=3*/
            if (isset($_GET['erreur'])  && empty($_GET['erreur']) == 0) {
                $erreur = (int) $_GET['erreur']; /*verifie si l'erreur existe*/
                if ($erreur == 3) {
            ?>
                    <br>
                    <p class="text-red-600 text-3xl text-center">Le numero ne semble pas correct</p>
                <?php
                } elseif ($erreur == 4) {/*verifie si erreur=4*/
                ?>
                    <br>
                    <p class="text-red-600 text-3xl text-center">L'age ne semble pas correct</p>
            <?php
                }
            }
            ?>
            <div class="group">
                <label for="firstname">Prénom</label>
                <input id="firstname" name="firstname" type="text" placeholder="ex : Pierre" required>
            </div>
            <div class="group">
                <label for="lastname">Nom</label>
                <input id="lastname" name="lastname" type="text" placeholder="ex : Dupont" required>
            </div>
            <div class="group">
                <label for="birthdate">Date de naissance</label>
                <input id="birthdate" name="birthdate" type="date">
            </div>
            <div class="group">
                <label for="phone">Numéro de téléphone</label>
                <input id="phone" name="tel" type="tel" placeholder="ex : 06 00 00 00 00" required>
            </div>
            <div class="group">
                <label for="profile-photo">Photo de profil</label>
                <input id="profile-photo" name="profile-photo" type="file" accept="image/*" onchange="previewImage(event)" required>
            </div>
            <img id="imagePreview" src="#" alt="Prévisualisation de l'image" style="display: none; max-width: 100%;">
            <div class="group">
                <label for="conducteur">Inscription comme Conducteur ?</label>
                <input id="conducteur" name="conducteur" type="checkbox">
            </div>
            <button type="submit" class="inscript2">Continuer</button>
        </form>
    </div>
</body>

</html>