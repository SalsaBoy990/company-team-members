<table class="company-team table table-striped">
  <thead>
    <tr>
      <?php if ($photo): ?>
      <th scope="col"></th>
      <?php endif; ?>
      <?php if ($name): ?>
      <th scope="col"><?php _e('Name', 'company-team'); ?></th>
      <?php endif; ?>
      <?php if ($position): ?>
      <th scope="col"><?php _e('Position', 'company-team'); ?></th>
      <?php endif; ?>
      <?php if ($department): ?>
      <th scope="col"><?php _e('Department', 'company-team'); ?></th>
      <?php endif; ?>
      <?php if ($works_since): ?>
      <th scope="col"><?php _e('Works since', 'company-team'); ?></th>
      <?php endif; ?>
      <?php if ($phone): ?>
      <th scope="col"><?php _e('Phone', 'company-team'); ?></th>
      <?php endif; ?>
      <?php if ($email): ?>
      <th scope="col"><?php _e('Email', 'company-team'); ?></th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($valid) :
      foreach ($formData as $row) :

        $profile_photo_field  = $row->profile_photo;
        $last_name_field      = $row->last_name;
        $first_name_field     = $row->first_name;
        $phone_field          = $row->phone;
        $email_field          = $row->email;
        $position_field       = $row->position;
        $department_field     = $row->department;
        $works_since_field    = $row->works_since;
    ?>
        <tr>
          <td class="image-table">
            <?php
            if (!empty($profile_photo_field)) : ?>
              <img src="<?php echo $profile_photo_field ?>" alt="<?php echo ($first_name_first) ? ($first_name_field . ' ' . $last_name_field) : ($last_name_field . ' ' . $first_name_field); ?>" />
            <?php
            else :
            ?>
               <img src="<?php echo plugin_dir_url(__FILE__) . '../sample-image/profile-placeholder.png'; ?>" alt="<?php esc_attr_e('placeholder image', 'company-team'); ?> />
            <?php
            endif;
            ?>
          </td>
          <?php if ($name) : ?>
            <td>
              <?php if ($first_name_first) : ?>
                <?php echo $first_name_field . ' ' . $last_name_field; ?>
              <?php else :
                echo $last_name_field . ' ' . $first_name_field;
              endif; ?>
            </td>
          <?php endif; ?>

          <?php if ($position) : ?>
            <td><?php echo $position_field; ?></td>
          <?php endif; ?>

          <?php if ($department) : ?>
            <td><?php echo $department_field; ?></td>
          <?php endif; ?>

          <?php if ($works_since) : ?>
            <td><?php echo $works_since_field; ?></td>
          <?php endif; ?>

          <?php if ($phone) : ?>
            <td>
              <a href="<?php echo 'tel:' . $phone_field; ?>"><?php echo $phone_field; ?></a>
            </td>
          <?php endif; ?>
          <?php if ($email) : ?>
            <td>
              <a href="<?php echo 'mailto:' . $email_field; ?>"><?php echo $email_field; ?></a>
            </td>
          <?php endif; ?>
        </tr>
    <?php
      endforeach;
    endif;
    ?>
  </tbody>
</table>