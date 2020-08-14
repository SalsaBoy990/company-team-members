# company-team-members
Company Team Members plugin for Wordpress

The plugin is under development.

The plugin handles the company team members in a custom table in WP database, CRUD functionality implemented. Internationalization (I18n) supported.

You can paste in the table or list of the workers using the shortcode supplied:

## Default usage:

- `[company_team]`

The default view is list, but you can change it with the `type` attribute:

- `[company_team type="table"]`, or
- `[company_team type="list"]`

You can also enable or disable specific fields to be shown:

`[company_team type="list" email="false" works_since="true"]`


The full list of available options (with defaults):

````
'type'              => 'list' / 'table',
'name'              => true,
'first_name_first'  => false,
'photo'             => true,
'phone'             => true,
'email'             => true,
'position'          => true,
'department'        => false,
'works_since'       => false,
````

The `first_name_first` attribute determines whether the first name comes before the last name, or the other way round. In some languages the last name (family name) is written before the first name.

TODO list:
- create a widget to be used in sidebars
- improve form validation in admin menu



                    


