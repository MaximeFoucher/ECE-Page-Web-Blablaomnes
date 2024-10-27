<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=blablaomnes;charset=utf8',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
        $adresse = $_POST['Adresse'];
        $nom = $_POST['Nom'];

        $add = $bdd->prepare('INSERT INTO campus (Adresse, Nom) VALUES (?, ?)');
        $add->execute([$adresse, $nom]);

        header('Location: pageadmincampus.php');
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Ajouter Campus</h1>
        <form action="pageadminajouter_campus.php" method="POST">
            <div class="mb-4">
                <label for="Adresse" class="block text-sm font-medium text-gray-700">Adresse :</label>
                <input type="text" id="Adresse" name="Adresse" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="Nom" class="block text-sm font-medium text-gray-700">Nom :</label>
                <input type="text" id="Nom" name="Nom" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <button type="submit" name="add" class="px-4 py-2 bg-blue-500 text-white rounded">Ajouter</button>
        </form>
    </div>
</body>

</html>
