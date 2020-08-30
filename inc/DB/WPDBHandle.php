<?php

namespace AG\CompanyTeam\DB;

class WPDBHandle
{
    use \AG\CompanyTeam\Input\FormInput;

    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    protected function list()
    {
    }

    protected function insert(string $tableName, array $sanitizedData): bool
    {
        global $wpdb;

        // prepare query, update table
        $res = $wpdb->insert(
            $wpdb->prefix . $tableName,
            array(
                'profile_photo' => $sanitizedData['new_file_url'],
                'last_name'     => $sanitizedData['last_name'],
                'first_name'    => $sanitizedData['first_name'],
                'phone'         => $sanitizedData['phone'],
                'email'         => $sanitizedData['email'],
                'position'      => $sanitizedData['position'],
                'department'    => $sanitizedData['department'],
                'works_since'   => $sanitizedData['works_since']
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s') // data format
        );

        return $res;
    }
    protected function update(string $tableName, array $sanitizedData, bool $profilePhoto = false): bool
    {
        global $wpdb;
        if ($profilePhoto) {
            // prepare query, update table
            $res = $wpdb->update(
                $wpdb->prefix . $tableName,
                array(
                    'profile_photo' => $sanitizedData['new_file_url'],
                    'last_name'     => $sanitizedData['last_name'],
                    'first_name'    => $sanitizedData['first_name'],
                    'phone'         => $sanitizedData['phone'],
                    'email'         => $sanitizedData['email'],
                    'position'      => $sanitizedData['position'],
                    'department'    => $sanitizedData['department'],
                    'works_since'   => $sanitizedData['works_since']
                ),
                // where clause
                array('id'  => $sanitizedData['id']),
                // data format
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
                // where format
                array('%d')
            );
        } else {
            // prepare query, update table
            $res = $wpdb->update(
                $wpdb->prefix . $tableName,
                array(
                    'last_name'     => $sanitizedData['last_name'],
                    'first_name'    => $sanitizedData['first_name'],
                    'phone'         => $sanitizedData['phone'],
                    'email'         => $sanitizedData['email'],
                    'position'      => $sanitizedData['position'],
                    'department'    => $sanitizedData['department'],
                    'works_since'   => $sanitizedData['works_since']
                ),
                // where clause
                array('id'  => $sanitizedData['id']),
                // data format
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s'),
                // where format
                array('%d')
            );
        }
        return $res;
    }

    protected function delete(int $id): bool
    {
        global $wpdb;
        // prepare get statement protect against SQL inject attacks!
        $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "company_team WHERE id = %d", $id);

        // perform query
        $res = $wpdb->query($sql);

        return $res;
    }
}
