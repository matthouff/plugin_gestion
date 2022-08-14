<?php
require_once('../../../wp-load.php');

global $wpdb;
$table_name = $wpdb->prefix . 'table_geleve_instrument';
$instrument_data = $wpdb->get_var("SELECT id FROM $table_name WHERE nom = '" . $_POST['instruments'] ."'");


// Pour envoyer de façon asynchrone
if(!empty($_POST["nom"]) AND !empty($_POST["prenom"]) AND !empty($_POST["date_naissance"]) AND !empty($_POST["adresse"]) AND !empty($_POST["tel"]) AND !empty($_POST["mail"]) AND !empty($_POST["parents"])){

    if(isset($_POST["abonnement"])){

        if(isset($_POST["instruments"])){

            //faire des sécurités
            $nomEleves = htmlspecialchars(strip_tags($_POST['nom']));
            $prenomEleves = htmlspecialchars(strip_tags($_POST['prenom']));
            $naissance = htmlspecialchars(strip_tags($_POST['date_naissance']));
            $adresse = htmlspecialchars(strip_tags($_POST['adresse']));
            $telInscription = htmlspecialchars(strip_tags($_POST['tel']));
            $mailInscription = htmlspecialchars(strip_tags($_POST['mail']));
            $parents = htmlspecialchars(strip_tags($_POST['parents']));
            $instruments = $_POST['instruments'];
            $abonnement = $_POST['abonnement'];


            /////////////////////////////////// ENVOIE A LA BASE DE DONNEE ET EN TANT QUE "POST" DANS LE post_type "eleve"

            try
                {

                    global $wpdb;

                    echo '<pre>';
                    print_r($wpdb);
                    echo '</pre>';

                    $wpdb->insert($wpdb->prefix . "table_geleve_eleve", array(
                        'nom' => $nomEleves,
                        'prenom' => $prenomEleves,
                        'date_naissance' => $naissance,
                        'adresse' => $adresse,
                        'tel' => $telInscription,
                        'mail' => $mailInscription,
                        'nom_prenom_parents' => $parents,
                        'id_instrument' => $instrument_data,
                        'formule' => $abonnement,
                        'valid' => 'en_attente',
                    ));

                    die('pourtant ca passe bien la');
                }
                catch (Exception $e)
                {
                    die('Erreur : ' . $e->getMessage());
                }

            /////////////////////////////////////////
        }else{
            $msg = "Veuillez choisir un instrument";
        }
        
    }else{
        $msg = "Veuillez choisir un d'abonnement";
    }
    

}else{
    $msg = "Veuillez remplir tous les champs";
}