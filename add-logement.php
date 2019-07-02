<?php

require 'pdo.php';
require 'helpers.php';

if (!empty($_POST)) {

    if (!isset($_POST['titre'])) {
        throw new Exception('Le champ titre est vide.');
    }

    if (strlen($_POST['titre']) > 150) {
        throw new Exception('Le champ titre est trop long.');
    }

    if (!isset($_POST['adresse'])) {
        throw new Exception('Le champ adresse est vide.');
    }

    if (strlen($_POST['adresse']) > 255) {
        throw new Exception('Le champ adresse est trop long.');
    }

    if (!isset($_POST['ville'])) {
        throw new Exception('Le champ ville est vide.');
    }

    if (strlen($_POST['ville']) > 150) {
        throw new Exception('Le champ ville est trop long.');
    }

    if (!isset($_POST['cp'])) {
        throw new Exception('Le champ cp est vide.');
    }

    if ($_POST['cp'] < 1000) {
        throw new Exception('Le champ cp est incorrect (< 1 000).');
    }

    if ($_POST['cp'] > 100000) {
        throw new Exception('Le champ cp est incorrect (> 100 000).');
    }

    if (!isset($_POST['prix'])) {
        throw new Exception('Le champ prix est vide.');
    }

    if (!is_numeric($_POST['prix'])) {
        throw new Exception('Le champ prix est au mauvais format.');
    }

    if (strpos($_POST['prix'], ',')) {
        throw new Exception('Le champ prix contient une virgule.');
    }

    if (strpos($_POST['prix'], '.')) {
        throw new Exception('Le champ prix contient un point.');
    }
    if (!isset($_POST['surface'])) {
        throw new Exception('Le champ surface est vide.');
    }

    if (!is_numeric($_POST['surface'])) {
        throw new Exception('Le champ surface est au mauvais format.');
    }

    if (strpos($_POST['surface'], ',')) {
        throw new Exception('Le champ surface contient une virgule.');
    }

    if (strpos($_POST['surface'], '.')) {
        throw new Exception('Le champ surface contient un point.');
    }

    if (!isset($_FILES['photo'])) {
        throw new Exception('Le champ photo est vide.');
    }


    if (!isset($_POST['type'])) {
        throw new Exception('Le champ type est vide');
    }

    $typesAutorises = ['location', 'vente'];

    if (!in_array($_POST['type'], $typesAutorises)) {
        throw new Exception('Le champ type est incorrect');
    }

    $request = 'INSERT INTO logement(titre, adresse, ville, cp, surface, prix, photo, type, description)
                VALUES (:titre, :adresse, :ville, :cp, :surface, :prix, :photo, :type, :description)';

    $response = $bdd->prepare($request);

    $response->execute([
        'titre'             => $_POST['titre'],
        'adresse'           => $_POST['adresse'],
        'ville'             => $_POST['ville'],
        'cp'                => $_POST['cp'],
        'surface'           => $_POST['surface'],
        'prix'              => $_POST['prix'],
        'photo'             => $_FILES['photo']['name'],
        'type'              => $_POST['type'],
        'description'       => $_POST['description']
        
    ]);

    $id = $bdd->lastInsertId();

    $newName = 'logement_' . $id;

    if ($_FILES['photo']['error'] == 0) {

        // Testons si le fichier n'est pas trop gros
        if ($_FILES['photo']['size'] <= 32000000) {
            // Testons si l'extension est autorisée
            $infosfichier = pathinfo($_FILES['photo']['name']);
            $extension_upload = $infosfichier['extension'];
            $extensions_autorisees = ['jpg', 'jpeg', 'gif', 'png'];
            if (in_array($extension_upload, $extensions_autorisees)) {
                // On peut valider le fichier et le stocker définitivement

                move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' .  $newName . '.' . $extension_upload);
                echo "L'envoi a bien été effectué !";


                $request = 'UPDATE logement
                            SET photo = "' . $newName . '.' . $extension_upload . '" 
                            WHERE id = ' . $id;

                $bdd->query($request);

                /**
                 * Gestion de la miniature : 
                 * Je traite mes variables afin de remplir les arguments de ma fonction createMinature,
                 * qui crééera par exemple l'image suivante : "logement_38_300x300.png"
                 */
                $titreAncienneImage = $newName . '.' . $extension_upload;       // Le nom de l'image de départ AVEC extension
                $extension = $extension_upload;                                 // L'extension de départ
                $dossierEnregistrement = 'uploads';                             // Le dossier de stockage des images, sans "/" !!!
                $titreNouvelleImage = $newName . '_300x300.' . $extension;     // Le nom de la nouvelle image AVEC extension
                $resultMiniature = createMiniature($titreAncienneImage, $extension, $dossierEnregistrement, $titreNouvelleImage);
                if (!$resultMiniature) {
                    echo "Il y a eu un problème lors de la création de la miniature.";
                    return;
                }
            }
        } else {
            throw new Exception('La photo est trop grande');
        }
    } else {
        throw new Exception('Une erreur lors de lupload de limage');
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <title>Formulaire</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <div class="row mt-3">
            <div class="col-12">

                <a class="btn btn-primary btn-sm" href="index.php">Retour</a>

                <form action="add-produit.php" method="post" class="form" enctype="multipart/form-data">

                    <input name="titre" placeholder="titre" type="text" class="form-control">
                    <input name="adresse" placeholder="adresse" type="text" class="form-control">
                    <input name="ville" placeholder="ville" type="text" class="form-control">
                    <input name="cp" placeholder="cp" type="text" class="form-control">
                    <input name="surface" placeholder="surface" type="text" class="form-control">
                    <input name="prix" placeholder="prix" type="text" class="form-control">
                    <input name="photo" placeholder="photo" type="file" class="form-control">


                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="location">
                        <label class="form-check-label" for="exampleRadios1">
                            Location
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" value="vente">
                        <label class="form-check-label" for="exampleRadios2">
                            Vente
                        </label>
                    </div>

                    <input name="description" placeholder="description" type="text" class="form-control">

                    <button class="btn btn-success float-right mt-3" type="submit">Envoyer</button>


                </form>

            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>