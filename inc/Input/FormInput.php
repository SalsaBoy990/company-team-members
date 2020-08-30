<?php
namespace AG\CompanyTeam\Input;

trait FormInput
{
    use \AG\CompanyTeam\Log\Logger;

    /**
     * Get form input, sanitize values
     * @return array associative
     */
    public function getFormInputValues(): array
    {
        $this->logger(\AG_COMPANY_TEAM_DEBUG, \AG_COMPANY_TEAM_LOGGING);

        // store escaped user input field values
        $formValues = array();

        if ($_FILES['profilepicture']['name'] != null && !empty($_FILES['profilepicture'])) {
            // echo '<pre>';
            // print_r($_FILES['profilepicture']);
            // echo '</pre>';
            try {
                // get error code from file input object
                $error_code = intval($_FILES['profilepicture']['error'], 10);
                echo $error_code;
                $profilePicture = $_FILES['profilepicture'];
                // POST image error
                if ($error_code > 0) {
                    /**
                     * Error code explanations
                     * @see https://www.php.net/manual/en/features.file-upload.errors.php
                     */
                    switch ($error_code) {
                        case 1:
                            throw new ImageInputException(
                                'The uploaded file exceeds the upload_max_filesize directive in php.ini.'
                            );
                            break;
                        case 2:
                            throw new ImageInputException(
                                'The uploaded file exceeds the MAX_FILE_SIZE directive'
                                    . 'that was specified in the HTML form.'
                            );
                            break;
                        case 3:
                            throw new ImageInputException(
                                'The uploaded file was only partially uploaded.'
                            );
                            break;
                        case 4:
                            throw new NoImageUploadException(
                                'No profile image was uploaded. The existing image will be used,'
                                    . 'or if no image exists, a placeholder image will be used.'
                            );
                            break;
                        case 6:
                            throw new ImageInputException(
                                'Missing a temporary folder.'
                            );
                            break;
                        case 7:
                            throw new ImageInputException(
                                'Failed to write file to disk.'
                            );
                            break;
                        case 8:
                            throw new ImageInputException(
                                'A PHP extension stopped the file upload.'
                            );
                            break;
                        default:
                            throw new ImageInputException(
                                'An unspecified PHP error occured.'
                            );
                            break;
                    }
                }
                $new_file_url = $this->addProfilePhoto($profilePicture);
                $formValues['new_file_url'] = $new_file_url;
            } catch (NoImageUploadException $ex) {
                echo '<div class="notice notice-warning is-dismissable"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_COMPANY_TEAM_LOGGING, $ex);

                $formValues['new_file_url'] = '';
            } catch (ImageInputException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
                $this->exceptionLogger(\AG_COMPANY_TEAM_LOGGING, $ex);

                $formValues['new_file_url'] = '';
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(\AG_COMPANY_TEAM_LOGGING, $ex);

                $formValues['new_file_url'] = '';
            }
        } else {
            $formValues['new_file_url'] = '';
        }

        if ($_POST['memberid'] ?? 0) {
            $id = $this->sanitizeInput($_POST['memberid']);
            $id = intval($id, 10);
            $formValues['id'] = absint($id);
        }

        if ($_POST['last_name'] ?? 0) {
            $last_name = $this->sanitizeInput($_POST['last_name']);
            $formValues['last_name'] = $last_name;
        } else {
            $formValues['last_name'] = '';
        }

        if ($_POST['first_name'] ?? 0) {
            $first_name = $this->sanitizeInput($_POST['first_name']);
            $formValues['first_name'] = $first_name;
        } else {
            $formValues['first_name'] = '';
        }

        if ($_POST['phone'] ?? 0) {
            $phone = $this->sanitizeInput($_POST['phone']);
            $formValues['phone'] = $phone;
        } else {
            $formValues['phone'] = '';
        }

        if ($_POST['email'] ?? 0) {
            $email = $this->sanitizeInput($_POST['email']);
            $formValues['email'] = $email;
        } else {
            $formValues['email'] = '';
        }

        if ($_POST['position'] ?? 0) {
            $position = $this->sanitizeInput($_POST['position']);
            $formValues['position'] = $position;
        } else {
            $formValues['position'] = '';
        }

        if ($_POST['department'] ?? 0) {
            $department = $this->sanitizeInput($_POST['department']);
            $formValues['department'] = $department;
        } else {
            $formValues['department'] = '';
        }

        if ($_POST['works_since'] ?? 0) {
            $works_since = $this->sanitizeInput($_POST['works_since']);
            $formValues['works_since'] = $works_since;
        } else {
            $formValues['works_since'] = date('Y-m-d', time());
        }


        return $formValues;
    }

    /**
     * Sanitizes input values
     * strips tags, more sanitization needed!
     * @return string
     */
    public function sanitizeInput(string $input, string $type = 'string'): string
    {
        // debug log and log to file
        $this->logger(\AG_COMPANY_TEAM_DEBUG, \AG_COMPANY_TEAM_LOGGING);

        return wp_strip_all_tags(trim($input));
    }



    /**
     * Add profile photo, save file in media folder
     * @return string the image url
     */
    public function addProfilePhoto(&$profilepicture): string
    {
        // debug log and log to file
        $this->logger(\AG_COMPANY_TEAM_DEBUG, \AG_COMPANY_TEAM_LOGGING);


        // upload profile image
        // @see
        // https://rudrastyh.com/wordpress/how-to-add-images-to-media-library-from-uploaded-files-programmatically.html
        // wp media folder
        $wordpress_upload_dir = wp_upload_dir();
        // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05,
        // for multisite works good as well
        // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually
        // we do not need it, just to show the link to file
        $i = 1; // number of tries when the file with the same name is already exists


        // store file object
        // $profilepicture = $_FILES['profilepicture'];

        // new path for the image in media folder
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'];
        $new_file_url = $wordpress_upload_dir['url'] . '/' . $profilepicture['name'];

        if (empty($profilepicture)) {
            wp_die('File is not selected.');
        }

        if ($profilepicture['error']) {
            wp_die($profilepicture['error']);
        }

        if ($profilepicture['size'] > wp_max_upload_size()) {
            wp_die('It is too large than expected.');
        }


        // get mime type
        $new_file_mime = mime_content_type($profilepicture['tmp_name']);

        if (!in_array($new_file_mime, get_allowed_mime_types())) {
            wp_die('WordPress doesn\'t allow this type of uploads.');
        }

        // if file exits with that name
        while (file_exists($new_file_path)) {
            $i++;
            $new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'] . '_' . $i;
            $new_file_url = $wordpress_upload_dir['url'] . '/' . $profilepicture['name'] . '_' . $i;
        }

        // looks like everything is OK, move file from temp to media folder, use path
        if (move_uploaded_file($profilepicture['tmp_name'], $new_file_path)) {
            // Insert an attachment
            $upload_id = wp_insert_attachment(array(
                'guid'           => $new_file_url, // use the url!
                'post_mime_type' => $new_file_mime,
                'post_title'     => preg_replace('/\.[^.]+$/', '', $profilepicture['name']),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ), $new_file_url);
        }

        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once(\ABSPATH . 'wp-admin/includes/image.php');

        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));

        // Show the uploaded file in browser, not needed
        // wp_redirect($wordpress_upload_dir['url'] . '/' . basename($new_file_path));

        // return image url to store it in db table
        return $new_file_url;
    }
}
