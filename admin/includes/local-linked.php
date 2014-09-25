<?php
/**
 *
 * @package   Alink_Tap
 * @author    Alain Sanchez <asanchezg@inetzwerk.com>
 * @license   GPL-2.0+
 * @link      http://www.inetzwerk.com
 * @copyright 2014 Alain Sanchez
 */

$linker_text = $sample;

$local_plurals = 0;

if ( $_POST['alink_tap_linker_text'] ){

    $pairs = str_replace("\r", '', $_POST['alink_tap_linker_text']);

    $pairs = explode("\n", $pairs);

    foreach( $pairs as $pair ){

        $pair = trim( $pair ); // no leading or trailing spaces. Can mess with the "target" thing in function alink_linker()

        $pair = explode( "->", $pair );

        if ( ( '' != $pair[0] ) && ( '' != $pair[1] ) )

            $new[ $pair[0] ] = $pair[1];

    }

    $pairs = $new;	// contains the pairs as an array for use by the filter

    $linker_text = $_POST['alink_tap_linker_text'];	// contains the pairs as entered in the form for display below



    $local_plurals = ( 1 == $_POST['alink_tap_local_plurals'] ) ? 1 : 0;

    $option = array( 'pairs'=>$pairs, 'text'=>$linker_text, 'plurals'=>$local_plurals );	// store both versions of the option, pairs and text

    update_option( 'alink_tap_linker_local', $option );

    print $message_updated;

}else{

    $option = get_option('alink_tap_linker_local');

    if (is_array($option)){

        $pairs = $option['pairs'];
        $linker_text = $option['text'];
        $local_plurals = $option['plurals'];

    }

}