<?php




function custom_table_example_cours_page_handler()
{
    global $wpdb;

    $table = new Geleves_Custom_Table_Cours_List_Table();
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
        <h2><?php _e('Instruments')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cours_form');?>"><?php _e('Ajouter')?></a>
        </h2>
        <?php echo $message; ?>

        <form id="eleves-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display() ?>
        </form>

    </div>
    <?php
}


function custom_table_example_cours_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'table_geleve_instrument'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'nom' => "",
    );

    // here we are verifying does this request is post back and have correct nonce
    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = custom_table_example_cours_validate($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'custom_table_example');
                } else {
                    $notice = __('There was an error while saving item', 'custom_table_example');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'custom_table_example');
                } else {
                    $notice = __('There was an error while updating item', 'custom_table_example');
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
    add_meta_box('cours_form_meta_box', 'Instrument', 'custom_table_example_cours_form_meta_box_handler', 'cours', 'normal', 'default');

    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Instruments', 'custom_table_example')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=gestion-cours');?>"><?php _e('Revenir à la liste', 'custom_table_example')?></a>
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
                        <?php do_meta_boxes('cours', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Enregistrer les modifications', 'custom_table_example')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}



function custom_table_example_cours_form_meta_box_handler($item)
{
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="nom"><?php _e('Nom')?></label>
            </th>
            <td>
                <input id="nom" name="nom" type="text" style="width: 95%" value="<?php echo esc_attr($item['nom'])?>"
                       size="50" class="code" placeholder="<?php _e('Nom')?>" required>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}




class Geleves_Custom_Table_Cours_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'cour',
            'plural' => 'cours',
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
            'edit' => sprintf('<a href="?page=cours_form&id=%s">%s</a>', $item['id'], __('Edit')),
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
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'name' => __('Nom'),
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
        $table_name = $wpdb->prefix . 'table_geleve_instrument'; // do not forget about tables prefix

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
        $table_name = $wpdb->prefix . 'table_geleve_instrument'; // do not forget about tables prefix

        $per_page = 5; // constant, how much records will be shown per page

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
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

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
function custom_table_example_cours_validate($item)
{
    $messages = array();

    if (empty($item['nom'])) $messages[] = __('Name is required');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}