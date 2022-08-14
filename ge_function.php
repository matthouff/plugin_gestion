<?php

/*
Plugin Name: Gestion des élèves
Description: Ce plugin permet de faire une liste des élèves avec leurs informations, le professeur correspondant et le type d'abonnement qu'ils ont pris. Pour ajouter le formulaire d'inscription à une page, il suffit simplement de taper [inscription] et votre formulaire d'inscription sera mis en place.
Author: Sorgniard
Version: 1.0
*/






// Créer les menus custom post avec les sous menus 

function custom_table_example_admin_menu()
{
    add_menu_page(__('Eleves'), __('Eleves').count_new_student_gestion(), 'activate_plugins', 'gestion-eleves', 'Functions_Add_Delete_Eleve' );
    add_submenu_page('Eleves', __('Add new'), __('Add new'), 'activate_plugins', 'eleves_form', 'custom_table_example_eleves_form_page_handler');

    add_menu_page(__('Professeurs'), __('Professeurs'), 'activate_plugins', 'gestion-prof', 'custom_table_example_prof_page_handler' );
    add_submenu_page('Professeurs', __('Add new'), __('Add new'), 'activate_plugins', 'prof_form', 'custom_table_example_prof_form_page_handler');

    add_menu_page(__('Instruments'), __('Instruments'), 'activate_plugins', 'gestion-cours', 'custom_table_example_cours_page_handler' );
    add_submenu_page('Cours', __('Add new'), __('Add new'), 'activate_plugins', 'cours_form', 'custom_table_example_cours_form_page_handler');
}
add_action('admin_menu', 'custom_table_example_admin_menu');



////////////////////////////////////////////////////////////////////////////////




// Ajout d'un badge pour chaque nouvel élève qui s'inscrit et qui n'est pas encode validé
// A voir pour amélioration de non-repetition

function count_new_student_gestion(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'table_geleve_eleve';
    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$wpdb->prefix."table_geleve_eleve WHERE valid = 'en_attente' ");

    $query = new WP_Query( array( 'post_type' => 'geleves_eleve' , 'meta_key' => 'valid', 'meta_value' => 'en_attente', 'posts_per_page' => -1 ) );
    $number=  $query->post_count;

    if($total_items != 0){
        return "<span class=\"geleves_badge_gen update-plugins count-$total_items\"><span class=\"plugin-count\">$total_items</span></span>";
    }
}
add_action( 'muplugins_loaded', 'count_new_student_gestion' );



/////////////////////////////////////////////////////////////////////////////////////////






// Activation 

function activate() { 
    flush_rewrite_rules(); 
    require_once 'table_crea.php'; // Appel de table_crea.php
}
register_activation_hook( __FILE__, 'activate');



// désactivation
function deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'geleves_eleve' );
    unregister_post_type( 'geleves_prof' );
    unregister_post_type( 'geleves_cours' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();

    //Suppression de la page 'connexion'
    $page_details = get_pages( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-connexion.php',
        'hierarchical' => 0,
    ));

    foreach($page_details as $page){
        wp_delete_post( $page->ID, true);
    }

    require_once 'table_suppr.php';
}
register_deactivation_hook( __FILE__,  'deactivate');



////////////////////////////////////////////////////////////////////////////////////////////






//Ajouter les feuilles de style 

function geleves_load_stylesheets_and_scripts(){

    wp_enqueue_style( 'geleves_style', plugin_dir_url( __FILE__ ).'/css/geleves_style.css', array(), '1.0');

    wp_enqueue_script("geleves_script", plugin_dir_url( __FILE__ ).'/js/geleves_script.js', array(), '1.0', true);
    wp_localize_script('geleves_script', 'myScript', array(
        'gestion' => plugins_url(),
    ));

}
add_action('wp_enqueue_scripts', 'geleves_load_stylesheets_and_scripts');


/////////////////////////////////////////////////////////////////////////////////////////////





// MODIFICATION (script et style) DANS LE BACK OFFICE GRACE AU HOOK "admin_print_styles"

function geleves_style_backoffice(){
    wp_enqueue_style( 'geleves_style', plugin_dir_url( __FILE__ ).'/css/admin.css', array(), '1.0');
}
add_action('admin_print_styles', 'geleves_style_backoffice', 11);

function geleves_scripts_backoffice(){
    wp_enqueue_script( 'geleves_scripts', plugin_dir_url( __FILE__ ).'/js/admin.js', array(), '1.0');
}
add_action('admin_print_scripts', 'geleves_scripts_backoffice', 11);



/////////////////////////////////////////////////////////////////////////////////////////////




// Fenêtre d'aide pour se servir du plugin

