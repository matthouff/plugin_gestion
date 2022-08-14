<?php




function Functions_Add_Delete_Eleve()
{
    global $wpdb;

    $table = new Geleves_Custom_Table_Eleves_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        if(is_iterable($_REQUEST['id'])){
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Elements supprimés: %d'), count($_REQUEST['id'])) . '</p></div>';
        }
        else $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Elements supprimé: %d'), 1) . '</p></div>';
    } ?>
    <div class="wrap">

        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Elèves')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=eleves_form');?>"><?php _e('Ajouter')?></a>
        </h2>
        <?php echo $message; ?>

        <form id="eleves-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display() ?>
        </form>

    </div>
    <?php
}


function custom_table_example_eleves_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'table_geleve_eleve'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'id_instrument' => "",
        'id_prof' => "",
        'nom' => "",
        'prenom' => "",
        'date_naissance' => "",
        'adresse' => "",
        'tel' => "",
        'mail' => "",
        'nom_prenom_parents' => "",
        'formule' => "",
        'valid' => "en_attente",
        'user_id' => "",
        'compte' => "",
    );

    // here we are verifying does this request is post back and have correct nonce
    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = custom_table_example_eleves_validate($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Enregistré', 'custom_table_example');
                } else {
                    $notice = __('Vos informations n\'ont pas pu être enregistrés', 'custom_table_example');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Modifié', 'custom_table_example');
                } else {
                    $notice = __('Vos informations sont déjà mises à jours', 'custom_table_example');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'custom_table_example');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('eleves_form_meta_box', 'Information de l\'éleve', 'Form_Data_Back_Eleve', 'eleve', 'normal', 'default');

    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('eleve', 'custom_table_example')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=gestion-eleves');?>"><?php _e('Revenir à la liste', 'custom_table_example')?></a>
        </h2>

        <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php /* And here we call our custom meta box */ ?>
                        <?php do_meta_boxes('eleve', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Enregistrer', 'custom_table_example')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}



function Form_Data_Back_Eleve($item)
{
    global $wpdb;

    $instrument = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'table_geleve_instrument WHERE id = (SELECT id_instrument FROM '.$wpdb->prefix.'table_geleve_eleve WHERE id = '. $item['id'] .')', ARRAY_A);
    $professeur = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'table_geleve_prof WHERE id = (SELECT id_prof FROM '.$wpdb->prefix.'table_geleve_eleve WHERE id = '. $item['id'] .')', ARRAY_A);
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="nom"><?php _e('NOM')?></label>
            </th>
            <td>
                <input id="nom" name="nom" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['nom']))?>"
                       size="50" class="code" placeholder="<?php _e('NOM')?>" required>
            </td>
        </tr>
        
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="prenom"><?php _e('Prénom')?></label>
            </th>
            <td>
                <input id="prenom" name="prenom" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['prenom']))?>"
                       size="50" class="code" placeholder="<?php _e('Prénom')?>" required>
            </td>
        </tr>

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="date_naissance"><?php _e('Date de naissance')?></label>
            </th>
            <td>
                <input id="date_naissance" name="date_naissance" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['date_naissance']))?>"
                       size="50" class="code" placeholder="<?php _e('Date de naissance')?>" required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="adresse"><?php _e('Adresse')?></label>
            </th>
            <td>
                <input id="adresse" name="adresse" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['adresse']))?>"
                       size="50" class="code" placeholder="<?php _e('Adresse')?>" required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="tel"><?php _e('tel')?></label>
            </th>
            <td>
                <input id="tel" name="tel" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['tel']))?>"
                       size="50" class="code" placeholder="<?php _e('Numéro de téléphone')?>" required>
            </td>
        </tr>
        

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="email"><?php _e('E-Mail')?></label>
            </th>
            <td>
                <input id="email" name="mail" type="email" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['mail']))?>"
                       size="50" class="code" placeholder="<?php _e('E-Mail')?>" required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="nom_parent"><?php _e('Nom des parents de l\'élève')?></label>
            </th>
            <td>
                <input id="nom_parent" name="nom_prenom_parents" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['nom_prenom_parents']))?>"
                       size="50" class="code" placeholder="<?php _e('Nom des parents de l\'élève')?>" required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="formule"><?php _e('formule')?></label>
            </th>
            <td>
                <input id="formule" name="formule" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['formule']))?>"
                       size="50" class="code" placeholder="<?php _e('formule')?>" required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="id_prof"><?php _e('Professeur')?></label>
            </th>
            <td>
                <?php 
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'table_geleve_prof';
                    $profName = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'table_geleve_prof');?>

                    <select name="id_prof">
                        <option value="">Choisir un professeur</option>
                        <?php
                        foreach($profName as $name){ ?>
                            <?php 
                            if($professeur != NULL){ ?>
                                <option id="<?= $name->nom ?>" name="id_prof" value="<?= $name->id ?>" size="50" class="code" <?= $name->id === $professeur['id'] ? "selected" : "" ?>><?= $name->prenom ." ". $name->nom?></option><?php
                            } else{ ?>
                                <option id="<?= $name->nom ?>" name="id_prof" value="<?= $name->id ?>" size="50" class="code"><?= $name->prenom ." ". $name->nom?></option><?php
                            }
                        }?>
                    </select>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="id_instrument"><?php _e('Instruments')?></label>
            </th>
            <td>
                <?php 
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'table_geleve_instrument';
                    $instruName = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'table_geleve_instrument');

                    foreach($instruName as $name){ ?>
                        <label for="<?= $name->nom ?>"><?= $name->nom ?></label>
                        <?php 
                        if($instrument != NULL){ ?>
                            <input id="<?= $name->nom ?>" name="id_instrument" type="radio" value="<?= $name->id ?>" size="50" class="code" <?= $name->nom === $instrument['nom'] ? "checked" : "" ?>><?php 
                        } else{ ?>
                            <input id="<?= $name->nom ?>" name="id_instrument" type="radio" value="<?= $name->id ?>" size="50" class="code"><?php 
                        }
                    }?>
            </td>
        </tr>
        
        
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="valid"><?php _e('validation')?></label>
            </th>
            <td>
                <label for="valid1"><?php _e('Oui') ?></label>
                <input id="valid1" name="valid" type="radio" value="valid" size="50" class="code"  <?= $item['valid'] !== "en_attente" ? "checked" : "" ?>>
                <label for="valid2"><?php _e('Non') ?></label>
                <input id="valid2" name="valid" type="radio" value="en_attente" size="50" class="code" <?= $item['valid'] === "en_attente" ? "checked" : "" ?>>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="compte"><?php _e('Compte')?></label>
            </th>
            <td>
                <label for="compte"><?php _e('Créer un compte') ?></label>
                <input id="compte" name="compte" type="checkbox" value="1" size="50" class="code" <?= $item['compte'] ? 'checked' : '' ?>>
            </td>
        </tr>
        </tbody>
    </table>
    <?php 
    echo '<pre>';
    print_r($item);
    echo '</pre>';
}



