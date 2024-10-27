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
    <link rel="stylesheet" href="stylemessagechat.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsmessagechat.js" defer></script>
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
                    <li class="h-10 text-center hover:bg-stone-200"><a href="index.php" class="waitkey">Rechercher un
                            trajet</a></li><br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="typetrajet.php" class="waitkey">Publier un
                            trajet</a></li>
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
                    <li class="h-10 text-center hover:bg-stone-200"><a href="profil.php" class="waitkey">Mon profil</a></li>
                    <br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="mestrajets.php" class="waitkey">Mes trajets</a>
                    </li><br><br>
                <?php
                } else {
                ?>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="connexion.php" class="waitkey">Connexion</a>
                    </li><br><br>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="inscription1.php" class="waitkey">Inscritpion</a></li><br><br>
                <?php
                }
                ?>
            </ul>
            <form method="POST" action="index.php">
                <button type="submit" class="deco" name="deconnexion" action=""> Deconexion </button>
                <div>
                    <input type="hidden" name="deconnexion" value="deconnexion">
                </div>
            </form>
        </div>
    </div>


    <a href="chat.php">
        <div id="retour">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </a>

    <?php
    try {
        $bdd = new PDO(
            'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
            'root',
            '',
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        // Utilisation de la cookie chatusername
        $username = $_COOKIE['chatusername'];
        // recupération du nom et prenom de l'utilisateur
        $req = $bdd->prepare('SELECT nom, prenom FROM utilisateur WHERE Username = :username');
        $req->execute(array('username' => $username));
        $resultat = $req->fetch();
        $nom = $resultat['nom'];
        $prenom = $resultat['prenom'];
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    ?>

    <h2 class="nom"><?php echo $nom, " ", $prenom; ?></h2>

    <div class="chat-container">
        <div class="chat-window" id="chat-window">
            <!-- Les messages apparaîtront ici -->
            <?php
    try {
        $bdd = new PDO(
            'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
            'root',
            '',
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        // Récupérer les messages envoyés par l'utilisateur actuel à un autre utilisateur
        $envoyeur = $_SESSION['username']; // l'utilisateur actuel
        $receveur = $_COOKIE['chatusername']; // Personne a qui l'utilisateur actuel envoie le message

        // trouver le id du chat entre les deux utilisateurs
        $req = $bdd->prepare('SELECT Idchat FROM chat WHERE (Username1 = :envoyeur AND Username2 = :receveur) 
        OR (Username1 = :receveur AND Username2 = :envoyeur)');
        $req->execute(array('envoyeur' => $envoyeur, 'receveur' => $receveur));
        $resultat = $req->fetch();
        $idchat = $resultat['Idchat'];

        // Mettre tous les messages reçus de l'utilisateur actuel à lu
        $req = $bdd->prepare('UPDATE message SET Lu = 1 WHERE Idchat = :idchat AND Envoyeur = :receveur');
        $req->execute(array('idchat' => $idchat, 'receveur' => $receveur));

        // Récupérer les ID des messages déjà existants
        $req = $bdd->prepare('SELECT Idmessage FROM message WHERE Idchat = :idchat');
        $req->execute(array('idchat' => $idchat));
        $resultat = $req->fetchAll();
        foreach ($resultat as $row) {
            $idmessage = $row['Idmessage'];
            // Récupérer les messages
            $req = $bdd->prepare('SELECT * FROM message WHERE Idmessage = :idmessage');
            $req->execute(array('idmessage' => $idmessage));
            $resultat = $req->fetch();
            $message = $resultat['Message'];
            $envoyeur_message = $resultat['Envoyeur'];
            if ($envoyeur_message == $envoyeur) {
                // afficher le message du côté droit
                ?>
            <div class="message">
                <div class="user-message"><?php echo $message; ?></div>
            </div>
            <?php        
            } else {
                // afficher le message du côté gauche
                ?>
            <div class="message">
                <div class="other-message"><?php echo $message; ?></div>
            </div>
            <?php 
            }
        }
        
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    ?>
        </div>
        <div class="chat-input">
            <form method="POST" action="traitement_messagechat.php">
            <input type="text" id="message-input" name="message"<?php if ($receveur == 2){?> readonly <?php }?> placeholder="Tapez votre message...">
            <button type="submit" id="send-button">Envoyer</button>
            </form>
        </div>
    </div>

</body>

</html>