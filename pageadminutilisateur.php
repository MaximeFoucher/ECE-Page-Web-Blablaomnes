<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;
charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

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


        $reponse = $bdd->query('SELECT * FROM utilisateur');
        // On affiche chaque entree une a une

        while ($donnees = $reponse->fetch()) { //peut etre ajouter de bloquer à 20 données affichées et ajouter un bouton pour afficher 20 de plus
        ?>
            <div>
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
                    <img src="<?php echo $photoFileName; ?>" alt="photo" class="float-left w-20 h-auto">
                <?php
                }
                ?>
            </div>


            <div class="flex my-10 ml-5">
                <form action="pageadminmodifier_utilisateur.php" method="POST" class="">
                    <p class="ml-60 mb-20">
                        Username: <?php echo $donnees['Username']; ?>,<br>
                        Nom : <?php echo $donnees['Nom']; ?>,<br>
                        Prenom : <?php echo $donnees['Prenom']; ?>,<br>
                        Email : <?php echo $donnees['Email']; ?>,<br>
                        <?php
                        if ($donnees['Admin'] == 1) {
                        ?>
                            Admin : Oui.
                        <?php
                        } else {
                        ?>
                            Date de naissance : <?php echo $donnees['Date']; ?>,<br>
                            Tel : <?php echo $donnees['Tel']; ?>,<br>
                            Argent : <?php echo $donnees['Argent']; ?>,<br>



                            Conducteur : <?php if ($donnees['Etat_conducteur'] == 1) {
                                                echo 'Oui'; ?><br>
                                Vehicule : <?php echo $donnees['Vehicule']; ?>,<br>
                                Model : <?php echo $donnees['Model']; ?>,<br>
                                Immatriculation : <?php echo $donnees['Immatriculation']; ?>.<br>
                            <?php
                                            } else {
                                                echo 'Non.';
                                            } ?><br><?php
                                                } ?>
                    </p>
                    <input type="hidden" name="Username" value="<?php echo $donnees['Username']; ?>">
                    <?php
                    if ($donnees['Etat_conducteur'] == 0 && isset($donnees['Permis'])) {
                    ?>
                        <p class="px-4 py-2 bg-red-500 text-white rounded">
                            Demande de permis !
                        </p>
                    <?php
                    }
                    ?>
                    <?php
                    if ($donnees['Admin'] == 0) {
                    ?>
                        <button type="submit" class="text-xs">Modifier</button>
                    <?php
                    } else {
                    ?>
                        <button type="" class="block mx-auto my-12 bg-gray-400 text-gray-800 border-none py-4 px-20 text-4xl font-custom rounded-full shadow-lg" disabled>Modifier</button>
                    <?php
                    } ?>
                </form>
            </div>
    <?php
        }
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    ?>
    <div class="flex justify-center my-10">
        <form action="pageadminajouter_utilisateur.php" method="POST">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ajouter Utilisateur</button>
        </form>
    </div>

    </body>

    </html>