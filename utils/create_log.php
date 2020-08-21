<?php
namespace AGCompanyTeam;
require_once 'klogger.php';

$company_team_log_file_path = plugin_dir_path(__FILE__) . 'log';

$company_team_log = new KLogger($company_team_log_file_path, KLogger::INFO);
