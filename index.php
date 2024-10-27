<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    session_start();
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
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve'], $_POST['trajet_id'], $_POST['trajet_prix'], $_POST['trajet_conducteur'], $_POST['nbpassager'])) {
        //ajoute le fait que la personne ait réservé
        $trajet_id = $_POST['trajet_id'];
        $trajet_prix = $_POST['trajet_prix'];
        $trajet_conducteur = $_POST['trajet_conducteur'];
        $user_id = $_SESSION['username'];
        $nbpassager = $_POST['nbpassager'];

        // Vérifier si l'utilisateur est déjà associé au trajet
        $checkQuery = $bdd->prepare('SELECT appartient.Places_reservees FROM appartient WHERE Idtrajet = ? AND Username = ?');
        $checkQuery->execute([$trajet_id, $user_id]);
        if ($checkQuery->fetch()) {
            //alors mes a jour les places reservees et le prix

            $prixtotal = $trajet_prix * $nbpassager;
            $updatepassager = $bdd->prepare('UPDATE utilisateur SET Argent = Argent - ? WHERE Username = ?');
            $updatepassager->execute([$prixtotal, $user_id]); //retrait argent au passager

            $updateconducteur = $bdd->prepare('UPDATE utilisateur SET Argent = Argent + ? WHERE Username = ?');
            $updateconducteur->execute([$prixtotal, $trajet_conducteur]); //ajout de l'argent au conducteur

            $updateplacesrestantes = $bdd->prepare('UPDATE trajet SET Placesrestantes = Placesrestantes - ? WHERE Idtrajet = ?');
            $updateplacesrestantes->execute([$nbpassager, $trajet_id]); //retrait des places

            $updateplacesreservees = $bdd->prepare('UPDATE appartient SET Places_reservees = Places_reservees + ? WHERE Idtrajet = ? AND Username = ?');
            $updateplacesreservees->execute([$nbpassager, $trajet_id, $user_id]); //ajout des places réservées

            $idbot = 2;
            // Récupération de l'id du chat du conducteur avec le bot
            $idchat = $bdd->prepare('SELECT Idchat FROM chat WHERE Username1 = ? AND Username2 = ?');
            $idchat->execute([$trajet_conducteur, $idbot]);
            $result = $idchat->fetch(PDO::FETCH_ASSOC);
            $messagebot = 'Nouveau passager';
            $time = new DateTime();
            // Envoi du premier message
            $messagenvpassager = $bdd->prepare('INSERT INTO message (Idchat, Message, Date, Envoyeur) VALUES (?, ?, ?, ?)');
            $messagenvpassager->execute([$result['Idchat'], $messagebot, $time->format('Y-m-d H:i:s'), $idbot]);

            header('Location: index.php');
            exit();
        }
        $requete = $bdd->prepare('INSERT INTO appartient (Idtrajet, Username, Places_reservees) VALUES (?, ?, ?)');
        $requete->execute([$trajet_id, $user_id, $nbpassager]); //associe l'utilisateur en tant que passager

        $prixtotal = $trajet_prix * $nbpassager;
        $updatepassager = $bdd->prepare('UPDATE utilisateur SET Argent = Argent - ? WHERE Username = ?');
        $updatepassager->execute([$prixtotal, $user_id]); //retrait argent au passager

        $updateconducteur = $bdd->prepare('UPDATE utilisateur SET Argent = Argent + ? WHERE Username = ?');
        $updateconducteur->execute([$prixtotal, $trajet_conducteur]); //ajout de l'argent au conducteur

        $updateplacesrestantes = $bdd->prepare('UPDATE trajet SET Placesrestantes = Placesrestantes - ? WHERE Idtrajet = ?');
        $updateplacesrestantes->execute([$nbpassager, $trajet_id]); //retrait des places

        $idbot = 2;
        // Récupération de l'id du chat du conducteur avec le bot
        $idchat = $bdd->prepare('SELECT Idchat FROM chat WHERE Username1 = ? AND Username2 = ?');
        $idchat->execute([$trajet_conducteur, $idbot]);
        $result = $idchat->fetch(PDO::FETCH_ASSOC);
        $messagebot = 'Nouveau passager';
        $time = new DateTime();
        // Envoi du premier message
        $messagenvpassager = $bdd->prepare('INSERT INTO message (Idchat, Message, Date, Envoyeur) VALUES (?, ?, ?, ?)');
        $messagenvpassager->execute([$result['Idchat'], $messagebot, $time->format('Y-m-d H:i:s'), $idbot]);

        header('Location: index.php');
        exit();
    }

    // Récupérer les adresses des campus
    $campusAdresses = array();
    $reponse = $bdd->query('SELECT Nom, Adresse FROM campus');
    while ($donnees = $reponse->fetch(PDO::FETCH_ASSOC)) {
        $campusAdresses[$donnees['Nom']] = $donnees['Adresse'];
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
    <link rel="stylesheet" href="styleaccueil.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" defer></script>
    <script src="jsaccueil.js" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC_nX474z61iyDOfP51E556yAFoDayjFC0&callback=initMap&libraries=places" loading=async defer></script>
    <script>
        // Transmettre les adresses des campus au JavaScript
        var campusAdresses = <?php echo json_encode($campusAdresses); ?>;
    </script>
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
                <button type="submit" class="deco" name="deconnexion" action=""> Deconnexion </button>
                <div>
                    <input type="hidden" name="deconnexion" value="deconnexion">
                </div>
            </form>
        </div>
    </div>

    <div class="rectanglefond bg-white my-12 p-6 shadow-lg">
        <form class="h-full w-full" method="POST" action="traitementrecherche.php">
            <h2 class="titre-principal">Le voyage démarre maintenant</h2>
            <div class="circleButton2"></div>
            <?php /*verifie si erreur=7*/
            if (isset($_GET['erreur'])  && empty($_GET['erreur']) == 0) {
                $erreur = (int) $_GET['erreur']; /*verifie si l'erreur existe*/
                if ($erreur == 7) {
            ?>
                    <br>
                    <p class="text-red-500 text-2xl text-center pb-5">Verifiez que la date au minimum actuelle </p>
            <?php
                }
            }
            ?>
            <!-- Groupe 1 : Départ et Arrivée -->
            <div class="group-container1">

                <div class="input-container z-0">
                    <select name="nouvel_input" required>
                        <option value=""></option>

                        <?php
                        $reponse = $bdd->query('SELECT Nom FROM campus');
                        $i = 0;
                        while ($donnees = $reponse->fetch()) {
                            $i = $i + 1;
                            $stmt = $bdd->prepare('SELECT Nom FROM campus WHERE IdCampus = :i');
                            $stmt->bindParam(':i', $i);
                            $stmt->execute();
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                            <option value="<?php echo $row['Nom'] ?>"><?php echo $row['Nom'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label class="label">Campus concerné</label>
                    <div class="underline"></div>

                </div>
                <div class="icone3">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="icone2">
                    <i class="fas fa-location-dot"></i>
                </div>
                <div class="input-container z-0 margin1">
                    <input type="text" id="depart" autocomplete="off" name="depart" placeholder=" " readonly>
                    <label class="label">Départ</label>
                    <div class="underline"></div>
                </div>
                <div class="swap-button">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="icone2">
                    <i class="fas fa-location-dot"></i>
                </div>
                <div class="input-container z-0 margin2">
                    <input type="text" id="arrivee" autocomplete="off" name="arrivee" placeholder=" " required>
                    <label class="label">Arrivée</label>
                    <div class="underline"></div>
                </div>
                <div id="act">Utiliser la carte</div>
            </div>

            <div id="map" class="hidden" style="height: 500px; width: 100%;"></div>

            <div class="circleButton2"></div>

            <!-- Groupe 2 : Dates -->
            <div class="group-container2">
                <div class="icone3">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="input-container z-0 margin3">
                    <input class="dateInput" type="text" autocomplete="off" name="datealler" required>
                    <label class="label">Date</label>
                    <div class="underline"></div>
                </div>
            </div>

            <div class="circleButton2"></div>

            <!-- Groupe 3 : Nombre de passagers -->
            <div class="group-container3">
                <div class="icone4">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-container z-0">
                    <input type="number" autocomplete="off" name="passagers" min="1" required>
                    <label class="label">Nombre de passagers</label>
                    <div class="underline"></div>
                </div>
            </div>

            <div class="circleButton2"></div>

            <button type="submit" class="recherche">Rechercher</button>
        </form>
    </div>

    <?php
    if (isset($_GET['recherche'])  && empty($_GET['recherche']) == 0) {




        $reponse = $bdd->prepare('SELECT trajet.`Date`, trajet.Commentaire, trajet.Idtrajet, trajet.Depart, trajet.Conducteur, trajet.Placesrestantes, trajet.Arrivee, trajet.prix, utilisateur.Nom, utilisateur.Prenom, utilisateur.Vehicule, utilisateur.Model, utilisateur.Photo 
            FROM trajet 
            JOIN utilisateur ON trajet.Conducteur = utilisateur.Username
            WHERE trajet.Depart = :depart 
            AND trajet.Arrivee = :arrivee 
            AND DATE(trajet.`Date`) = :datealler 
            AND trajet.Placesrestantes >= :passagers
            AND trajet.Conducteur != :username
            ORDER BY TIME(trajet.`Date`) ASC'); 
            // Requête SQL pour récupérer les trajets disponibles
        $reponse->bindParam(':depart', $_COOKIE['depart']);
        $reponse->bindParam(':arrivee', $_COOKIE['arrivee']);
        $reponse->bindParam(':datealler', $_COOKIE['datealler']);
        $reponse->bindParam(':passagers', $_COOKIE['passagers']);
        $reponse->bindParam(':username', $_SESSION['username']);
        // Récupere les données des cookies si une recherche a été effectuée précédemment
        $reponse->execute();

        $i = 0; // Compteur pour vérifier si des résultats ont été trouvés

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
                <div class="modal">
                    <div class="modal-content">
                        <div class="close-icon" onclick="closeModal('<?php echo $modalId; ?>')"> <!--fermer fenetre au clique-->
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                        <div class="profile-section"> <!--affichage des informations du conducteur-->
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
                            <?php
                            $nbpassager = $_COOKIE['passagers']; //recupere le nombre de passager des cookies
                            ?>
                            <input type="hidden" name="trajet_id" value="<?php echo $donnees['Idtrajet'];
                                                                            //recupere l'id sans que l'utilisateur le voit
                                                                            ?>">
                            <input type="hidden" name="trajet_prix" value="<?php echo $donnees['prix'];
                                                                            //recupere le prix sans que l'utilisateur le voit
                                                                            ?>">
                            <input type="hidden" name="trajet_conducteur" value="<?php echo $donnees['Conducteur'];
                                                                                    //recupere le prix sans que l'utilisateur le voit
                                                                                    ?>">
                            <input type="hidden" name="nbpassager" value="<?php echo $nbpassager;
                                                                            //recupere le nombre de passager sans que l'utilisateur le voit
                                                                            ?>">
                            <button type="submit" name="reserve" class="reserve-button">Réserver</button>
                        </form>

                    </div>
                </div>
            </div>
        <?php
            $i++; // Incrémenter le compteur
        }
        if ($i == 0) {
        ?>
            <h2 class="titre-principal">Aucun résultat</h2> <!--affiche si aucun trajet n'est trouvé grace au compteur-->
        <?php
        }

        ?>
    <?php
    }
    ?>
</body>

</html>