class Geleves_Custom_Table_Eleves_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'eleve',
            'plural' => 'eleves',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_nom($item)
    {
        return '<em>' . $item['nom'] . '</em>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &eleve=2
        $actions = array(
            'edit' => sprintf('<a href="?page=eleves_form&id=%s">%s</a>', $item['id'], __('Edit')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete')),
        );

        return sprintf('%s %s',
            $item['nom'],
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] Rendu de la colonne des cases à cocher
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }


    /**
     * [REQUIRED] Cette méthode renvoie les colonnes à afficher dans le tableau. On peut ignorer les colonnes qu'on ne souhaite pas afficher, comme le contenu ou la description.
     *
     * @return array
     */
    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox"/>', //Render a checkbox instead of text
            'name' => __('Nom'),
            'prenom' => __('Prénom'),
            'date_naissance' => __('Date de n\'aissance'),
            'mail' => __('E-Mail'),
            'nom_instrument' => __('Instruments'),
            'nom_prof' => __('Professeurs'),
            'formule' => __('Formule'),
            'valid' => __('Status'),
            // 'payer' => __('Paiement'),
        );
        return $columns;
    }


    /**
     * [OPTIONAL] Cette méthode met en avant les colonnes qui peuvent être utilisées pour trier le tableau
     *
     * @return array
     */
    function get_sortable_columns()
    {
        

        $sortable_columns = array(
            'name' => array('nom', true),
            'mail' => array('mail', false),
            'formule' => array('formule', false),
            'date_naissance' => array('date_naissance', false),
            'nom_instrument' => array('nom_instrument', false),
            'nom_prof' => array('nom_prof', false),
            'valid' => array('valid', false),
            // 'payer' => array('payer', false),
        );
        return $sortable_columns;
    }


    /**
     * [OPTIONAL] Retourne un tableau d'actions s'il y en a
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] Cette méthode traite les actions de masse, elle peut être en dehors de la classe, ici nous traitons l'action de suppression
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'table_geleve_eleve'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    /**
     * [REQUIRED] C'est la méthode la plus importante
     *
     * Il récupérera les lignes de la base de données et les préparera pour les afficher dans la table.
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'table_geleve_eleve'; // do not forget about tables prefix
        $table_name_instrus = $wpdb->prefix . 'table_geleve_instrument'; // do not forget about tables prefix
        $table_name_prof = $wpdb->prefix . 'table_geleve_prof'; // do not forget about tables prefix

        $per_page = 30; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? ($per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'nom';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // remplace l'id_instrument et l'id_prof de la table "eleve" par leur valeur choisit dans les tables "instrument" et "professeur"
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT ".$table_name.".*, ".$table_name_instrus.".nom as nom_instrument, CONCAT(".$table_name_prof.".prenom, ' ', ".$table_name_prof.".nom) as nom_prof FROM ".$table_name." LEFT JOIN ".$table_name_instrus." ON (".$table_name_instrus.".id = ".$table_name.".id_instrument) LEFT JOIN ".$table_name_prof." ON (".$table_name_prof.".id = ".$table_name.".id_prof)  ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);


        /*
        SELECT promodw_table_geleve_eleve.*, promodw_table_geleve_instrument.nom as nom_instrument, CONCAT(promodw_table_geleve_prof.nom, ' ', promodw_table_geleve_prof.prenom) as id_prof 
        FROM promodw_table_geleve_eleve 
        INNER JOIN promodw_table_geleve_instrument, promodw_table_geleve_prof 
        WHERE (promodw_table_geleve_instrument.id = promodw_table_geleve_eleve.id_instrument) 
        AND (promodw_table_geleve_prof.id = promodw_table_geleve_eleve.id_prof) 
        ORDER BY $orderby $order LIMIT %d OFFSET %d
        */
        
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}


