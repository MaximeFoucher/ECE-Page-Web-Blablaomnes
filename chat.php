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
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['conversation'], $_POST['username'])) {

        $cookie_duration = time() + (1200 * 1); // 1200 secondes = 20 mins
        $username = $_POST['username'];

        // Stocker les données dans des cookies
        setcookie('chatusername', $username, $cookie_duration, "/");

        header('Location: messagechat.php'); //redirige vers la page de conversation
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['botchat'])) { //pour ouvrir le chat du bot

        header('Location: messagechat.php'); //redirige vers la page de conversation
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajout'], $_POST['username_a_ajouter'], $_POST['user_session'])) {

        $usernameajouter = $_POST['username_a_ajouter'];
        $user = $_POST['user_session'];

        $add = $bdd->prepare('INSERT INTO chat (Username1, Username2) VALUES (?, ?)');
        $add->execute([$user, $usernameajouter]);

        header('Location: chat.php'); //actualise la page
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
    <link rel="stylesheet" href="stylechat.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jschat.js" defer></script>
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
                <li class="h-10 text-center hover:bg-stone-200"><a href="profil.php" class="waitkey">Mon profil</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="mestrajets.php" class="waitkey">Mes trajets</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="chat.php" class="waitkey">Messagerie</a></li><br><br>
            </ul>
            <form method="POST" action="index.php">
                <button type="submit" class="deco" name="deconnexion" action=""> Deconnexion </button>
                <div>
                    <input type="hidden" name="deconnexion" value="deconnexion">
                </div>
            </form>
        </div>
    </div>
    <!-- fin bandeau -->

    <h2 class="titre">Messagerie</h2>

    <div class="crayon" onclick="openModal('myModal')">
        <i class="fa-solid fa-pen"></i>
    </div>
    <?php
    $chat = $bdd->prepare('SELECT utilisateur.Photo, utilisateur.Nom, utilisateur.Prenom, utilisateur.Username  
    FROM utilisateur 
    JOIN chat ON (utilisateur.Username = chat.Username1 OR utilisateur.Username = chat.Username2)
    WHERE :username IN (chat.Username1, chat.Username2)
    AND utilisateur.Username != :username');
    //recupere les nom et prenom des utilisateur ayant un chat avec la personne de la session
    $chat->bindParam(':username', $_SESSION['username']);
    $chat->execute();

    $i = 0; //pour verifier s'il y a au moins un resultat (compteur)
    while ($donnees = $chat->fetch()) {
        //$modalId = 'trajetModal_' . $donnees['Username'];

    ?>
        <form method="POST" action="chat.php" <?php
                                                if ($i == 0) {
                                                ?> class="" <?php
                                                        } else {
                                                            ?> class="-mt-40" <?php
                                                                            }
                                                                                ?>>
            <button type="submit" name="conversation">
                <input type="hidden" name="username" value="<?php echo $donnees['Username'];
                                                            //recupere l'id sans que l'utilisateur le voit
                                                            ?>">
                <div class="profil1">
                    <div class="profil">
                        <?php

                        // Récupération des données binaires de la photo depuis la base de données
                        $stmt = $bdd->prepare('SELECT Photo FROM utilisateur WHERE Username = :username'); // Remplacez 1 par l'ID de l'utilisateur concerné
                        $stmt->bindParam(':username', $donnees['Username']);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (isset($row['Photo']) && empty($row['Photo']) == 0) {
                            $photoData = $row['Photo'];
                            // Enregistrement des données binaires dans un fichier
                            // Générer un nom de fichier unique basé sur le nom d'utilisateur
                            $photoFileName = 'photo_' . $donnees['Username'] . '.jpg';
                            // Enregistrement des données binaires dans un fichier avec un nom unique
                            file_put_contents($photoFileName, $photoData);
                        ?>
                            <img src="<?php echo $photoFileName; ?>" alt="Photo de profil">
                        <?php
                        }
                        ?>
                    </div>
                    <div class="noms">
                        <div class="nom"><?php echo $donnees['Prenom'] ?></div>
                        <div class="prenom"><?php echo $donnees['Nom'] ?></div>
                        <?php
                        $envoyeur = $donnees['Username'];
                        // trouver dans la table message si l'utilisateur a des message non lus de l'envoyeur
                        $req = $bdd->prepare('SELECT COUNT(*) AS nb FROM message WHERE  Envoyeur = :envoyeur AND Lu = 0');
                        $req->bindParam(':envoyeur', $envoyeur);
                        $req->execute();
                        $nb_messages_non_lus = $req->fetch();
                        if ($nb_messages_non_lus['nb'] > 0) {
                        ?>
                            <p class="text-red-600 text-3xl text-center">Messages non lus: <?php echo $nb_messages_non_lus['nb']; ?></p>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </button>
        </form>
    <?php
        $i++;
    }
    /*if ($i == 0) {
    ?>
        <br>
        <div class="titre">Ouvrez une nouvelle discussion avec le crayon !</div>
    <?php
    }*/

    ?>
    <div id="myModal" class="hidden">
        <!-- Modal -->
        <div class="modal">
            <div class="modal-content">
                <div class="close-icon" onclick="closeModal('myModal')">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <?php
                $ajouterchat = $bdd->prepare('SELECT u.Username, u.Nom, u.Prenom, u.Photo
                FROM utilisateur u
                WHERE u.Username IN (
                    -- Sélectionne les noms d utilisateur des utilisateurs
                    -- qui sont sur les mêmes trajets que l utilisateur user
                    SELECT appartient.Username
                    FROM appartient
                    WHERE appartient.Idtrajet IN (
                        -- Sélectionne les identifiants de trajet sur lesquels
                        -- l utilisateur username est associé
                        SELECT a1.Idtrajet
                        FROM appartient a1
                        WHERE a1.Username = :username
                    )
                    AND appartient.Username != :username
                )
                AND u.Username NOT IN (
                    -- Sélectionne les noms d utilisateur des utilisateurs
                    -- avec lesquels l utilisateur 11 a déjà eu une conversation
                    SELECT
                        CASE
                            WHEN chat.Username1 = :username THEN chat.Username2 
                            WHEN chat.Username2 = :username THEN chat.Username1
                        END
                    FROM chat 
                    WHERE :username IN (chat.Username1, chat.Username2)
                );
                ');
                //recupere les nom et prenom des utilisateur ayant un chat avec la personne de la session
                $ajouterchat->bindParam(':username', $_SESSION['username']);
                $ajouterchat->execute();
                $j = 0; //pour verifier s'il y a au moins un resultat (compteur)
                while ($donnees1 = $ajouterchat->fetch()) {
                    $modalId = 'trajetModal_' . $donnees1['Username'];
                ?>
                    <form method="POST" action="chat.php">
                        <button type="submit" name="ajout">
                            <input type="hidden" name="username_a_ajouter" value="<?php echo $donnees1['Username'];
                                                                                    //recupere le username sans que l'utilisateur le voit
                                                                                    ?>">
                            <input type="hidden" name="user_session" value="<?php echo $_SESSION['username'];
                                                                            //recupere l'id de la session sans que l'utilisateur le voit
                                                                            ?>">
                            <div class="profil2 ">
                                <div class="profil3 pr-80">
                                    <?php

                                    // Récupération des données binaires de la photo depuis la base de données
                                    $stmt = $bdd->prepare('SELECT Photo FROM utilisateur WHERE Username = :username'); // Remplacez 1 par l'ID de l'utilisateur concerné
                                    $stmt->bindParam(':username', $donnees1['Username']);
                                    $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                    if (isset($row['Photo']) && empty($row['Photo']) == 0) {
                                        $photoData = $row['Photo'];
                                        // Enregistrement des données binaires dans un fichier
                                        // Générer un nom de fichier unique basé sur le nom d'utilisateur
                                        $photoFileName = 'photo_' . $donnees1['Username'] . '.jpg';
                                        // Enregistrement des données binaires dans un fichier avec un nom unique
                                        file_put_contents($photoFileName, $photoData);
                                    ?>
                                        <img src="<?php echo $photoFileName; ?>" alt="Photo de profil">
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="noms1 ml-20">
                                    <div class="nom1"><?php echo $donnees1['Prenom'] ?></div>
                                    <div class="prenom1"><?php echo $donnees1['Nom'] ?></div>
                                </div>
                            </div>
                        </button>
                    </form>
                <?php
                    $j++;
                }
                if ($j == 0) {
                ?>
                    <br>
                    <div class="titre">Vous avez deja toutes les messageries pour organiser vos voyages !</div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>