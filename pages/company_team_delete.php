<?php
// delete current member

global $wpdb;
$valid = true;

// prepare get statement protect against SQL inject
$sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "company_team WHERE id = %d", $id);

$row = $wpdb->query($sql);
