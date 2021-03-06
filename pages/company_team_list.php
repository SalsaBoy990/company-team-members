<?php

use \AG\CompanyTeam\File\SaveData as SaveData;

if (current_user_can('manage_options')) {
    // display member list in a admin table
    global $wpdb;
    $valid = true;

    $sql = "SELECT * FROM " . $wpdb->prefix . "company_team";

    $formData = $wpdb->get_results($sql);

    // print_r($formData);

    if (!$formData) {
        $valid = false;
        echo $sql . '- This form is invalid.';
    }

    $json_data = json_encode($formData);
} else {
    $json_data = null;
    $valid = false;
    echo 'You are not authorized to perform this action.';
}


// $current = get_current_screen(  );
// print_r($current);

?>
<h1 class="mt1 mb1"><?php echo __('Manage Company Team Members', 'company-team'); ?></h1>
<form action="" method="post" class="mb1">
    <input type="hidden" name="listaction" value="insert">
    <button type="submit" class="button-primary"><span class="company-team dashicons dashicons-plus"></span><?php _e('Add new member', 'company-team'); ?></button>
</form>

<div class="company-team-wrapper">
    <table class="company-team widefat table table-striped">
        <thead>
            <tr>
                <!-- <th scope="col">#</th> -->
                <th scope="col"><?php _e('Action', 'company-team'); ?></th>
                <th scope="col"><?php _e('Profile img', 'company-team'); ?></th>
                <th scope="col"><?php _e('Last name', 'company-team'); ?></th>
                <th scope="col"><?php _e('First name', 'company-team'); ?></th>
                <th scope="col"><?php _e('Phone number', 'company-team'); ?></th>
                <th scope="col"><?php _e('Email', 'company-team'); ?></th>
                <th scope="col"><?php _e('Position', 'company-team'); ?></th>
                <th scope="col"><?php _e('Department', 'company-team'); ?></th>
                <th scope="col"><?php _e('Works since', 'company-team'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($valid) :
                foreach ($formData as $row) :

                    $id             = $row->id;
                    $profile_photo  = $row->profile_photo;
                    $last_name      = $row->last_name;
                    $first_name     = $row->first_name;
                    $phone          = $row->phone;
                    $email          = $row->email;
                    $position       = $row->position;
                    $department     = $row->department;
                    $works_since    = $row->works_since;

            ?>
                    <tr>
                        <form action="" method="post">
                            <input type="hidden" name="listaction" value="edit">
                            <input type="hidden" name="memberid" value="<?php echo esc_html($id) ?>">
                            <!-- <td><?php echo esc_html($id); ?></td> -->
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="submit" class="button-secondary"><span class="company-team dashicons dashicons-edit"></span><?php _e('Edit', 'company-team'); ?></button>
                                </div>
                            </td>
                            <td class="small-col">
                                <?php
                                if (!empty($profile_photo)) : ?>
                                    <img class="small-image" src="<?php echo esc_url($profile_photo) ?>" alt="<?php echo esc_html($last_name) . ' ' . esc_html($first_name); ?>" />
                                <?php
                                else :
                                ?>
                                    <img class="small-image" src="<?php echo plugin_dir_url(__FILE__) . '../sample-image/profile-placeholder.png'; ?>" alt="placeholder image" />
                                <?php
                                endif;
                                ?>
                            </td>
                            <td class="small-col"><?php echo esc_html($last_name); ?></td>
                            <td class="small-col"><?php echo esc_html($first_name); ?></td>
                            <td class="medium-col"><?php echo esc_html($phone); ?></td>
                            <td class="medium-col"><?php echo esc_html($email); ?></td>
                            <td class="medium-col"><?php echo esc_html($position); ?></td>
                            <td class="medium-col"><?php echo esc_html($department); ?></td>
                            <td class="medium-col"><?php echo esc_html($works_since); ?></td>
                        </form>
                    </tr>
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>
<?php

if (current_user_can('manage_options')) {
    $ag_company_team_save_data = new SaveData();

    $ag_company_team_save_data->saveDataToJSON('company_team', $json_data);

    $ag_company_team_save_data->saveDataToCSV('company_team', $formData);
}
?>