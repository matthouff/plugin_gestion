<?php    
    global $wpdb;

    // Nom des tables

    $table_name_eleve = $wpdb->prefix . 'table_geleve_eleve';
    $table_name_prof = $wpdb->prefix . 'table_geleve_prof';
    $table_name_cours = $wpdb->prefix . 'table_geleve_cours';
    $table_name_instrument = $wpdb->prefix . 'table_geleve_instrument';
    $table_name_liens_prof_instrument = $wpdb->prefix . 'table_geleve_liens_prof_instrument';


    // wp_table_geleve_eleve
    // wp_table_geleve_prof
    // wp_table_geleve_cours
    // wp_table_geleve_instrument
    // wp_table_geleve_liens_prof_eleve



    // création des table et des champs SQL 

    $sql_gestion_eleve =
    "CREATE TABLE `{$table_name_eleve}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_instrument` int(11) DEFAULT NULL,
        `id_prof` int(11) DEFAULT NULL,
        `nom` varchar(255) DEFAULT NULL,
        `prenom` varchar(255) DEFAULT NULL,
        `date_naissance` varchar(255) DEFAULT NULL,
        `adresse` varchar(255) DEFAULT NULL,
        `tel` varchar(255) DEFAULT NULL,
        `mail` varchar(255) DEFAULT NULL,
        `nom_prenom_parents` varchar(255) DEFAULT NULL,
        `formule` varchar(255) DEFAULT NULL,
        `valid` varchar(255) NOT NULL DEFAULT 'en_attente',
        `user_id` INT(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `id_instrument` (`id_instrument`),
        KEY `id_prof` (`id_prof`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   
    CREATE TABLE `{$table_name_prof}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_instrument` varchar(20) DEFAULT NULL,
        `nom` varchar(255) DEFAULT NULL,
        `prenom` varchar(255) DEFAULT NULL,
        `mail` varchar(255) DEFAULT NULL,
        `tel` varchar(255) DEFAULT NULL,
        `ville` varchar(255) DEFAULT NULL,
        `code_postale` int(10) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `{$table_name_cours}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_instrument` int(11) DEFAULT NULL,
        `id_prof` int(11) DEFAULT NULL,
        `duree` int(5) DEFAULT NULL,
        `nb_eleves` int(5) DEFAULT NULL,
        `nom` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `id_instrument` (`id_instrument`),
        KEY `id_prof` (`id_prof`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `{$table_name_instrument}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nom` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE `{$table_name_liens_prof_instrument}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_prof` int(11) DEFAULT NULL,
        `id_instrument` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `id_instrument` (`id_instrument`),
        KEY `id_prof` (`id_prof`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


    
    
    
    
    // Liaison entre les tables
    
    $sql_gestion_eleve_liens =
    "ALTER TABLE `{$table_name_eleve}`
        ADD CONSTRAINT `eleve_ibfk_1` FOREIGN KEY (`id_instrument`) REFERENCES `{$table_name_instrument}` (`id`),
        ADD CONSTRAINT `eleve_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `{$table_name_prof}` (`id`);";
    $sql_gestion_eleve_liens2 =
    "ALTER TABLE `{$table_name_liens_prof_instrument}`
        ADD CONSTRAINT `instrument_ibfk_1` FOREIGN KEY (`id_instrument`) REFERENCES `{$table_name_instrument}` (`id`),
        ADD CONSTRAINT `instrument_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `{$table_name_prof}` (`id`);";
    $sql_gestion_eleve_liens3 =
    "ALTER TABLE `{$table_name_cours}`
        ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`id_instrument`) REFERENCES `{$table_name_instrument}` (`id`),
        ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `{$table_name_prof}` (`id`);";
    $sql_gestion_eleve_liens4 =
    "ALTER TABLE `{$table_name_cours}`
        ADD CONSTRAINT `prof_ibfk_1` FOREIGN KEY (`id_instrument`) REFERENCES `{$table_name_instrument}` (`id`);";


 //echo $sql_gestion_eleve."<BR />";
// echo $sql_gestion_eleve_key."<BR />";
 //echo $sql_gestion_eleve_liens;
 //exit;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta( $sql_gestion_eleve );
    //dbDelta( $sql_gestion_eleve_liens );    
    $query_result = $wpdb->query( $sql_gestion_eleve_liens );
    $query_result = $wpdb->query( $sql_gestion_eleve_liens2 );
    $query_result = $wpdb->query( $sql_gestion_eleve_liens3 );