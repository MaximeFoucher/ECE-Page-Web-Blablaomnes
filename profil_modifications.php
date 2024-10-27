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
    <link rel="stylesheet" href="styleprofil.css">
    <script src="https://kit.fontawesome.com/26275dc0b6.js" crossorigin="anonymous" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsprofil.js"></script>
</head>

<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    session_start();
    if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $bdd->prepare("SELECT * FROM utilisateur WHERE Username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nom = $row['Nom'];
    $prenom = $row['Prenom'];
    $dateNaissance = $row['Date'];
    $email = $row['Email'];
    $telephone = $row['Tel'];
    $motDePasse = $row['Pwd'];
    $vehicule = $row['Vehicule'];
    $statPermis = $row['Etat_conducteur'];
    } 
    else {
    header('Location: connexion.html');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

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
        <form class="profil-form" action="traitement_profil.php" method="post">
            <h2>Mon profil</h2>
            <?php
            try {
                $stmt = $bdd->prepare("SELECT Photo FROM utilisateur WHERE Username = :username");
                $stmt->bindParam(':username', $username, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $photoData = $row['Photo'];
                file_put_contents('photo.jpg', $photoData);
            } catch (Exception $e) {
                echo 'Erreur : ' . $e->getMessage();
            }
            ?>
            <img src="photo.jpg" alt="photo">
            <h3><?php echo htmlspecialchars($nom . ' ' . $prenom); ?></h3>

            <div class="group">
                <label for="dateNaissance">Date de naissance</label>
                <input id="dateNaissance" name="dateNaissance" type="date" class="input" value="<?php echo htmlspecialchars($dateNaissance); ?>">
            </div>
            <div class="group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" class="input" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="group">
                <label for="tel">Téléphone</label>
                <input id="tel" name="tel" type="tel" class="input" value="<?php echo htmlspecialchars($telephone); ?>">
            </div>
            <div class="group">
                <label for="mdp">Mot de passe</label>
                <input id="motDePasse" name="pwd" type="password" class="input" value="<?php echo htmlspecialchars($motDePasse); ?>">
            </div>
            <div class="group">
                <label for="vehicule">Véhicule</label>
                <input id="vehicule" name="vehicule" type="text" class="input" value="<?php echo htmlspecialchars($vehicule); ?>">
            </div>
            <div class="group">
                <label for="permis">Stat du permis</label>
                <input id="permis" name="permis" type="text" class="input" value="<?php echo ($statPermis == 1) ? 'vérifié' : 'non-vérifié'; ?>">
            </div>
            <button type="submit" name="button_valider" id="btnValider">Valider</button>
        </form>
        <form class="profil-form" action="traitement_supprimer.php" method="post">
            <button type="submit" name="button_supprimer" id="btnSupprimer">Supprimer compte</button>
        </form>    
    </div>
</body>
</html>
