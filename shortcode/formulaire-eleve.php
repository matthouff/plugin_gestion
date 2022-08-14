<?php
function mon_shortcode(){ 
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'table_geleve_instrument';
    $instruName = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'table_geleve_instrument');


$formulaire = '
    <section class="geleves-cont-form-inscription">
    <form method="POST" id="form-inscription" class="inscript-form">
        <div class="cont_left">
            <div>
                <label for="nom">NOM (élève)</label>
                <input type="text" placeholder="votre nom" name="nom" value="matth">
            </div>
            <div>
                <label for="prenom">Prénom (élève)</label>
                <input type="text" placeholder="votre prénom" name="prenom" value="berth">
            </div>
            <div>
                <label for="date_naissance">Né(e) le :</label>
                <input type="date" placeholder="01/01/2022" name="date_naissance">
            </div>
            <div>
                <label for="adresse">Adresse</label>
                <input type="text" name="adresse" value="rue du cul de sac">
            </div>
            <div>
                <label for="tel">Tel</label>
                <input type="text" placeholder="-- -- -- -- --" name="tel" value="06 00 00 00 00">
            </div>
            <div>
                <label for="mail">Mail</label>
                <input type="email" placeholder="@" name="mail" value="matth@hotmail.com">
            </div>
            <div>
                <label for="parents">NOM/Prénom(Parents)</label>
                <input type="text" placeholder="NOM / Prénom" name="parents" id="parents" value="parent matth">
            </div>
            <div class="geleves-cont-intruments">
                <h4>Les instruments</h4>
                <div class="geleves-souscont-instruments">';

                foreach($instruName as $name){
                    $formulaire.='<div>
                        <input type="radio" id="'.$name->nom.'" name="instruments" value="'.$name->nom.'">
                        <label for="'.$name->nom.'">'.$name->nom.'</label>
                    </div>';
                }


                $formulaire.='
                </div>
            </div>
        </div>
        <div class="cont_formules">
            <h3>Les formules :</h3>
            <div class="formules">
                <label class="type-abonnement">Individuels</label>
                <div>
                    <h4>Carte 10 cours</h4>
                    <div>
                        <label for="min30">30 min</label>
                        <input type="radio" name="abonnement" id="min30" value="individuel-carte-30min">
                        <label for="min45">45 min</label>
                        <input type="radio" name="abonnement" id="min45" value="individuel-carte-45min">
                        <label for="min60">1h</label>
                        <input type="radio" name="abonnement" id="min60" value="individuel-carte-1h">
                    </div>
                </div>
                <div>
                    <h4>Abonnement saison (formule annuelle)</h4>
                    <div>
                        <label for="min30annu">30 min</label>
                        <input type="radio" name="abonnement" id="min30annu" value="individuel-annuelle-30min">
                        <label for="min45annu">45 min</label>
                        <input type="radio" name="abonnement" id="min45annu" value="individuel-annuelle-45min">
                        <label for="min60annu">1h</label>
                        <input type="radio" name="abonnement" id="min60annu" value="individuel-annuelle-1h">
                    </div>
                </div>
            </div>
            <hr>
            <div class="formules">
                <label class="type-abonnement">COURS COLLECTIFS</label>
                <div>
                    <h4>Abonnement saison (formule annuelle)</h4>
                    <div>
                        <p for="binome">binôme</p>
                        <label for="min30abo">30 min</label>
                        <input type="radio" name="abonnement" id="min30abo" value="binome-annuelle-30min">
                        <label for="min45abo">45 min</label>
                        <input type="radio" name="abonnement" id="min45abo" value="binome-annuelle-45min">
                        <label for="min60abo">1h</label>
                        <input type="radio" name="abonnement" id="min60abo" value="binome-annuelle-1h">
                        <p for="trinome">trinôme</p>
                        <label for="min30trino">30 min</label>
                        <input type="radio" name="abonnement" id="min30trino" value="trinome-annuelle-30min">
                        <label for="min45trino">45 min</label>
                        <input type="radio" name="abonnement" id="min45trino" value="trinome-annuelle-45min">
                        <label for="min60trino">1h</label>
                        <input type="radio" name="abonnement" id="min60trino" value="trinome-annuelle-1h">
                    </div>
                </div>
            </div>
            <div class="geleves-cont-bout-form">
                <button type="submit" class="bout-sub">Envoyer</button>
                <input class="geleve-reset" type="reset">
            </div>
        </div>
    </form>
    <div class="message-valid">

    </div>
</section>';

return $formulaire;

}
add_shortcode( 'inscription', 'mon_shortcode' );