<?php
// This code will only run when plugin is deleted
// it will drop the custom database table
// if you want to keep your data, save it
// click on Company Team menu,
// beneath the table there are the links to download table data in JSON and in CSV format
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    global $wpdb;
    $table_name = $wpdb->prefix . 'company_team';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    delete_option("company_team_db_version");
?>