<?php
// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu()
{

    //create new top-level menu
    add_menu_page('DHL & Fedex shipping Calculator', 'Shipping Calculator', 'administrator', __FILE__, 'my_cool_plugin_settings_page', plugins_url('/images/icon.png', __FILE__));

    //call register settings function
    add_action('admin_init', 'register_my_cool_plugin_settings');
}


function register_my_cool_plugin_settings()
{
    //register our settings
//    DHL
    register_setting('my-cool-plugin-settings-group', 'dhl_active');
    register_setting('my-cool-plugin-settings-group', 'dhl_api');
    register_setting('my-cool-plugin-settings-group', 'dhl_password');
    register_setting('my-cool-plugin-settings-group', 'dhl_commission');
//    FEDEX
    register_setting('my-cool-plugin-settings-group', 'fedex_active');
    register_setting('my-cool-plugin-settings-group', 'fedex_production');
    register_setting('my-cool-plugin-settings-group', 'fedex_key');
    register_setting('my-cool-plugin-settings-group', 'fedex_password');
    register_setting('my-cool-plugin-settings-group', 'fedex_account');
    register_setting('my-cool-plugin-settings-group', 'fedex_meter');
    register_setting('my-cool-plugin-settings-group', 'fedex_commission');

    add_settings_section("wpship_label_settings_section","WPSHIP fields settings Short code: [wpshipShortCode]","WPSHIP_plugin_settings_section_cb","wpship-settings");

}

function my_cool_plugin_settings_page()
{ 
    ?>
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

    </style>
    <div id="exTab2" class="bil_tabs">
        <ul class="nav nav-tabs admin_nav_tabs">
            <li class="active"><a href="#1" data-toggle="tab"><?= __('DHL', 'shippingCalculator') ?></a></li>
            <li><a href="#2" data-toggle="tab">FedEx</a>
            </li>
        </ul>
        <div class="wrap admin_form_outer">
           <!--  <h1>API Configuration</h1> -->


            <form method="post" action="options.php">
                <?php settings_fields('my-cool-plugin-settings-group'); ?>
                <?php do_settings_sections('my-cool-plugin-settings-group'); ?>
                <div class="tab-content">
                    <div class="tab-pane active pt-1" id="1">
                        <h4>API Configuration of DHL</h4>
                        <hr>

                        <!--DHL -->
                        <table class="form-table admin_table">
                            <tr valign="top">
                                <th scope="row">Active</th>
                                <td class="mb-3">
                                    <label class="switch">
                                        <input type="checkbox" required
                                               name="dhl_active" <?php echo (esc_attr(get_option('dhl_active'))) ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">API Site ID</th>
                                <td><input type="text" class="admin_input_fields" name="dhl_api" required placeholder="API SiteID"
                                           value="<?php echo esc_attr(get_option('dhl_api')); ?>"/></td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Password</th>
                                <td><input type="password" class="admin_input_fields" name="dhl_password" required placeholder="Password"
                                           value="<?php echo esc_attr(get_option('dhl_password')); ?>"/></td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Commission (%)</th>
                                <td><input type="number" class="admin_input_fields" placeholder="0 - 100" step="0.01" min="0" max="100" class="edit-items"  name="dhl_commission" 
                                    value="<?php echo esc_attr(get_option('dhl_commission')); ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-left: 0;">
                                    <code id="p1"> [wpshipShortCode] </code><a id="btnn" onclick="copyToClipboard('#p1')">Copy </a> <span id="text_shortcode"> Use this shortcode to display DHL & FeDex Calculator for Shipping.</span>
                                </td>
                            </tr>
                            <script>
                                function copyToClipboard(element) {
                                  var $temp = $("<input>");
                                  $("body").append($temp);
                                  $temp.val($(element).text()).select();
                                  document.execCommand("copy");
                                  $temp.remove();
                                }
                            </script>
                        </table>
                        <script>
                             document.getElementsByClassName('edit-items')[0].oninput = function () {
                                var max = parseInt(this.max);

                                if (parseInt(this.value) > max) {
                                    this.value = max; 
                                }
                            }
                        </script>
                    </div>
                    <!--FedEx  -->

                    <div class="tab-pane pt-1" id="2">
                        <h4>API Configuration of Fedex</h4>
                        <hr>
                        <!-- Checked switch -->


                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Active</th>
                                <td class="mb-3">
                                    <label class="switch">
                                        <input type="checkbox" required
                                               name="fedex_active" <?php echo (esc_attr(get_option('fedex_active'))) ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Production Mode</th>
                                <td class="mb-3">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="fedex_production" <?php echo (esc_attr(get_option('fedex_production'))) ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Key</th>
                                <td><input type="text" class="admin_input_fields" name="fedex_key" placeholder="Key" required
                                    value="<?php echo esc_attr(get_option('fedex_key')); ?>"/></td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Password</th>
                                <td><input type="password" class="admin_input_fields" name="fedex_password" placeholder="Password" required
                                    value="<?php echo esc_attr(get_option('fedex_password')); ?>"/></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Account Number</th>
                                <td><input type="number" class="admin_input_fields" name="fedex_account"  placeholder="Account Number" required
                                    value="<?php echo esc_attr(get_option('fedex_account')); ?>"/></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Meter Number</th>
                                <td><input type="number" class="admin_input_fields" name="fedex_meter"  placeholder="Meter Number" required
                                    value="<?php echo esc_attr(get_option('fedex_meter')); ?>"/></td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Commission (%)</th>
                                <td><input type="number" class="admin_input_fields" placeholder="0 - 100" step="0.01" min="0" max="100" class="edit-items1" name="fedex_commission"
                                    value="<?php echo esc_attr(get_option('fedex_commission')); ?>"/></td>
                            </tr>
                        </table>
                        <script>
                             document.getElementsByClassName('edit-items1')[0].oninput = function () {
                                var max = parseInt(this.max);

                                if (parseInt(this.value) > max) {
                                    this.value = max; 
                                }
                            }
                        </script>
                    </div>
                </div>

               <!--  <?php //submit_button(); ?> -->
               <input type="submit" name="Save" value="Save" class="save_button">

            </form>
        </div>
    </div>
<?php } ?>




