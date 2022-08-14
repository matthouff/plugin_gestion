<?php

global $wpdb;


$table_name = $wpdb->prefix . "table_geleve_eleve";
$sql = "DROP TABLE IF EXISTS $table_name;";
$wpdb->query($sql);

$table_name = $wpdb->prefix . "table_geleve_cours";
$sql = "DROP TABLE IF EXISTS $table_name;";
$wpdb->query($sql);

$table_name = $wpdb->prefix . "table_geleve_liens_prof_instrument";
$sql = "DROP TABLE IF EXISTS $table_name;";
$wpdb->query($sql);

$table_name = $wpdb->prefix . "table_geleve_instrument";
$sql = "DROP TABLE IF EXISTS $table_name;";
$wpdb->query($sql);

$table_name = $wpdb->prefix . "table_geleve_prof";
$sql = "DROP TABLE IF EXISTS $table_name;";
$wpdb->query($sql);