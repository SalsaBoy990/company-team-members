<?php
// edit form for simple member

global $wpdb;
$valid = true;

// prepare get statement protect against SQL inject
$sql = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "company_team WHERE id = %d", $id);

$row = $wpdb->get_row($sql);

// get the values for the current member record
$last_name    = $row->last_name;
$first_name   = $row->first_name;
$phone        = $row->phone;
$email        = $row->email;
$position     = $row->position;
$department   = $row->department;
$works_since  = $row->works_since;
$profile_photo = $row->profile_photo;

// print_r($formData);

if (!$row) {
  $valid = false;
  echo $sql . '- This form is invalid.';
}
?>
<h1><?php echo __('Edit company team member details', 'company-team'); ?></h1>


<div class="card bg-light">
  <div class="card-header">

    <h3 class="card-title">
      <?php _e('Member details', 'company-team'); ?>
    </h3>
  </div>
  <div class="card-body">
    <div>
      <form action="#" method="post" enctype="multipart/form-data">
        <input type="hidden" name="memberid" value="<?php echo $id; ?>">
        <div class="form-group mbhalf">
          <label for="last_name"><?php _e('Last name', 'company-team'); ?></label><br />
          <input type="text" class="form-control" name="last_name" value="<?php echo $last_name; ?>" />
        </div>
        <div class="form-group mbhalf">
          <label for="first_name"><?php _e('First name', 'company-team'); ?></label><br />
          <input type="text" class="form-control" name="first_name" value="<?php echo $first_name; ?>" />
        </div>
        <div class="form-group mbhalf">
          <label for="profilepicture"><?php _e('Profile Photo', 'company-team'); ?></label>
          <div class="company-team mthalf">
            <input type="file" class="custom-file-label button-secondary" type="file" name="profilepicture" size="25" id="profilepicture" aria-describedby="profilepicture">
          </div>
          <span class="italic">(Min. resolution: 500px * 500px)</span>
        </div>
        <div class="form-group mbhalf">
          <label for="phone"><?php _e('Phone number', 'company-team'); ?></label><br />
          <input type="tel" class="form-control regular-text" name="phone" value="<?php echo $phone; ?>" aria-placeholder="Phone number should start with the country code like +36 for Hungary, phone number should not contain separator characters like '-' or '/'" />
        </div>
        <div class="form-group mbhalf">
          <label for="email"><?php _e('Email address', 'company-team'); ?></label><br />
          <input type="email" class="form-control regular-text" name="email" value="<?php echo $email; ?>" />
        </div>
        <div class="form-group mbhalf">
          <label for="position"><?php _e('Position at the company', 'company-team'); ?></label><br />
          <input type="text" class="form-control regular-text" name="position" value="<?php echo $position; ?>" />
        </div>
        <div class="form-group mbhalf">
          <label for="department"><?php _e('Department', 'company-team'); ?></label><br />
          <input type="text" class="form-control regular-text" name="department" value="<?php echo $department; ?>" />
        </div>
        <div class="form-group mbhalf">
          <label for="works_since"><?php _e('Works Since', 'company-team'); ?></label><br />
          <input type="date" class="form-control regular-text" name="works_since" value="<?php echo $works_since; ?>" />
        </div>

        <div class="mt1">
          <button type="submit" name="listaction" value="handleupdate" class="button-primary"><?php _e('Update', 'company-team'); ?></button>
          <button type="submit" name="listaction" value="list" class="button-secondary"><?php _e('Cancel', 'company-team'); ?></button>
          <button type="submit" name="listaction" value="handledelete" class="company-team button-secondary button-danger" onclick="return confirm('Are you sure you want to delete this member?'); "><?php _e('Delete', 'company-team'); ?></button>
        </div>
      </form>
    </div>
  </div>
</div>