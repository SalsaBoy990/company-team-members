<ul class="company-team grid-list">
  <?php
  if ($valid) :
    foreach ($formData as $row) :

      $profile_photo_field  = $row->profile_photo;
      $last_name_field     = $row->last_name;
      $first_name_field     = $row->first_name;
      $phone_field          = $row->phone;
      $email_field          = $row->email;
      $position_field       = $row->position;
      $department_field     = $row->department;
      $works_since_field    = $row->works_since;
  ?>
      <li class="mb1">
        <?php
        if ($photo) :
          if (!empty($profile_photo_field)) : ?>
            <img src="<?php echo $profile_photo_field ?>" alt="<?php echo ($first_name_first) ? ($first_name_field . ' ' . $last_name_field) : ($last_name_field . ' ' . $first_name_field); ?>" />
          <?php
          else :
          ?>
            <img src="<?php echo plugin_dir_url(__FILE__) . '../sample-image/profile-placeholder.png'; ?>" alt="placeholder image" />
        <?php
          endif;
        endif;
        ?>

        <div class="company-team list-heading">
          <?php if ($name) : ?>
            <h2>
              <?php if ($first_name_first) : ?>
                <?php echo $first_name_field . ' ' . $last_name_field; ?>
              <?php else :
                echo $last_name_field . ' ' . $first_name_field;
              endif; ?>
            </h2>
          <?php endif; ?>

          <?php if ($position) : ?>
            <div><?php echo $position_field; ?></div>
          <?php endif; ?>

          <?php if ($department) : ?>
            <div><?php echo $department_field; ?></div>
          <?php endif; ?>

          <?php if ($works_since) : ?>
            <div><?php echo $works_since_field; ?></div>
          <?php endif; ?>
        </div>

        <ul>
          <li>
            <?php if ($phone) : ?>
              <a href="<?php echo 'tel:' . $phone_field; ?>"><?php echo $phone_field; ?></a>
            <?php endif; ?>
          </li>
          <li>
            <?php if ($email) : ?>
              <a href="<?php echo 'mailto:' . $email_field; ?>"><?php echo $email_field; ?></a>
            <?php endif; ?>
          </li>
        </ul>
      </li>
  <?php
    endforeach;
  endif;
  ?>
</ul>