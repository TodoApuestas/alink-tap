<?php
/**
 *
 * @package   Alink_Tap
 * @author    Alain Sanchez <asanchezg@inetzwerk.com>
 * @license   GPL-2.0+
 * @link      http://www.inetzwerk.com
 * @copyright 2014 Alain Sanchez
 */

$domain = $url_sync_link = $url_get_country_from_ip = $remote_plurals = '';

if(isset($_POST['alink_tap_domain'])){
    $domain = $_POST['alink_tap_domain'];
    $url_sync_link = esc_url($_POST['alink_tap_url_sync_link']);
    $url_get_country_from_ip = esc_url($_POST['alink_tap_url_get_country_from_ip']);
    $remote_plurals = $_POST['alink_tap_remote_plurals'];

    $option = array('domain' => $domain, 'url_sync_link' => $url_sync_link, 'url_get_country_from_ip' => $url_get_country_from_ip, 'plurals' => $remote_plurals);
    update_option('alink_tap_linker_remote_info', $option);

    print $message_updated;
}else{
    $option = get_option('alink_tap_linker_remote_info', Alink_Tap::get_instance()->get_default_options());

    $domain = $option['domain'];
    $url_sync_link = esc_url($option['url_sync_link']);
    $url_get_country_from_ip = esc_url($option['url_get_country_from_ip']);
    $remote_plurals = $option['plurals'];
}