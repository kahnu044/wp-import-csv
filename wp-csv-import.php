<?php

/*
Plugin Name: Wp CSV Import
Description: Used To Import CSV
Version: 0.1
Author: Debaprasad Nanda
*/

define("PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
define("PLUGIN_URL", plugins_url());

// echo plugins_url('assests/css/style.css', __FILE__);die;

// Create a new table
register_activation_hook(__FILE__, 'csv_import_plugin');
function csv_import_plugin()
{

   global $wpdb;
   $charset_collate = $wpdb->get_charset_collate();

   $normal_csv = $wpdb->prefix . "normal_csv";

   $key_value_csv = $wpdb->prefix . "keyvalue_csv";

   $donor_csv = $wpdb->prefix . "donor_csv_import";

   $normal_csv_import = "CREATE TABLE `$normal_csv` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(250) NOT NULL,
    `email` varchar(250) NOT NULL,
    `phone` int(10) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

   $keyvalue_csv_import = "CREATE TABLE `$key_value_csv` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `row_id` int(11) NOT NULL,
    `csv_key` varchar(250) NOT NULL,
    `csv_value` varchar(250) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

   $donor_csv_import = "CREATE TABLE `$donor_csv` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `form_id` int(11) NOT NULL,
   `form_title` varchar(250) NOT NULL,
   `form_value` longtext NOT NULL,
   `form_submit_date` varchar(250) NOT NULL,
   `is_deleted` int(11) NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

   dbDelta($normal_csv_import);
   dbDelta($keyvalue_csv_import);
   dbDelta($donor_csv_import);
}

// Add menu and submenu
add_action("admin_menu", "plugin_menu");
function plugin_menu()
{

   add_menu_page("WP CSV Import", "WP CSV Import", "manage_options", "wp-csv-import", "normal_csv_import_function", "dashicons-admin-network");

   add_submenu_page("wp-csv-import", "Key Value CSV Import", "Key Value CSV Import", "manage_options", "key-value-csv", "keyvalue_csv_import_function");

   add_submenu_page("wp-csv-import", "Donor CSV Import", "Donor CSV Import", "manage_options", "donor-csv-import", "donor_csv_import_function");
}


//function for normal csv import
function normal_csv_import_function()
{
   include_once PLUGIN_DIR_PATH . "views/normalcsvimport.php";
}

//function for keyvalue csv import
function keyvalue_csv_import_function()
{
   include_once PLUGIN_DIR_PATH . "views/keyvaluecsvimport.php";
}

//function for donor csv import
function donor_csv_import_function()
{
   include_once PLUGIN_DIR_PATH . "views/donorcsvimport.php";
}

//Add style in field and display table
add_action("init", "add_csv_import_style");
function add_csv_import_style()
{
   wp_enqueue_style("csv_import_style", plugins_url('assest/css/import_csv_style.css', __FILE__));
}