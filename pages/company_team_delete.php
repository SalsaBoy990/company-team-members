<?php

if (current_user_can('manage_options')) {
  // delete current member

  global $wpdb;
  $valid = true;

  // prepare get statement protect against SQL inject
  $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "company_team WHERE id = %d", $id);

  $row = $wpdb->query($sql);
} else {
  echo 'You are not authorized to perform this action.';
}
