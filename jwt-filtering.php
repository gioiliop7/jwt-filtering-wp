<?php
/*
Plugin Name: JWT Filtering
Description: Filter endpoints for the jwt-auth plugin from wordpress page
Version: 1.0.0
Text Domain: jwt-endpoints-filtering
Author: Giorgos Iliopoulos
*/

if (!defined('ABSPATH')) {
    exit;
}

function jwt_filtering_menu_page() {
    add_menu_page(
        'JWT Filtering',
        'JWT Filtering',
        'manage_options',
        'jwt-filtering',
        'jwt_filtering_settings_page',
        'dashicons-admin-generic',
        99
    );
}
add_action('admin_menu', 'jwt_filtering_menu_page');

function jwt_filtering_settings_page() {
    if (isset($_POST['jwt_filterin_endpoints'])) {
        $endpoints = explode("\n", sanitize_textarea_field($_POST['jwt_filterin_endpoints']));
        $whitelistedEndpoints = get_option('jwt_filterin_whitelisted_endpoints', array());

        // Merge the existing whitelisted endpoints with the new ones
        $whitelistedEndpoints = array_unique(array_merge($whitelistedEndpoints, $endpoints));

        // Check for removed endpoints
        $removedEndpoints = array_diff($whitelistedEndpoints, $endpoints);

        // Remove the endpoints that are no longer in the form
        $whitelistedEndpoints = array_diff($whitelistedEndpoints, $removedEndpoints);

        var_dump($whitelistedEndpoints);

        // Save the updated whitelist to the database
        update_option('jwt_filterin_whitelisted_endpoints', $whitelistedEndpoints);
    }

    $whitelistedEndpoints = get_option('jwt_filterin_whitelisted_endpoints', array());
    ?>
    <div class="wrap">
        <h1>JWT Filtering Settings</h1>
        <form method="post" style="display:flex; flex-direction:column; gap:2rem;">
            <label for="jwt_filterin_endpoints">Whitelisted Endpoints:</label>
            <textarea name="jwt_filterin_endpoints" id="jwt_filterin_endpoints" rows="5" cols="50"><?php echo implode("\n", $whitelistedEndpoints); ?></textarea>
            <p class="description">Enter one endpoint per line.</p>
            <?php submit_button('Save Endpoints'); ?>
        </form>
    </div>
    <?php
}

add_filter('jwt_auth_whitelist', function ($endpoints) {
    $whitelistedEndpoints = get_option('jwt_filterin_whitelisted_endpoints', array());

    return array_unique(array_merge($endpoints, $whitelistedEndpoints));
}, 10, 1);