/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function custom_table_example_eleves_validate($item)
{
    $messages = array();
    $username = strtolower(str_replace(" ", "_", $item['nom']));
    $password = null;
    $email = $item['mail'];

    global $wpdb; 
    $id_post = $item['id'];
    

    // PERMET DE CREER UN UTILISATEUR LORS DE LA VALIDATOIN DE L'ELEVE

    if (username_exists($username) == null && email_exists($email) == false && $item['valid'] === "valid") {
       // Create the new user
        $user_id = wp_create_user($username, $password, $email);

       // Get current user object
        $user = get_user_by('id', $user_id);
    
       // remove role
        $user->remove_role('subscriber');
    
       // Add role
        $user->add_role('eleve');
        $dbData = array();
        $dbData['user_id'] = $user->ID;
        $wpdb->update('wp_table_geleve_eleve', $dbData, array('id' => $id_post));
    }



    if (empty($item['nom'])) $messages[] = __('Name is required');
    if (empty($item['date_naissance'])) $messages[] = __('Name is required');
    if (empty($item['adresse'])) $messages[] = __('Name is required');
    if (empty($item['tel'])) $messages[] = __('Name is required');
    if (!empty($item['mail']) && !is_email($item['mail'])) $messages[] = __('E-Mail is in wrong format');
    if (empty($item['nom_prenom_parents'])) $messages[] = __('Parent is required');
    if (empty($item['id_instrument'])) $messages[] = __('Instrument is required');
    if (empty($item['id_prof'])) $messages[] = __('Professeur is required');
    if (empty($item['formule'])) $messages[] = __('Formule is required');

    if (empty($messages)) return true;
    
    return implode('<br />', $messages);
}