function geleves_aide(){

    // Attente de solution pour afficher la bande d'aide uniquement sur le plugin

    if(true){
        ?>
            <div class="cont-bout-aide-geleves">
                    <img src="<?php echo plugin_dir_url( __FILE__ )?>/img/logo.png" alt="Logo Sorgniard">
                    <p class="geleves_nom_plugin">Gestion - Cours de musique</p>
                    <button>Aide utilisation <span>▼</span></button>
                </div>
            <div class="cont-aide-geleves">
    
                <p class="geleves_nom_plugin">Gestion - Cours de musique</p>
                
                <!-- Explication instruments -->
                
                <div class="instrument-geleves">
                    <p class="titre-geleves">Ajouter ou modifier des instruments, professeurs ou élèves</p>
    
                    <ul class="chemin">
                        <li>Pour accéder à vos tableau, cliquez sur l'onglet "<strong>Eleves / Professeurs / Instruments</strong>" se situant en bas du menu de navigation (A gauche de l'écrans)</li>
                        <li>Sur la nouvelle page où est affiché tout vos élèves, cliquez sur "<strong>Ajouter</strong>"</li>
                        <li>Modifiez ou entrez les informations demandées dans les champs</li>
                        <li>N'oubliez pas d'<strong class="alert">enregistrer</strong> pour ajouter à la liste</li>
                    </ul>
                    
                </div>
                
                <!-- Explication élèves --> <hr>
    
                <div class="eleves-geleves">
                    <p class="titre-geleves">Valider un élève inscrit par le formulaire d'inscription</p>
    
                    <p class="explication">
                        Quand un nouvel élève s'inscrit, l'élève s'ajoute au tableau automatiquement mais à un status "<strong>en attente</strong>". <br>
                        Si il y a des élèves en attente, un badge rouge "<span class="badge">1</span>" apparait à coté du menu (Eleves) affichant le nombre d'élèves qui ne sont pas encore validés et attribués à un professeur.
                    </p>
                    <p class="non-valides">
                        Pour voir les élèves "en attente", cliquez sur le selecteur " <strong>Tout les élèves</strong> " au dessus du tableau et choisissez "<strong>en_attente</strong>". <br>
                        Aprèss ça tous les élèves "en attente" seront affichés, il suffira simplement de cliquer sur l'un d'eux ce qui vous affichera toutes les informations entrés par l'élève. <br>
                        En bas des informations il y a des champs non remplis comme le professeur à attribuer manuellement et la validation de l'élève.<br><br>
                        Lorsque l'élève sera validé il recevra automatiquement un email avec son <strong>Identifiant</strong> et son <strong>Mot de passe</strong> pour pouvoir consulter les informations sur ses cours, son professeur et les autres élèves participant (si il y en a).
                    </p>
                </div>
    
                <!-- Supprimer élèves --> <hr>
    
                <div class="cont-suppr-geleves">
                    <p class="titre-geleves">Supprimer un élève/professeur/instrument</p>
    
                    <ul class="suppr-geleves">
                        <li>Dans le tableau, survolez la ligne à supprimer et cliquez sur "<strong>Supprimer</strong>" (en rouge).</li>
                    </ul> <br>
                    <p class="attention"><strong>ATTENTION:</strong> Toute suppresion est définitive !</p>
                </div>
            </div>
        <?php
    }
}
add_action( "in_admin_header", "geleves_aide" );


/////////////////////////////////////////////////////////////////////////////////////////////




// Ajouter les rôles d'admin pour les élèves / professeurs

add_role(
    'eleve',
    __( 'Eleve' ),
    array(
        'read'         => false,  // true allows this capability
        'edit_posts'   => false,
        'delete_posts' => false, // Use false to explicitly deny
    )
);
add_role(
    'professeur',
    __( 'Professeur' ),
    array(
        'read'         => false,  // true allows this capability
        'edit_posts'   => false,
        'delete_posts' => false, // Use false to explicitly deny
    )
);







if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}





// Création d'un template

// function wpse255804_add_page_template ($templates) {
//     $templates['template-connexion.php'] = 'Connexion';
//         return $templates;
//     }
// add_filter ('theme_page_templates', 'wpse255804_add_page_template');





add_filter( 'page_template', 'wpa3396_page_template' );
function wpa3396_page_template( $page_template )
{
    if ( is_page(100128) ) {
        $page_template = dirname( __FILE__ ) . '/templates/template-connexion.php';
    }
    return $page_template;
}






// Permet de créer une page

function add_my_custom_page($id_page) {
    $temp = get_page_templates();

    $id_page = wp_insert_post(array(
        'post_title'    => wp_strip_all_tags( 'connexion page' ),
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'page',
        'page_template'  => 'template-connexion.php'
        )
    );
}
register_activation_hook(__FILE__, 'add_my_custom_page');








include("shortcode/formulaire-eleve.php");

include("eleve.php");
include("professeur.php");
include("cours.php");