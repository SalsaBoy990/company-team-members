<?php
/*
Plugin Name: Company Team Members
Plugin URI: https://github.com/SalsaBoy990/company-team-members
Description: Company Team Members plugin
Version: 2.0.0
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: company-team
Domain Path: /languages
*/

defined('ABSPATH') or die();

// require all requires once
require_once 'requires.php';

use \AG\CompanyTeam\CompanyTeam as CompanyTeam;

use \AG\CompanyTeam\Log\KLogger as KLogger;


// path to folder where to store log files.
$company_team_log_file_path = plugin_dir_path(__FILE__) . '/log';

// instantiate logger class
$company_team_log = new KLogger($company_team_log_file_path, KLogger::INFO);

// instantiate main plugin singleton class
CompanyTeam::getInstance();

// we don't need to do anything when deactivation
// register_deactivation_hook(__FILE__, function () {});
register_activation_hook(__FILE__, '\AG\CompanyTeam\CompanyTeam::activatePlugin');
