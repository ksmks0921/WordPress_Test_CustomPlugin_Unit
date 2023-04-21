
# Skill Test of WordPress / Alexander Mamaril / +1 2092435277

I have created a custom plugin. The requirments are as below.

[Requirements](https://docs.google.com/document/d/1wrr4Eu0S9OkeO8Lq0nKVcvDS5cJmHCwKp6apgDwjNjc/edit#)


## Requirements

- PHP 8.2.0
- WordPress 6.2

## Installation

1. Upload the folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Install ACF plugin for custom field create from [here](https://wordpress.org/plugins/advanced-custom-fields/)
4. Create one Field group with 5 custom fields
    - asset_id
    - asset_id
    - building_id
    - floor_id
    - floor_plan_id
    - area

    Add a 'rule group': "post type is equal to 'unit'".
    (Custom post type 'unit' would be created when install this plugin. You can see it in admin page wiht the name of 'units')
    
5. To trigger an API call to create 'unit' records, click "Get Custom Posts" button from 'My Custom Plugin'
6. To show all units, you can use shortcode: [unit-list] in any page or post.