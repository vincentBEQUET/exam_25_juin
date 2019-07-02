<?php

/**
 * Permet de créer une miniature au format 300x300 d'une image source.
 * Les extensions acceptées sont : jpg, jpeg, png et gif.
 * 
 * @param string $titreAncienneImage Titre avec extension de l'image de départ
 * @param string $extension Extension de l'image de départ
 * @param string $dossierEnregistrement Dossier de stockage des images (sans "/")
 * @param string $titreNouvelleImage Titre avec extension de l'image d'arrivée
 * 
 * @return boolean True si l'image a été créée, False s'il y a un problème d'extension.
 */
function createMiniature($titreAncienneImage, $extension, $dossierEnregistrement, $titreNouvelleImage)
{

    $cheminSource = $dossierEnregistrement . '/' . $titreAncienneImage;
    $cheminDestination = $dossierEnregistrement . '/' . $titreNouvelleImage;

    switch ($extension) {

        case 'jpg':
            $source = imagecreatefromjpeg($cheminSource);
            break;

        case 'jpeg':
            $source = imagecreatefromjpeg($cheminSource);
            break;

        case 'png':
            $source = imagecreatefrompng($cheminSource);
            break;

        case 'gif':
            $source = imagecreatefromgif($cheminSource);
            break;

        default:
            return false;
            break;
    }


    $destination = imagecreatetruecolor(300, 300); // On crée la miniature vide

    // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
    $largeur_source = imagesx($source);
    $hauteur_source = imagesy($source);
    $largeur_destination = imagesx($destination);
    $hauteur_destination = imagesy($destination);

    // On crée la miniature
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);

    // On enregistre la miniature sous le nom "mini_couchersoleil.jpg"

    switch ($extension) {

        case 'jpg':
            imagejpeg($destination, $cheminDestination);
            return true;
            break;

        case 'jpeg':
            imagejpeg($destination, $cheminDestination);
            return true;
            break;

        case 'png':
            imagepng($destination, $cheminDestination);
            return true;
            break;

        case 'gif':
            imagegif($destination, $cheminDestination);
            return true;
            break;

        default:
            return false;
            break;
    }
}
