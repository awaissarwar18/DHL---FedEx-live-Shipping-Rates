<?php
/**
 * @package shippingCalculator
 */
/*
Plugin Name: DHL & Fedex Shipping Calculator
Description: Shipping Calculator will calculate shipping costs with respect to particular products according to their attributes ( e.g. source, weight, dimensions etc ).
Version: 1.0.0
Author: Bitsclan IT Solutions Private Limited
Author uri: https://bitsclan.com/
*/

if (!defined("WPINC")) {
    die;
}

if (!defined("WPSHIP_PLUGIN_DIR")) {
    define("WPSHIP_PLUGIN_DIR", plugin_dir_url(__FILE__));
}

require plugin_dir_path(__FILE__) . 'includes/functions.php';
require plugin_dir_path(__FILE__) . 'includes/wpShipScripts.php';
require plugin_dir_path(__FILE__) . 'includes/settings.php';
require plugin_dir_path(__FILE__) . 'includes/shipShortcode.php';