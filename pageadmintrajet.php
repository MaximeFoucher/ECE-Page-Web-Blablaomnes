<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'], $_POST['trajet_id'], $_POST['trajet_date'], $_POST['trajet_conducteur'], $_POST['trajet_prix'])) {

        $trajet_id = $_POST['trajet_id'];
        $trajet_date = $_POST['trajet_date'];
        $trajet_conducteur = $_POST['trajet_conducteur'];
        $trajet_prix = $_POST['trajet_prix'];
        $today = new DateTime(); // Obtenir la date du jour


        if ($trajet_date > $today) { 
            //si le trajet n'est pas encore passé alors rend l'argent à tout le monde
            for ($i = 1; isset($_POST['passager_' . $i . '_id']); $i++) {
                $passager_id = $_POST['passager_' . $i . '_id']; //recupere l'id de chaque passager associé au voyage
                $renduconducteur = $bdd->prepare("UPDATE utilisateur SET Argent= Argent - :prix WHERE Username = :id"); //pour le conducteur
                $renduconducteur->bindParam('prix', $trajet_prix);
                $renduconducteur->bindParam('id', $trajet_conducteur);
                $renduconducteur->execute();

                $rendupassager = $bdd->prepare("UPDATE utilisateur SET Argent= Argent + :prix WHERE Username = :id"); //pour le passager
                $rendupassager->bindParam('prix', $trajet_prix);
                $rendupassager->bindParam('id', $passager_id);
                $rendupassager->execute();

            }
        }

        

        $delete = $bdd->prepare('DELETE FROM appartient WHERE Idtrajet = ?');
        $delete->execute([$trajet_id]);
        //supprime dans la table appartient
        $delete = $bdd->prepare('DELETE FROM trajet WHERE Idtrajet = ?');
        $delete->execute([$trajet_id]);
        //supprime dans la table trajet

        header('Location: pageadmintrajet.php');
        exit();
    }

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>page admin</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styleadmin.css">

    </head>

    <body>

        <div class="header duration-500 flex justify-between bg-gradient-to-r from-slate-500 to-slate-500/75 text-center sticky top-0 text-4xl h-10 sm:h-1/5 sm:py-2 md:h-20 md:text-1xl lg:text-2xl lg:h-20 xl:text-2xl xl:h-20 z-20">


            <div id="menu">
                <div id="close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <ul>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="pageadminutilisateur.php" class="waitkey">Utilisateur</a>
                    </li>
                </ul>
            </div>

            <div id="menu">
                <div id="close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <ul>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="pageadmintrajet.php" class="waitkey">Trajet</a>
                    </li>
                </ul>
            </div>

            <div id="menu">
                <div id="close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <ul>
                    <li class="h-10 text-center hover:bg-stone-200"><a href="pageadmincampus.php" class="waitkey">Campus</a>
                    </li>
                </ul>
            </div>


        </div>


        <?php


        $reponse = $bdd->query('SELECT * 
        FROM utilisateur 
        JOIN appartient ON utilisateur.Username = appartient.Username
        JOIN trajet ON appartient.Idtrajet = trajet.Idtrajet
        where utilisateur.Username = trajet. conducteur');
        while ($donnees = $reponse->fetch()) {
            //recupere uniquement les differents trajets pour les afficher
        ?>

            <form method="POST" action="pageadmintrajet.php">
                <div class="w-full min-h-screen bg-white p-4">
                    <p class="ml-60 mb-20 w-20em">
                    <h1>Trajet <?php echo $donnees['Idtrajet'] ?></h1><br>
                    Conducteur: Nom : <?php echo $donnees['Nom'] . ", Prenom : " . $donnees['Prenom'] . ", Username : " . $donnees['Username']; ?>,<br>
                    Prix : <?php echo $donnees['prix']; ?>,<br>
                    Nombre de place(s) restante(s) : <?php echo $donnees['Placesrestantes']; ?>,<br>
                    Date : <?php echo $donnees['Date'] ?><br>

                    <?php
                    $participants = $bdd->prepare('SELECT trajet.Idtrajet, trajet.Depart, trajet.Conducteur, trajet.Placesrestantes, utilisateur.Nom, utilisateur.Prenom, utilisateur.Tel 
                    FROM utilisateur 
                    JOIN appartient ON utilisateur.Username = appartient.Username
                    JOIN trajet ON appartient.Idtrajet = trajet.Idtrajet
                    WHERE  trajet.Idtrajet = :Idtrajet and utilisateur.Username  != :username');
                    $participants->bindParam(':Idtrajet', $donnees['Idtrajet']);
                    $participants->bindParam(':username', $donnees['Conducteur']);
                    $participants->execute();
                    $i = 1; //compteur pour le numero du passager
                    while ($donnees2 = $participants->fetch()) {
                        //recupere les passagers de chaque trajets
                    ?>
                        Passager <?php echo $i ?> : Nom : <?php echo $donnees['Nom'] . ", Prenom : " . $donnees['Prenom'] . ", Username : " . $donnees['Username']; ?>,<br>
                        <input type="hidden" name="passager_<?php echo $i; ?>_id" value="<?php echo $donnees2['Id']; ?>">
                    <?php
                        $i++;
                    }
                    ?>
                    <input type="hidden" name="trajet_id" value="<?php echo $donnees['Idtrajet'];
                                                                    //recupere l'id sans que l'utilisateur le voit
                                                                    ?>">
                    <input type="hidden" name="trajet_date" value="<?php echo $donnees['Date'];
                                                                    //recupere la date sans que l'utilisateur le voit
                                                                    ?>">
                    <input type="hidden" name="trajet_conducteur" value="<?php echo $donnees['Conducteur'];
                                                                            //recupere le conducteur sans que l'utilisateur le voit
                                                                            ?>">
                    <input type="hidden" name="trajet_conducteur" value="<?php echo $donnees['prix'];
                                                                            //recupere le prix sans que l'utilisateur le voit
                                                                            ?>">
                    <button type="submit" name="delete" class="text-xs">Supprimer</button>
            </form>
        <?php
        }
        ?>
        </div>
    <?php

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
    ?>

    </body>

    </html>