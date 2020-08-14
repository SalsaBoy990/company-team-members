<?php
require_once('klogger.php');

$log_file_path = plugin_dir_path(__FILE__) . 'log';

$log = new AG_Company_Team_KLogger($log_file_path, AG_Company_Team_KLogger::INFO);
