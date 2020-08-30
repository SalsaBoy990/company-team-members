<h1><?php _e('Add new company team member', 'company-team'); ?></h1>

<div class="card bg-light">
    <div class="card-header">

        <h3 class="card-title">
            <?php _e('Add member details', 'company-team'); ?>
        </h3>
    </div>
    <div class="card-body">
        <div>
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="memberid" value="">
                <?php wp_nonce_field('company_team_insert', 'company_admin_insert_security'); ?>
                <div class="form-group mbhalf">
                    <label for="last_name"><?php _e('Last name', 'company-team'); ?></label><br />
                    <input type="text" class="form-control" name="last_name" value="" />
                </div>
                <div class="form-group mbhalf">
                    <label for="first_name"><?php _e('First name', 'company-team'); ?></label><br />
                    <input type="text" class="form-control" name="first_name" value="" />
                </div>
                <div class="form-group mbhalf">
                    <label for="profilepicture"><?php _e('Profile Photo', 'company-team'); ?></label>
                    <div class="company-team mthalf">
                        <input type="file" name="profilepicture" id="profilepicture" aria-describedby="profilepicture">
                    </div>
                    <span class="italic"></span>
                </div>
                <div class="form-group mbhalf">
                    <label for="phone"><?php _e('Phone number', 'company-team'); ?></label><br />
                    <input type="tel" class="form-control regular-text" name="phone" value="" aria-placeholder="Phone number should start with the country code like +36 for Hungary, phone number should not contain separator characters like '-' or '/'" />
                </div>
                <div class="form-group mbhalf">
                    <label for="email"><?php _e('Email address', 'company-team'); ?></label><br />
                    <input type="email" class="form-control regular-text" name="email" value="" />
                </div>
                <div class="form-group mbhalf">
                    <label for="position"><?php _e('Position at the company', 'company-team'); ?></label><br />
                    <input type="text" class="form-control regular-text" name="position" value="" />
                </div>
                <div class="form-group mbhalf">
                    <label for="department"><?php _e('Department', 'company-team'); ?></label><br />
                    <input type="text" class="form-control regular-text" name="department" value="" />
                </div>
                <div class="form-group mbhalf">
                    <label for="works_since"><?php _e('Works Since', 'company-team'); ?></label><br />
                    <input type="date" class="form-control regular-text" name="works_since" value="" />
                </div>

                <div class="mt1">
                    <button type="submit" name="listaction" value="handleinsert" class="button-primary"><?php _e('Add new member', 'company-team'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>