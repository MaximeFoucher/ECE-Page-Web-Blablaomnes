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


        $reponse = $bdd->query('SELECT * FROM campus');
        // On affiche chaque entree une a une
        while ($donnees = $reponse->fetch()) {
        ?>
            <div class="flex my-10 ml-5">
                <form action="pageadminmodifier_campus.php" method="POST" class="">
                    <p class="ml-20">
                        <span class="font-bold">Id Campus:</span> <?php echo $donnees['IdCampus']; ?>,<br>
                        <span class="font-bold">Adresse:</span> <?php echo $donnees['Adresse']; ?>,<br>
                        <span class="font-bold">Nom:</span> <?php echo $donnees['Nom']; ?>
                    </p>
                    <input type="hidden" name="IdCampus" value="<?php echo $donnees['IdCampus']; ?>">
                    <button type="submit" class="text-xs">Modifier</button>
                </form>
            </div>
    <?php
        }
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    ?>
    <div class="flex justify-center my-10">
        <form action="pageadminajouter_campus.php" method="POST">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ajouter un Campus</button>
        </form>
    </div>

    </body>

    </html>