<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve'], $_POST['trajet_id'], $_POST['trajet_prix'], $_POST['trajet_conducteur'])) {
        //ajoute le fait que la personne ai reservé
        $trajet_id = $_POST['trajet_id'];
        $trajet_prix = $_POST['trajet_prix'];
        $trajet_conducteur = $_POST['trajet_conducteur'];
        $user_id = $_SESSION['username'];

        $requete = $bdd->prepare('INSERT INTO appartient (Idtrajet, Username) VALUES (?, ?)');
        $requete->execute([$trajet_id, $user_id]); //associe l'utilisateur en temps que passager

        $updatepassager = $bdd->prepare('UPDATE utilisateur SET Argent = Argent - ? WHERE Username = ?');
        $updatepassager->execute([$trajet_prix, $user_id]); //retrait argent au passager

        $updateconducteur = $bdd->prepare('UPDATE utilisateur SET Argent = Argent + ? WHERE Username = ?');
        $updateconducteur->execute([$trajet_prix, $trajet_conducteur]); //ajout de l'argent du conducteur

        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deconnexion'])) {
        session_start();
        // Démarrer la session

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
    <link rel="stylesheet" href="styleresultatrecherche.css">
    <script src="https://kit.fontawesome.com/26275dc0b6.js" crossorigin="anonymous" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsresultatrecherche.js" defer></script>
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
                <button type="submit" class="deco" name="deconnexion" action=""> Deconnexion </button>
                <div>
                    <input type="hidden" name="deconnexion" value="deconnexion">
                </div>
            </form>
        </div>
    </div>

    <div class="info-bande">
        <div class="info-item"><span><?php echo $_COOKIE['passagers'] ?></span></div>
        <div class="info-item"><span><?php echo $_COOKIE['datealler'] ?></span></div>
        <div class="info-item"><span><?php echo $_COOKIE['depart'] ?></span></div>
        <div class="info-item"><span><?php echo $_COOKIE['arrivee'] ?></span></div>
    </div>

    <div class="titre">Résultats:</div>

    <?php



    $reponse = $bdd->prepare('SELECT trajet.`Date`, trajet.Commentaire, trajet.Idtrajet, trajet.Depart, trajet.Conducteur, trajet.Placesrestantes, trajet.Arrivee, trajet.prix, utilisateur.Nom, utilisateur.Prenom, utilisateur.Vehicule, utilisateur.Model, utilisateur.Photo 
        FROM trajet 
        JOIN utilisateur ON trajet.Conducteur = utilisateur.Username
        WHERE trajet.Depart =:depart AND trajet.Arrivee =:arrivee AND DATE(trajet.`Date`) =:datealler AND trajet.Placesrestantes>= :passagers
        ORDER BY TIME(trajet.`Date`) ASC');
    $reponse->bindParam(':depart', $_COOKIE['depart']);
    $reponse->bindParam(':arrivee', $_COOKIE['arrivee']);
    $reponse->bindParam(':datealler', $_COOKIE['datealler']);
    $reponse->bindParam(':passagers', $_COOKIE['passagers']);
    $reponse->execute();
    ///ajouter quand les cookies sont mort de revenir à l'accueil
    $i = 0;

    while ($donnees = $reponse->fetch()) {
        $modalId = 'trajetModal_' . $donnees['Idtrajet'];
    ?>
        <div class="trajet" onclick="openModal('<?php echo $modalId; ?>')">
            <div class="profil">
                <?php

                // Récupération des données binaires de la photo depuis la base de données
                $stmt = $bdd->prepare('SELECT Photo FROM utilisateur WHERE Username = :username'); // Remplacez 1 par l'ID de l'utilisateur concerné
                $stmt->bindParam(':username', $donnees['Conducteur']);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (isset($row['Photo']) && empty($row['Photo']) == 0) {
                    $photoData = $row['Photo'];
                    // Enregistrement des données binaires dans un fichier
                    // Générer un nom de fichier unique basé sur le nom d'utilisateur
                    $photoFileName = 'photo_' . $donnees['Conducteur'] . '.jpg';
                    // Enregistrement des données binaires dans un fichier avec un nom unique
                    file_put_contents($photoFileName, $photoData);
                ?>
                    <img src="<?php echo $photoFileName; ?>" alt="Photo de profil"> 
                <?php
                }
                ?>
                
            </div>
            <div class="noms">
                <div class="nom">Qui : <?php echo $donnees['Nom'] ?></div><!--Nom-->
                <div class="prenom"><?php echo $donnees['Prenom'] ?></div><!--Prenom-->
                <div class="prenom">Quand : <br><?php echo $donnees['Date'] ?></div><!--date-->
            </div>
            <div class="prix"><?php echo $donnees['prix'] ?>€</div><!--prix-->
        </div>

        <div id="<?php echo $modalId; ?>" class="hidden">
            <!--ouvrir fenetre au clique-->
            <div class="modal hidden">
                <div class="modal-content">
                    <div class="close-icon" onclick="closeModal('<?php echo $modalId; ?>')">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                    <div class="profile-section">
                        <div class="profile-icon">
                        <img src="<?php echo $photoFileName; ?>" alt="Photo de profil"> 
                        </div>
                        <div class="profile-inputs">
                            <input type="text" value="<?php echo $donnees['Prenom'] ?>" class="profile-input" readonly>
                            <input type="text" value="<?php echo $donnees['Nom'] ?>" class="profile-input" readonly>
                        </div>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Départ :</label>
                        <input type="text" value="<?php echo $donnees['Depart'] ?>" class="input-field" readonly>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Destination :</label>
                        <input type="text" value="<?php echo $donnees['Arrivee'] ?>" class="input-field" readonly>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Date :</label>
                        <input type="text" value="<?php echo $donnees['Date'] ?>" class="input-field" readonly>
                    </div>

                    <div class="input-group">
                        <label class="input-label">Marque de voiture :</label>
                        <input type="text" value="<?php echo $donnees['Vehicule'] ?>" class="input-field" readonly>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Modèle de voiture :</label>
                        <input type="text" value="<?php echo $donnees['Model'] ?>" class="input-field" readonly>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Prix :</label>
                        <input type="text" value="<?php echo $donnees['prix'] ?>€" class="input-field" readonly>
                    </div>
                    <div class="input-group">
                        <label class="input-label">Commentaire :</label>
                        <input type="text" value="<?php echo $donnees['Commentaire'] ?>" class="input-field" readonly>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="trajet_id" value="<?php echo $donnees['Idtrajet'];
                                                                        //recupere l'id sans que l'utilisateur le voit
                                                                        ?>">
                        <input type="hidden" name="trajet_prix" value="<?php echo $donnees['prix'];
                                                                        //recupere le prix sans que l'utilisateur le voit
                                                                        ?>">
                        <input type="hidden" name="trajet_conducteur" value="<?php echo $donnees['Conducteur'];
                                                                                //recupere le prix sans que l'utilisateur le voit
                                                                                ?>">
                        <button type="submit" name="reserve" class="reserve-button">Réserver</button>
                    </form>

                </div>
            </div>
        </div>
    <?php
        $i++;
    }
    if ($i == 0) {
    ?>
        <div class="titre">Aucun resultat</div>
    <?php
    }

    ?>


</body>

</html>