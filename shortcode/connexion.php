<?php
function shortcode_connexion(){ 
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'table_geleve_instrument';
    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


$formulaire = '
    <form action="" id="formulaire-connexion">

        <label for="">Entrez votre Pseudo ou Email</label>
        <input type="text" placeholder="Pseudo / Email" name="pseudo">

        <label for="">Mot de passe</label>
        <input type="password" placeholder="********" name="password">

        <a href="">Première connexion ici</a>

        <button type="submit">Connexion</button>

    </form>
';

return $formulaire;

}
add_shortcode( 'connexion', 'shortcode_connexion' );