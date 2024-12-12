<?php

add_action('admin_menu', 'cnw_add_admin_menu');
add_action('admin_init', 'cnw_settings_init');

function cnw_enqueue_media_uploader() {
    if (isset($_GET['page']) && $_GET['page'] === 'cnw-settings') {
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
        'dashicons-admin-generic'
    );
}

function cnw_options_page() {
    ?>
    <h1>Cloud Nine Web Tools</h1>

    <div class="cnw-tabs">
        <button id="tab1" onclick="setTab(1)">General</button>
        <button id="tab2" onclick="setTab(2)">Security</button>
        <button id="tab3" onclick="setTab(3)">Admin UI</button>
        <button id="tab4" onclick="setTab(4)">Performance</button>
        <button id="tab5" onclick="setTab(5)">SEO</button>
        <button id="tab6" onclick="setTab(6)">Users</button>
        <button id="tab7" onclick="setTab(7)">Forms</button>
        <button id="tab8" onclick="setTab(8)">Notification</button>
    </div>

    <script>
        function setTab(tabId) {
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            params.set('tab', tabId);
            const newUrl = currentUrl.pathname + '?' + params.toString();
            window.location.href = newUrl;
        }

        function setActiveTab() {
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            const tabId = params.get('tab') || 1;

            document.querySelectorAll('.cnw-tabs button').forEach(button => {
                button.classList.remove('active');
            });

            const activeButton = document.getElementById('tab' + tabId);
            if (activeButton) {
                activeButton.classList.add('active');
            }

            document.querySelectorAll('.cnw-tab-contents > table').forEach((table, index) => {
                table.style.display = 'none';
            });

            const activeContent = document.querySelector(`.cnw-tab-contents > table:nth-child(${tabId})`);
            if (activeContent) {
                activeContent.style.display = 'block';
            }
        }

        // Function to toggle the display of the custom logo field
        function toggleCustomLogoField() {
            const logoField = document.getElementById('cnw_custom_logo_field');
            const checkbox = document.getElementById('admin_ui_settings_cnw-customize-admin-logo');
            if (checkbox && checkbox.checked) {
                logoField.style.display = 'block';
            } else {
                logoField.style.display = 'none';
            }
        }

        // Function to toggle the display of the plausible URL field
        function togglePlausibleUrlField() {
            const plausibleUrlField = document.getElementById('cnw_plausible_url_field');
            const checkbox = document.getElementById('seo_settings_cnw-plausible');
            if (checkbox && checkbox.checked) {
                plausibleUrlField.style.display = 'block';
            } else {
                plausibleUrlField.style.display = 'none';
            }
        }

        // Attach event listener to toggle the fields based on checkbox state
        window.onload = function() {
            setActiveTab();
            toggleCustomLogoField();
            togglePlausibleUrlField();
            const adminUiCheckbox = document.getElementById('admin_ui_settings_cnw-customize-admin-logo');
            if (adminUiCheckbox) {
                adminUiCheckbox.addEventListener('change', toggleCustomLogoField);
            }
            const seoCheckbox = document.getElementById('seo_settings_cnw-plausible');
            if (seoCheckbox) {
                seoCheckbox.addEventListener('change', togglePlausibleUrlField);
            }
        };
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

        .cnw-tabs button {
            border: 1px solid #ddd;
            padding: 10px;
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .cnw-tabs button.active {
            color: #fff;
            background-color: #f04449;
            border-color: #f04449;
        }

        .cnw-tab-contents > table {
            display: none;
        }

        @media(min-width: 1000px){
            .cnw-tab-contents {
                padding-right: 20px;
            }
        }

        /* Checkbox styles */
        .cnw-tab-contents input[type=checkbox] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .cnw-tab-contents label {
            position: relative;
            display: inline-block;
            padding-left: 40px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .cnw-tab-contents label::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 20px;
            background-color: #e5e5e5;
            border-radius: 20px;
            transition: background-color 0.4s;
        }

        .cnw-tab-contents label::after {
            content: "";
            position: absolute;
            left: 2px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.4s, background-color 0.4s;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
        }


        .cnw-tab-contents input[type=checkbox]:checked + label::before {
            background-color: #ff004d;
        }
        
        .cnw-tab-contents input[type=checkbox]:checked + label::after {
            transform: translate(14px, -50%);
            background-color: #fff;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
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

function cnw_upload_field_render($args) {
    $options = get_option('cnw_settings');
    $logo_url = isset($options[$args['id']]) ? esc_url($options[$args['id']]) : '';
    $display = isset($options['admin_ui_settings']['cnw-customize-admin-logo']) ? 'block' : 'none';
    ?>
    <div id="cnw_custom_logo_field" style="display: <?php echo $display; ?>;">
        <input type="text" id="<?php echo esc_attr($args['id']); ?>" name="cnw_settings[<?php echo esc_attr($args['id']); ?>]" value="<?php echo $logo_url; ?>" />
        <button class="button cnw-upload-button">Upload Logo</button>
    </div>
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

function cnw_text_field_render($args) {
    $options = get_option('cnw_settings');
    $value = isset($options[$args['id']]) ? esc_attr($options[$args['id']]) : '';
    $display = isset($options['seo_settings']['cnw-plausible']) ? 'block' : 'none';
    ?>
    <div id="cnw_plausible_url_field" style="display: <?php echo $display; ?>;">
        <input type='text' name='cnw_settings[<?php echo esc_attr($args['id']); ?>]' value='<?php echo $value; ?>' placeholder='Plausible Analytics URL' />
    </div>
    <?php
}

function cnw_settings_init() {
    register_setting('cnw_settings', 'cnw_settings');

    add_settings_section('cnw_general_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('general_settings', 'General Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_general_section', [
        'id' => 'general_settings',
        'choices' => [
            'cnw-pages' => 'Add Pages Link to Admin Bar',
            'cnw-ignoreupdates' => 'Enable Ignore Plugin Updates Toggles',
            'cnw-disable-autoupdates' => 'Disable Auto Updates',
            'cnw-disablegutenberg' => 'Disable Gutenberg on all post types, except blog posts',
            'cnw-disablegutenberg-everywhere' => 'Disable Gutenberg Everywhere',
            'cnw-disable-gutenberg-fullscreen' => 'Disable Gutenberg Fullscreen Editor',
            'cnw-duplicate' => 'Add duplicate button to post table',
            'cnw-system-stats' => 'Display System Stats Widget',
            'cnw-site-health' => 'Disable WP Site Health Widget'
        ]
    ]);

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
    add_settings_field('cnw_custom_logo', '', 'cnw_upload_field_render', 'cnw-settings', 'cnw_admin_ui_section', [
        'id' => 'cnw_custom_logo'
    ]);


    // Performance Section
    add_settings_section('cnw_performance_section', '', 'cnw_section_callback', 'cnw-settings');
    add_settings_field('performance_settings', 'Performance Settings', 'cnw_checkbox_field_render', 'cnw-settings', 'cnw_performance_section', ['id' => 'performance_settings', 'choices' => [
        'cnw-quickpurge' => 'Enable LiteSpeed Quick Purge Link',
        'cnw-disableauthorarchives' => 'Disable Author Archives',
        'cnw-disablecompression' => 'Disable Image Compression in WP'
    ]]);

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
    add_settings_field('plausible_url', '', 'cnw_text_field_render', 'cnw-settings', 'cnw_seo_section', [
        'id' => 'plausible_url'
    ]);

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
    // Optionally add text for the section
}

function cnw_checkbox_field_render($args) {
    $options = get_option('cnw_settings');
    $choices = $args['choices'];
    foreach ($choices as $key => $label) {
        $checked = isset($options[$args['id']][$key]) ? 'checked' : '';
        echo "<div style='margin-bottom: 10px;'>
                <input type='checkbox' id='{$args['id']}_$key' name='cnw_settings[{$args['id']}][$key]' value='1' $checked>
                <label for='{$args['id']}_$key'>$label</label>
              </div>";
    }
}


?>
