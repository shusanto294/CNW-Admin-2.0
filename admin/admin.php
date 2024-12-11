<?php

add_action('admin_menu', 'cnw_add_admin_menu');
add_action('admin_init', 'cnw_settings_init');


function cnw_enqueue_media_uploader() {
    // Check if we are on the cnw-settings admin page
    if (isset($_GET['page']) && $_GET['page'] === 'cnw-settings') {
        // Enqueue the WordPress media library scripts
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'cnw_enqueue_media_uploader');


function cnw_add_admin_menu() {
    add_menu_page(
        'Cloud Nine Web',
        'Cloud Nine Web',
        'manage_options',
        'cnw-settings',
        'cnw_options_page',
        'https://gocloudnine.b-cdn.net/images/Menu-Icon.svg'
    );
}

function cnw_options_page() {
    ?>
    <h1>Cloud Nine Web Tools</h1>

    <div class="cnw-tabs">
        <a href="#" id="tab1" onclick="setTab(1)">General</a>
        <a href="#" id="tab2" onclick="setTab(2)">Security</a>
        <a href="#" id="tab3" onclick="setTab(3)">Admin UI</a>
        <a href="#" id="tab4" onclick="setTab(4)">Performance</a>
        <a href="#" id="tab5" onclick="setTab(5)">SEO</a>
        <a href="#" id="tab6" onclick="setTab(6)">Users</a>
        <a href="#" id="tab7" onclick="setTab(7)">Forms</a>
        <a href="#" id="tab8" onclick="setTab(8)">Notification</a>
    </div>

    <script>
        function setTab(tabId) {
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);

            // Set or update the 'tab' parameter
            params.set('tab', tabId);

            // Construct the new URL with updated parameters
            const newUrl = currentUrl.pathname + '?' + params.toString();

            // Navigate to the new URL
            window.location.href = newUrl;
        }

        // Function to set the active class based on the current 'tab' parameter
        function setActiveTab() {
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            const tabId = params.get('tab') || 1; // Default to 1 if no tab parameter is set

            // Remove active class from all links
            document.querySelectorAll('.cnw-tabs a').forEach(link => {
                link.classList.remove('active');
            });

            // Add active class to the current tab link
            const activeLink = document.getElementById('tab' + tabId);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            // Hide all tab contents
            document.querySelectorAll('.cnw-tab-contents > table').forEach((table, index) => {
                table.style.display = 'none';
            });

            // Show the current tab content
            const activeContent = document.querySelector(`.cnw-tab-contents > table:nth-child(${tabId})`);
            if (activeContent) {
                activeContent.style.display = 'block';
            }
        }

        // Call setActiveTab on page load
        window.onload = setActiveTab;
    </script>

    <style>

    .cnw-tabs {
        display: flex;
        flex-wrap: wrap;
        column-gap: 10px;
        row-gap: 10px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .cnw-tabs a {
        border: 1px solid #ddd;
        padding: 10px;
        color: black;
        text-decoration: none;
    }

    .cnw-tabs a.active {
        color: #fff;
        background-color: #f04449;
    }

    .cnw-tab-contents > table {
        display: none; /* Initially hide all tables */
    }

    @media(min-width: 1000px){
        .cnw-tab-contents {
            padding-right: 20px;
        }
    }
    </style>

    <form action='options.php' method='post'>
        <?php
        settings_fields('cnw_settings');
        echo '<div class="cnw-tab-contents">';
        do_settings_sections('cnw-settings');
        echo '</div>';
        submit_button();
        ?>
    </form>
    <?php
}

function cnw_settings_init() {
    register_setting('cnw_settings', 'cnw_settings');

    // General Section
    add_settings_section('cnw_general_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('general_settings', 'General Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_general_section', ['id' => 'general_settings', 'choices' => [
        'cnw-pages' => 'Add Pages Link to Admin Bar',
        'cnw-ignoreupdates' => 'Enable Ignore Plugin Updates Toggles',
        'cnw-disable-autoupdates' => 'Disable Auto Updates',
        'cnw-disablegutenberg' => 'Disable Gutenberg on all post types, except blog posts',
        'cnw-disablegutenberg-everywhere' => 'Disable Gutenberg Everywhere',
        'cnw-disable-gutenberg-fullscreen' => 'Disable Gutenberg Fullscreen Editor',
        'cnw-duplicate' => 'Add duplicate button to post table',
        'cnw-system-stats' => 'Display System Stats Widget',
        'cnw-site-health' => 'Disable WP Site Health Widget'
    ]]);

    // Security Section
    add_settings_section('cnw_security_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('security_settings', 'Security Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_security_section', ['id' => 'security_settings', 'choices' => [
        'cnw-hidewp' => 'Hide WordPress Version',
        'cnw-comments' => 'Disable Comments',
        'cnw-xmlrpc' => 'Disable XML-RPC',
        'cnw-tpeditors' => 'Disable Theme and Plugin file editors',
        'cnw-blacklist' => 'Auto Update WordPress Comment Blacklist',
        'cnw-userenumeration' => 'Block User Enumeration',
        'cnw-limit-login' => 'Limit Login Attempts'
    ]]);

    // Admin UI Section
    add_settings_section('cnw_admin_ui_section', '', 'cnw_section_callback', 'cnw-settings');

    // Add checkbox fields
    add_settings_field('admin_ui_settings', 'Admin UI Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_admin_ui_section', [
        'id' => 'admin_ui_settings',
        'choices' => [
            'cnw-dashboard' => 'CNW Dashboard',
            'cnw-login' => 'CNW Login Screen',
            'cnw-support' => 'Enable CNW Support Widget',
            'cnw-removedashwidget' => 'Remove WordPress Dashboard Widgets',
            'cnw-howdy' => 'Rename WordPress Howdy',
            'cnw-removewplogo' => 'Remove WordPress logo/link from Admin Bar',
            'cnw-cleanup' => 'CNW Dashboard Clean-Up',
            'cnw-customize-admin-logo' => 'Customize the Admin Menu logo' // New field
        ]
    ]);

    // Add logo upload field
    add_settings_field('cnw_custom_logo', 'Custom Logo', 'cnw_upload_field_render', 'cnw-settings', 'cnw_admin_ui_section', [
        'id' => 'cnw_custom_logo'
    ]);


    // Function to render the upload field
    function cnw_upload_field_render($args) {
        $options = get_option('cnw_settings');
        $logo_url = isset($options[$args['id']]) ? esc_url($options[$args['id']]) : '';
        ?>
        <input type="text" id="<?php echo esc_attr($args['id']); ?>" name="cnw_settings[<?php echo esc_attr($args['id']); ?>]" value="<?php echo $logo_url; ?>" />
        <button class="button cnw-upload-button">Upload Logo</button>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('.cnw-upload-button').click(function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var custom_uploader = wp.media({
                        title: 'Select Logo',
                        button: {
                            text: 'Use this logo'
                        },
                        multiple: false
                    }).on('select', function() {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        button.prev('input').val(attachment.url);
                    }).open();
                });
            });
        </script>
        <?php
    }

    // Performance Section
    add_settings_section('cnw_performance_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('performance_settings', 'Performance Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_performance_section', ['id' => 'performance_settings', 'choices' => [
        'cnw-quickpurge' => 'Enable LiteSpeed Quick Purge Link',
        'cnw-disableauthorarchives' => 'Disable Author Archives',
        'cnw-disablecompression' => 'Disable Image Compression in WP'
    ]]);

    // // SEO Section
    // add_settings_section('cnw_seo_section', '', 'cnw_section_callback', 'cnw-settings');
    // add_settings_field('seo_settings', 'SEO Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_seo_section', ['id' => 'seo_settings', 'choices' => [
    //     'cnw-disable-attachments' => 'Disable Media Attachment Pages',
    //     'cnw-blur-alt-images' => 'Blur Images Without an Alt Tag',
    //     'cnw-plausible' => 'Enable CNW Plausible Analytics'
    // ]]);

    // SEO Section
    add_settings_section('cnw_seo_section', '', 'cnw_section_callback', 'cnw-settings');

    // SEO Settings Checkboxes
    add_settings_field('seo_settings', 'SEO Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_seo_section', [
        'id' => 'seo_settings',
        'choices' => [
            'cnw-disable-attachments' => 'Disable Media Attachment Pages',
            'cnw-blur-alt-images' => 'Blur Images Without an Alt Tag',
            'cnw-plausible' => 'Enable CNW Plausible Analytics'
        ]
    ]);

    // Plausible URL Text Input
    add_settings_field('plausible_url', 'Plausible Analytics URL', 'cnw_text_field_render', 'cnw-settings', 'cnw_seo_section', [
        'id' => 'plausible_url'
    ]);

    function cnw_text_field_render($args) {
        $options = get_option('cnw_settings');
        $value = isset($options[$args['id']]) ? esc_attr($options[$args['id']]) : '';
        echo "<input type='text' name='cnw_settings[{$args['id']}]' value='$value' />";
    }

    // Users Section
    add_settings_section('cnw_users_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('user_settings', 'User Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_users_section', ['id' => 'user_settings', 'choices' => [
        'cnw-removeab' => 'Remove Admin Bar from all users, except Admins',
        'cnw-email-users' => 'Enable Email Users by Role',
        'cnw-role-management' => 'Hide Menu Items by Role',
        'cnw-member-role' => 'Add Member User Role',
        'cnw-hide-roles' => 'Hide Unnecessary User Roles'
    ]]);

    // Forms Section
    add_settings_section('cnw_forms_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('form_settings', 'Form Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_forms_section', ['id' => 'form_settings', 'choices' => [
        'cnw-gf-blacklist' => 'Gravity Forms Validate Against Comment Blacklist',
        'cnw-force-gf-honeypot' => 'Force Gravity Forms Honeypot Enabled',
        'cnw-cache-buster' => 'Enable GravityWiz Cache Buster for Gravity Forms'
    ]]);

    // Notifications Section
    add_settings_section('cnw_notifications_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('notifications_settings', 'Notification Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_notifications_section', ['id' => 'notifications_settings', 'choices' => [
        'cnw-admin-email-check' => 'Disable Admin Email Confirmations',
        'cnw-update-emails' => 'Disable Update Emails',
        'cnw-newuser-emails' => 'Disable New User Emails',
        'cnw-passwordreset-emails' => 'Disable Password Reset Emails'
    ]]);

    // Email Section
    // add_settings_section('cnw_email_section', 'Email Settings', 'cnw_section_callback', 'cnw-settings');
    // add_settings_field('field_cnw_email_signature', 'Email Signature', 'cnw_wysiwyg_field_render', 'cnw-settings', 'cnw_email_section', ['id' => 'field_cnw_email_signature']);
}

function cnw_section_callback() {
    //echo 'Configure the settings below:';
}

function cnw_checkbox_field_render($args) {
    $options = get_option('cnw_settings');
    $choices = $args['choices'];
    foreach ($choices as $key => $label) {
        $checked = isset($options[$args['id']][$key]) ? 'checked' : '';
        echo "<label><input type='checkbox' name='cnw_settings[{$args['id']}][$key]' value='1' $checked> $label</label><br>";
    }
}

function cnw_wysiwyg_field_render($args) {
    $options = get_option('cnw_settings');
    $content = isset($options[$args['id']]) ? $options[$args['id']] : '';
    wp_editor($content, $args['id'], ['textarea_name' => 'cnw_settings[' . $args['id'] . ']']);
}



?>
