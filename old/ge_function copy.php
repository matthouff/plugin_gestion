<?php

/**
*    @package gestionnairePlugin
*/

/*
Plugin Name: Mon plugin
Plugin URI: https://site-du-plugin.fr
Description: Ce plugin WordPress sert à …
Author: Sorgniard
Version: 1.0
Author URI: https://mon-site.fr
*/


// if(! defined('ABSPATH')){
//     die;
// }



class GestionPlugin{

    function __construct(){
        add_action( 'init', array($this, 'custom_post_type') );
    }

    function activate(){
        // génère le CPT
        $this->custom_post_type();
        // flush rewrite rules 
        flush_rewrite_rules();
    }

    function deactivate(){
        //flush rewrite
        flush_rewrite_rules();
    }

    function custom_post_type(){
        register_post_type( 'eleve', [
            'label' => 'Elèves',
            'hierarchical' => true,
            'public' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
        ] );
        register_taxonomy('professeur','eleve', array(
            'hierarchical' => true,
            'labels' => array('name' => 'Professeurs'),
            'show_admin_column' => true,
            'rewrite' => array( 'slug' => 'Professeurs' ),
        ));
        register_taxonomy('cours','eleve', array(
            'hierarchical' => true,
            'labels' => array('name' => 'Cours'),
            'show_admin_column' => true,
            'rewrite' => array( 'slug' => 'Cours' ),
        ));


        register_post_type( 'prof', [
            'label' => 'Professeurs',
            'hierarchical' => true,
            'public' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
        ] );
    }
}

if(class_exists('GestionPlugin')){
    $gestionPlugin = new GestionPlugin();
}


// Activation
register_activation_hook( __FILE__, array($gestionPlugin, 'activate') );

// désactivation
register_deactivation_hook( __FILE__, array($gestionPlugin, 'deactivate') );













add_action( 'admin_init', "prof_init_meta" );
add_action( 'save_post', "prof_save_meta" );

function prof_init_meta(){
    if(function_exists('add_meta_box')){
        add_meta_box( "professeur", "Informations professeur", "gestion_render_metabox", 'prof' );
    }
}

// FONCTION PERMETANT DE CREER LES CHAMPS
function gestion_render_metabox(){

    global $post;

    $categories = ['nom', 'prenom', 'mail', 'mobile', 'ville', 'code_postal', 'instrument', 'date'];

    for($i = 0; $i < count($categories); ++$i){
        $value = get_post_meta( $post->ID, $categories[$i], true );

        if($categories[$i] == 'mail'){
            ?>
        <!-- INFORMATIONS DU PROFESSEUR -->
        <div class="meta-box-item-title" style="margin-bottom: .5rem;">
            <?= $categories[$i] ?>
        </div>
        <div class="meta-box-item-content">
            <input type="mail" name="<?php $categories[$i] ?>" id="<?php $categories[$i] ?>" value="<?= $value ?>"
                style="width: 100%; margin-bottom: 1rem;" require>
        </div>
        <?php
        } else{
            ?>
        <!-- INFORMATIONS DU PROFESSEUR -->
        <div class="meta-box-item-title" style="margin-bottom: .5rem;">
            <?= $categories[$i] ?>
        </div>
        <div class="meta-box-item-content">
            <input type="text" name="<?php $categories[$i] ?>" id="<?php $categories[$i] ?>" value="<?= $value ?>"
                style="width: 100%; margin-bottom: 1rem;" require>
        </div>
        <?php
        }
    }
}







// FONCTION PERMETANT DE SAUVEGARDER LES DONNEES DANS LES CHAMPS

function prof_save_meta($post_id){
    
    $meta = "nom";

    if(!isset($_POST[$meta]) ||
     (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
     (defined('DOING_AJAX') && DOING_AJAX)){
        return false;
    }

    $value = $_POST[$meta];

    if(get_post_meta( $post_id, $meta, true )){
        update_post_meta( $post_id, $meta, $value );
    } else if($value === ''){
        delete_post_meta( $post_id, $meta );
    } else{
        add_post_meta( $post_id, $meta, $value );
    }
}






// DESACTIVER L'EDITEUR DE TEXTE

add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);

 function prefix_disable_gutenberg($gutenberg_filter, $post_type)
  {
   if ($post_type === 'prof') return false;
   return $gutenberg_filter;
  }
  add_action('init', 'init_remove_editor',100);

  function init_remove_editor(){
   $post_type = 'prof';
   remove_post_type_support( $post_type, 'editor');
  }





// Permet d'afficher les valeurs des champs dans les colonnes du custom post

function custom_columns($columns)
{
    unset($columns['date']);
    return array_merge(
        $columns,
        array(
            'nom' => __('Nom'),
            'prenom' => __('Prénom'),
            'mail' => __('E-mail'),
            'mobile' => __('Mobile'),
            'ville' => __('ville'),
            'postal' => __('Code postal'),
            'instrument' => __('Type d\'instrument'),
            'time' => __('Publié')
        )
    );
}
add_filter('manage_prof_posts_columns', 'custom_columns');


function display_custom_columns($column, $post_id)
{
    switch ($column) {
        case 'nom':
            echo get_post_meta($post_id, $_POST['nom'], true);
            break;
        case 'prenom':
            echo get_post_meta($post_id, $_POST['prenom'], true);
            break;
        case 'mail':
            echo get_post_meta($post_id, $_POST['mail'], true);
            break;
        case 'mobile':
            echo get_post_meta($post_id, $_POST['mobile'], true);
            break;
        case 'ville':
            echo get_post_meta($post_id, $_POST['ville'], true);
            break;
        case 'postal':
            echo get_post_meta($post_id, $_POST['postal'], true);
            break;
        case 'instrument':
            echo get_post_meta($post_id, $_POST['instrument'], true);
            break;
        case 'time':
            echo get_post_meta($post_id, the_time('Y-m-d H:i:s'), true);
            break;
    }
}
add_action('manage_prof_posts_custom_column', 'display_custom_columns', 10, 2);






// $serveur = "localhost";
// $dbname = "gestion_eleve";
// $user = "root";
// $pass = "";

// try
// {
//     $dateActuelle=new Datetime();
//     $dbco = new PDO("mysql:host=$serveur;dbname=$dbname",$user,$pass);
//     $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $data = [
//         'nom' => $_POST['nom'],
//         'prenom' => $_POST['prenom'],
//         'mail' => $_POST['mail'],
//         'mobile' => $_POST['mobile'],
//         'ville' => $_POST['ville'],
//         'postal' => $_POST['postal'],
//         'instrument' => $_POST['instrument'],
//         'time' => $dateActuelle->format('Y-m-d H:i:s')
//     ];

//     $sql=" INSERT INTO professeur ( nom, prenom, mail, mobile, ville, postal, instrument, time)
//         VALUES ( :nom, :prenom, :mail, :mobile, :ville, :postal, :instrument, time)";
//     $stmt= $dbco->prepare($sql);
//     $stmt->execute($data);
    


// }
// catch (Exception $e)
// {
//     die('Erreur : ' . $e->getMessage());
// }