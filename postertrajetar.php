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
    <link rel="stylesheet" href="stylepostertrajetar.css">
    <script src="https://kit.fontawesome.com/26275dc0b6.js" crossorigin="anonymous" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jspostertrajetar.js" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC_nX474z61iyDOfP51E556yAFoDayjFC0&callback=initMap&libraries=places" loading=async defer></script>
    <script>
        // Transmettre les adresses des campus au JavaScript
        var campusAdresses = <?php echo json_encode($campusAdresses); ?>;
    </script>
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
    if ($_SESSION['username'] != null) {
        $username = $_SESSION['username'];
        // Verifier si l'utilisateur en question et registrer comme conducteur dans la bdd
        $stmt = $bdd->prepare('SELECT Etat_conducteur FROM utilisateur WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $etat_conducteur = $row['Etat_conducteur'];
        if ($etat_conducteur == 0) {
            header('Location: typetrajet.php?erreur=13');
        }
    } else {
        header('Location: connexion.php');
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
                <button type="submit" class="deco" name="deconnexion" action=""> Deconnexion </button>
                <div>
                    <input type="hidden" name="deconnexion" value="deconnexion">
                </div>
            </form>
        </div>
    </div>

    <div class="container">

        <form class="poster-form" method="POST" action="traitement_postertrajerar.php">

            <h2>Poster un trajet Aller-Retour</h2>
            <?php /*verifie si erreur=11*/
            if (isset($_GET['erreur'])  && empty($_GET['erreur']) == 0) {
                $erreur = (int) $_GET['erreur']; /*verifie si l'erreur existe*/
                if ($erreur == 11) {
            ?>
                    <br>
                    <p class="text-red-600 text-3xl text-center">La date aller n'est pas correcte</p>
                <?php
                } elseif ($erreur == 12) {/*verifie si erreur=12*/
                ?>
                    <br>
                    <p class="text-red-600 text-3xl text-center">La date retour n'est pas correcte</p>
            <?php
                }
            }
            ?>

            <div class="group">

                <div class="lieu">
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
                                $row = $stmt->fetch(PDO::FETCH_ASSOC); //$row['Nom']
                            ?>
                                <option value="<?php echo $row['Nom'] ?>"><?php echo $row['Nom'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <label class="label">Campus concerné</label>
                        <div class="underline"></div>
                        <div class="icone3">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                    </div>

                    <div class="input-container z-0 margin1">
                        <input type="text" autocomplete="off" name="depart" id="depart" placeholder=" " readonly>
                        <label class="label">Départ</label>
                        <div class="underline"></div>
                        <div class="icone2">
                            <i class="fas fa-location-dot"></i>
                        </div>
                    </div>

                    <div class="swap-button">
                        <i class="fas fa-exchange-alt"></i>
                    </div>

                    <div class="input-container z-0 margin2">
                        <input type="text" autocomplete="off" id="arrivee" placeholder=" " name="arrivee" required>
                        <label class="label">Arrivée</label>
                        <div class="underline"></div>
                        <div class="icone2">
                            <i class="fas fa-location-dot"></i>
                        </div>
                    </div>
                </div>

                <div id="act">Utiliser la carte</div>
                <div id="map" class="hidden" style="height: 500px; width: 100%;"></div>

                <div class="date">
                    <div class="date1">
                        <div class="input-container z-0 margin3">
                            <input class="dateInput" type="text" autocomplete="off" name="datealler" required>
                            <label class="label">Date de l'aller</label>
                            <div class="underline">
                            </div>
                            <div class="icone3">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                        </div>

                        <div class="date2">
                            <div class="input-container z-0 margin3">
                                <input class="dateInput" type="text" autocomplete="off" name="dateretour" required>
                                <label class="label">Date du retour</label>
                                <div class="underline">
                                </div>
                                <div class="icone3">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>
                            </div>
                        </div>

                        <div class="heure1">
                            <div class="input-container z-0 margin3">
                                <input class="timeInput" type="text" autocomplete="off" name="heurealler" required>
                                <label class="label">Heure Aller</label>
                                <div class="underline"></div>
                                <div class="icone5">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="heure2">
                            <div class="input-container z-0 margin3">
                                <input class="timeInput" type="text" autocomplete="off" name="heureretour" required>
                                <label class="label">Heure Retour</label>
                                <div class="underline"></div>
                                <div class="icone5">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                            </div>
                        </div>


                        <div class="passager">
                            <div class="input-container z-0">
                                <input type="number" autocomplete="off" name="nb_passagers" min="1" required>
                                <label class="label">Nombre de passagers</label>
                                <div class="underline"></div>
                                <div class="icone4">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            </div>
                        </div>

                        <div class="prix">
                            <div class="input-container z-0">
                                <input type="number" autocomplete="off" name="prix" required>
                                <label class="label">Prix</label>
                                <div class="underline"></div>
                                <div class="icone6">
                                    <i class="fa-solid fa-euro-sign"></i>
                                </div>
                            </div>
                        </div>

                        <div class="commentaire">
                            <textarea id="commentaire" name="commentaire" placeholder="Écrivez votre commentaire ici..."></textarea>
                            <div class="underline"></div>
                        </div>

                        <button type="submit">Poster le trajet</button>

        </form>

    </div>

</body>

</html>