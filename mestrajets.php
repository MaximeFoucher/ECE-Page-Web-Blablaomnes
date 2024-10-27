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
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteconducteur'], $_POST['trajet_id'], $_POST['trajet_date'], $_POST['trajet_conducteur'], $_POST['trajet_prix'])) {
        // si le trajet est supprimé par le conducteur alors le supprime pour tout le monde
        $trajet_id = $_POST['trajet_id'];
        $trajet_date = new DateTime($_POST['trajet_date']);
        $trajet_conducteur = $_POST['trajet_conducteur'];
        $trajet_prix = $_POST['trajet_prix'];
        $today = new DateTime(); 

        if ($trajet_date > $today) {
            // si le trajet n'est pas encore passé alors rend l'argent à tout le monde
            $passengers = $bdd->prepare('SELECT Username, Places_reservees FROM appartient WHERE Idtrajet = :trajet_id AND Username != :conducteur');
            $passengers->bindParam(':trajet_id', $trajet_id);
            $passengers->bindParam(':conducteur', $trajet_conducteur);
            $passengers->execute();
            //selectionne les passagers du trajet et leur nombre de places réservées

            while ($passenger = $passengers->fetch(PDO::FETCH_ASSOC)) {
                $passager_id = $passenger['Username'];
                $nbplaces = $passenger['Places_reservees'];
                //pour chaque passager rend l'argent et retire l'argent du conducteur

                $prixtotal=$trajet_prix * $nbplaces;
                $renduconducteur = $bdd->prepare("UPDATE utilisateur SET Argent = Argent - :prix WHERE Username = :id");
                $renduconducteur->bindParam(':prix', $prixtotal, PDO::PARAM_INT);
                $renduconducteur->bindParam(':id', $trajet_conducteur);
                $renduconducteur->execute();

                $rendupassager = $bdd->prepare("UPDATE utilisateur SET Argent = Argent + :prix WHERE Username = :id");
                $rendupassager->bindParam(':prix', $prixtotal, PDO::PARAM_INT);
                $rendupassager->bindParam(':id', $passager_id);
                $rendupassager->execute();
            }
        }

        $delete = $bdd->prepare('DELETE FROM appartient WHERE Idtrajet = :trajet_id');
        $delete->bindParam(':trajet_id', $trajet_id);
        $delete->execute();
        //supprime les passagers du trajet
        
        $delete1 = $bdd->prepare('DELETE FROM trajet WHERE Idtrajet = :trajet_id');
        $delete1->bindParam(':trajet_id', $trajet_id);
        $delete1->execute();
        //supprime le trajet

        header('Location: mestrajets.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletepassager'], $_POST['trajet_id'], $_POST['trajet_date'], $_POST['trajet_conducteur'], $_POST['trajet_prix'])) {
        // si le passager supprime le trajet alors supprime le passager du trajet
        $trajet_id = $_POST['trajet_id'];
        $trajet_date = new DateTime($_POST['trajet_date']);
        $trajet_conducteur = $_POST['trajet_conducteur'];
        $trajet_prix = $_POST['trajet_prix'];
        $passager_id = $_SESSION['username'];
        $today = new DateTime();

        if ($trajet_date > $today) {
            // si le trajet n'est pas encore passé alors rend l'argent au passager et retire l'argent du conducteur
            $passengers = $bdd->prepare('SELECT Places_reservees FROM appartient WHERE Idtrajet = :trajet_id AND Username = :conducteur');
            $passengers->bindParam(':trajet_id', $trajet_id);
            $passengers->bindParam(':conducteur', $passager_id);
            $passengers->execute();
            $nbplaces = $passengers->fetch(PDO::FETCH_ASSOC)['Places_reservees'];
            $prixtotal=$trajet_prix * $nbplaces;


            $renduconducteur = $bdd->prepare("UPDATE utilisateur SET Argent = Argent - :prix WHERE Username = :id");
            $renduconducteur->bindParam(':prix', $prixtotal, PDO::PARAM_INT);
            $renduconducteur->bindParam(':id', $trajet_conducteur);
            $renduconducteur->execute();

            $rendupassager = $bdd->prepare("UPDATE utilisateur SET Argent = Argent + :prix WHERE Username = :id");
            $rendupassager->bindParam(':prix', $prixtotal, PDO::PARAM_INT);
            $rendupassager->bindParam(':id', $passager_id);
            $rendupassager->execute();

            $renduplace = $bdd->prepare("UPDATE trajet SET Placesrestantes = Placesrestantes + :nbplaces WHERE Idtrajet = :Idtrajet");
            $renduplace->bindParam(':nbplaces', $nbplaces);
            $renduplace->bindParam(':Idtrajet', $trajet_id);
            $renduplace->execute();
        }

        $delete = $bdd->prepare('DELETE FROM appartient WHERE Idtrajet = ? AND Username = ?');
        $delete->execute([$trajet_id, $passager_id]);
        //supprime le passager du trajet mais pas le trajet

        header('Location: mestrajets.php');
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
    <link rel="stylesheet" href="stylemestrajets.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="jsmestrajets.js" defer></script>
</head>
<body class="h-screen">
    <div class="header duration-500 flex justify-between bg-gradient-to-r from-slate-500 to-slate-500/75 text-center sticky top-0 text-4xl h-10 sm:h-1/5 sm:py-2 md:h-20 md:text-1xl lg:text-2xl lg:h-20 xl:text-2xl xl:h-20 z-20">
        <div id="icone"><i class="fas fa-bars"></i></div>
        <div id="logo1"><img src="images/file.png" alt="Logo" class="h-10"></div>
        <div id="menu1">
            <div id="close"><i class="fa-solid fa-xmark"></i></div>
            <div id="logo"><img src="images/file.png" alt="Logo" class="h-10"></div>
            <ul>
                <li class="h-10 text-center hover:bg-stone-200"><a href="index.php" class="waitkey">Rechercher un trajet</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="typetrajet.php" class="waitkey">Publier un trajet</a></li>
            </ul>
        </div>
        <div id="profil"><i class="fa-solid fa-user"></i></div>
        <div id="menu2">
            <div id="close2"><i class="fa-solid fa-xmark"></i></div>
            <div id="logo"><img src="images/file.png" alt="Logo" class="h-10"></div>
            <ul>
                <li class="h-10 text-center hover:bg-stone-200"><a href="profil.php" class="waitkey">Mon profil</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="mestrajets.php" class="waitkey">Mes trajets</a></li><br><br>
                <li class="h-10 text-center hover:bg-stone-200"><a href="chat.php" class="waitkey">Messagerie</a></li><br><br>
            </ul>
            <form method="POST" action="index.php">
                <button type="submit" class="deco" name="deconnexion"> Deconnexion </button>
                <input type="hidden" name="deconnexion" value="deconnexion">
            </form>
        </div>
    </div>

    <div id="main-content">
    <div class="titre2">Trajets réservés:</div>
        <?php
        $reserve = $bdd->prepare('SELECT trajet.Date, trajet.Commentaire, trajet.Idtrajet, trajet.Depart, trajet.Conducteur, trajet.Placesrestantes, trajet.Arrivee, trajet.prix 
            FROM utilisateur 
            JOIN appartient ON utilisateur.Username = appartient.Username
            JOIN trajet ON appartient.Idtrajet = trajet.Idtrajet
            WHERE appartient.Username = :username 
            and trajet.Conducteur != :username
            ORDER BY trajet.Date ASC');
        $reserve->bindParam(':username', $_SESSION['username']);
        $reserve->execute();
        //selectionne les infos des trajets réservés par l'utilisateur
        $i = 0;//compteur pour savoir si l'utilisateur a des trajets réservés
        while ($donnees = $reserve->fetch()) {
            $reponse = $bdd->prepare('SELECT utilisateur.Nom, utilisateur.Prenom, utilisateur.Vehicule, utilisateur.Tel, utilisateur.Model, utilisateur.Photo 
            FROM utilisateur 
            WHERE utilisateur.Username = :usernameconducteur');
            $reponse->bindParam(':usernameconducteur', $donnees['Conducteur']);
            $reponse->execute();
            $conducteurInfo = $reponse->fetch(PDO::FETCH_ASSOC);
            //selectionne les infos du conducteur du trajet
            $modalId = 'trajetModal_' . $donnees['Idtrajet'];
            //crée un id pour chaque modal

            $places = $bdd->prepare('SELECT Places_reservees 
            FROM appartient 
            WHERE appartient.Username = :usernameconducteur AND appartient.Idtrajet = :idtrajet');
            $places->bindParam(':usernameconducteur', $_SESSION['username']);
            $places->bindParam(':idtrajet', $donnees['Idtrajet']);
            $places->execute();
            $placesreserves = $places->fetch(PDO::FETCH_ASSOC);
            //selectionne le nombre de places réservées par l'utilisateur
        ?>
            <div class="trajet" onclick="openModal('<?php echo $modalId; ?>')">
                <div class="profil">
                    <?php
                    $stmt = $bdd->prepare('SELECT Photo FROM utilisateur WHERE Username = :username');
                    $stmt->bindParam(':username', $donnees['Conducteur']);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (isset($row['Photo']) && !empty($row['Photo'])) {
                        $photoData = $row['Photo'];
                        $photoFileName = 'photo_' . $donnees['Conducteur'] . '.jpg';
                        file_put_contents($photoFileName, $photoData);
                    ?>
                        <img src="<?php echo $photoFileName; ?>" alt="Photo de profil">
                    <?php } ?>
                </div>
                <div class="noms">
                    <div class="nom">Qui : <?php echo $conducteurInfo['Nom'] ?></div>
                    <div class="prenom"><?php echo $conducteurInfo['Prenom'] ?></div>
                    <div class="prenom">Quand : <br><?php echo $donnees['Date'] ?></div>
                </div>
                <div class="prix"><?php echo $donnees['prix'] ?>€</div>
            </div>
            <div id="<?php echo $modalId; ?>" class="hidden">
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
                                <input type="text" value="<?php echo $conducteurInfo['Prenom'] ?>" class="profile-input" readonly>
                                <input type="text" value="<?php echo $conducteurInfo['Nom'] ?>" class="profile-input" readonly>
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Départ :</label>
                            <input type="text" value="<?php echo $donnees['Depart'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Nombre de places prises :</label>
                            <input type="text" value="<?php echo $placesreserves['Places_reservees'] ?>" class="input-field" readonly>
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
                            <label class="input-label">Telephone :</label>
                            <input type="text" value="<?php echo $conducteurInfo['Tel'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Marque de voiture :</label>
                            <input type="text" value="<?php echo $conducteurInfo['Vehicule'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Modèle de voiture :</label>
                            <input type="text" value="<?php echo $conducteurInfo['Model'] ?>" class="input-field" readonly>
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
                            <input type="hidden" name="trajet_id" value="<?php echo $donnees['Idtrajet']; ?>">
                            <input type="hidden" name="trajet_date" value="<?php echo $donnees['Date']; ?>">
                            <input type="hidden" name="trajet_conducteur" value="<?php echo $donnees['Conducteur']; ?>">
                            <input type="hidden" name="trajet_prix" value="<?php echo $donnees['prix']; ?>">
                            <button type="submit" name="deletepassager" class="reserve-button">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php
            $i++;
        }
        if ($i == 0) {
        ?>
            <div class="titre">Aucun trajet reservés encore</div>
        <?php } ?>

        <div class="titre">Trajets publiés:</div>
        <?php
        $publies = $bdd->prepare('SELECT trajet.Date, trajet.Commentaire, trajet.Idtrajet, trajet.Depart, trajet.Conducteur, trajet.Placesrestantes, trajet.Arrivee, trajet.prix 
        FROM trajet 
        WHERE trajet.Conducteur = :username
        ORDER BY trajet.Date ASC');
        $publies->bindParam(':username', $_SESSION['username']);
        $publies->execute();
        $i = 0;
        while ($donnees1 = $publies->fetch()) {
            $reponse1 = $bdd->prepare('SELECT utilisateur.Nom, utilisateur.Prenom, utilisateur.Vehicule, utilisateur.Model, utilisateur.Photo, utilisateur.Argent 
            FROM utilisateur 
            WHERE utilisateur.Username = :usernameconducteur');
            $reponse1->bindParam(':usernameconducteur', $_SESSION['username']);
            $reponse1->execute();
            $Info = $reponse1->fetch(PDO::FETCH_ASSOC);
            $modalId = 'trajetModal_' . $donnees1['Idtrajet'];
        ?>
            <div class="trajet" onclick="openModal('<?php echo $modalId; ?>')">
                <div class="profil">
                    <?php
                    $stmt = $bdd->prepare('SELECT Photo FROM utilisateur WHERE Username = :username');
                    $stmt->bindParam(':username', $_SESSION['username']);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (isset($row['Photo']) && !empty($row['Photo'])) {
                        $photoData = $row['Photo'];
                        $photoFileName = 'photo_' . $_SESSION['username'] . '.jpg';
                        file_put_contents($photoFileName, $photoData);
                    ?>
                        <img src="<?php echo $photoFileName; ?>" alt="Photo de profil">
                    <?php } ?>
                </div>
                <div class="noms">
                    <div class="nom">Qui : <?php echo $Info['Nom'] ?></div>
                    <div class="prenom"><?php echo $Info['Prenom'] ?></div>
                    <div class="prenom">Quand : <br><?php echo $donnees1['Date'] ?></div>
                </div>
                <div class="prix"><?php echo $donnees1['prix'] ?>€</div>
            </div>
            <div id="<?php echo $modalId; ?>" class="hidden">
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
                                <input type="text" value="<?php echo $Info['Prenom'] ?>" class="profile-input" readonly>
                                <input type="text" value="<?php echo $Info['Nom'] ?>" class="profile-input" readonly>
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Départ :</label>
                            <input type="text" value="<?php echo $donnees1['Depart'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Destination :</label>
                            <input type="text" value="<?php echo $donnees1['Arrivee'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Date :</label>
                            <input type="text" value="<?php echo $donnees1['Date'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Prix :</label>
                            <input type="text" value="<?php echo $donnees1['prix'] ?>€" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Places restantes :</label>
                            <input type="text" value="<?php echo $donnees1['Placesrestantes'] ?>" class="input-field" readonly>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Commentaire :</label>
                            <input type="text" value="<?php echo $donnees1['Commentaire'] ?>" class="input-field" readonly>
                        </div>

                        <?php
                        $participants = $bdd->prepare('SELECT u.Username, u.Prenom, u.Nom, u.Tel
                               FROM utilisateur u
                               JOIN appartient a ON a.Username = u.Username
                               WHERE a.Idtrajet = :Idtrajet 
                               AND u.Username != :username');
                        $participants->bindParam(':Idtrajet', $donnees1['Idtrajet'], PDO::PARAM_INT);
                        $participants->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
                        $participants->execute();
                        $i = 1;
                        while ($donnees2 = $participants->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <div class="input-group">
                                <label class="input-label">Nom, prénom et contact:</label>
                                <input type="text" value="<?php echo htmlspecialchars($donnees2['Nom'] . ", " . $donnees2['Prenom'] . ", " . $donnees2['Tel']); ?>" class="input-field" readonly>
                                <input type="hidden" name="passager_<?php echo $i; ?>_id" value="<?php echo htmlspecialchars($donnees2['Username']); ?>">
                            </div>
                        <?php
                            $i++;
                        }
                        ?>

                        <form method="POST">
                            <input type="hidden" name="trajet_id" value="<?php echo $donnees1['Idtrajet']; ?>">
                            <input type="hidden" name="trajet_date" value="<?php echo $donnees1['Date']; ?>">
                            <input type="hidden" name="trajet_conducteur" value="<?php echo $_SESSION['username']; ?>">
                            <input type="hidden" name="trajet_prix" value="<?php echo $donnees1['prix']; ?>">
                            <button type="submit" name="deleteconducteur" class="reserve-button">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php
            $i++;
        }
        if ($i == 0) {
        ?>
            <div class="titre">Aucun trajet publié encore</div>
        <?php } ?>
    </div>

    <div id="footer">
        <button onclick="window.location.href='typetrajet.php'" class="footer-button">Poster</button>
        <div id="balance">
            <?php
            $stmt2 = $bdd->prepare('SELECT Argent FROM utilisateur WHERE Username = :username');
            $stmt2->bindParam(':username', $_SESSION['username']);
            $stmt2->execute();
            $argent = $stmt2->fetch(PDO::FETCH_ASSOC);
            echo $argent['Argent'] ?>€
        </div>
        <button onclick="window.location.href='index.php'" class="footer-button">Chercher</button>
    </div>
</body>
</html>
