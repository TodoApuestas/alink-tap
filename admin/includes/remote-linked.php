<?php
/**
 *
 * @package   Alink_Tap
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 */

$domain = $tracked_web_category = $remote_plurals = '';

if(isset($_POST['alink_tap_domain'])){
    $domain = $_POST['alink_tap_domain'];
    $tracked_web_category = $_POST['alink_tap_tracked_web_category'];
    $remote_plurals = $_POST['alink_tap_remote_plurals'];

    $old_options = get_option('alink_tap_linker_remote_info', Alink_Tap::get_instance()->get_default_options());
    $options = array('domain' => $domain, 'tracked_web_category' => $tracked_web_category, 'plurals' => $remote_plurals);
    update_option('alink_tap_linker_remote_info', array_merge($old_options, $options));

    print $message_updated;
}else{
    $option = get_option('alink_tap_linker_remote_info', Alink_Tap::get_instance()->get_default_options());

    $domain = $option['domain'];
    $tracked_web_category = esc_url($option['tracked_web_category']);
    $remote_plurals = $option['plurals'];
}