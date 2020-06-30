<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Alink_Tap
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 */
?>
<?php
//$sample = 'wordpress->http://wordpress.org/
//
//google->http://www.google.com/ _blank
//
//kb linker->http://adambrown.info/b/widgets/kb-linker/
//
//knuckleheads->http://www.house.gov/';

$message_updated = '<div id="message" class="updated fade"><p><strong>Alink Tap options updated.</strong> <a href="'.get_bloginfo('url').'">View site &raquo;</a></p></div>';

include_once( dirname(__FILE__).'/../includes/remote-linked.php' );

?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div class="card">

        <p>Alink Tap will link phrases you specify to sites you specify. For example, you could make it so that whenever "TodoApuestas" occurs in a post it is automatically linked to todoapuestas.org.</p>

        <form method="post" action="<?php echo admin_url( 'options-general.php?page=' . Alink_Tap::get_instance()->get_plugin_slug() ); ?>">
            <p>
                <label for="domain"><?php _e('Website domain name (traker)', Alink_Tap::get_instance()->get_plugin_slug()) ?></label>
                <input type="text" id="domain" name="alink_tap_domain" value="<?php echo $domain ?>">
            </p>
            <p>
                <label for="tracked_web_category"><?php _e('Tracked web category', Alink_Tap::get_instance()->get_plugin_slug()) ?></label>
                <select id="tracked_web_category" name="alink_tap_tracked_web_category">
                    <option <?php if($tracked_web_category == 'apuestas'): ?>selected="selected"<?php endif;?> value="apuestas"><?php _e('Apuestas', Alink_Tap::get_instance()->get_plugin_slug()) ?></option>
                    <option <?php if($tracked_web_category == 'casinos'): ?>selected="selected"<?php endif;?> value="casinos"><?php _e('Casinos', Alink_Tap::get_instance()->get_plugin_slug()) ?></option>
                    <option <?php if($tracked_web_category == 'poker'): ?>selected="selected"<?php endif;?> value="poker"><?php _e('Poker', Alink_Tap::get_instance()->get_plugin_slug()) ?></option>
                    <option <?php if($tracked_web_category == 'bingo'): ?>selected="selected"<?php endif;?> value="bingo"><?php _e('Bingo', Alink_Tap::get_instance()->get_plugin_slug()) ?></option>
                </select>
            </p>
            <p><input type="checkbox" <?php echo $remote_plurals ? 'checked="checked"' : ''; ?> name="alink_tap_remote_plurals" value="<?php echo $remote_plurals; ?>" /> <?php _e('Also link the keyword if it ends in <i>s</i> (i.e. plurals in certain languages)', Alink_Tap::get_instance()->get_plugin_slug()) ?></p>

            <p class="submit"><input type="submit" value="Submit &raquo;" /></p>

        </form>

        <p>Considerations:</p>

        <ul>

            <li>URLs should be valid (i.e. begin with http://)</li>

            <li>The same URL can appear on more than one line (i.e. with more than one keyword).</li>

            <li>Because a word can only link to one site, a keyword should not appear on more than one line. If it does, only the last instance of the keyword will be matched to its URL.</li>

            <li>If one of your keywords is a substring of the other--e.g. "download wordpress" and "wordpress"--then you should list the shorter one later than the first one.</li>

            <li>Keywords are case-insensitive (e.g. "wordpress" is the same as "WoRdPrEsS").</li>

            <li>Spaces count, so "wordpress" is not the same as "wordpress ".</li>

            <li>Keywords will be linked only if they occur in your post as a word (or phrase), not as a partial word. So if one of your keywords is "a" (for some strange reason), it will be linked only when it occurs as the word "a"--when the letter "a" occurs within a word, it will not be linked.</li>

            <li>You can use any valid target attribute, not just "_blank"--see <a href="http://www.w3schools.com/tags/tag_a.asp">W3C</a> for a list of valid targets.</li>

        </ul>

    </div>

<?php if($errors) :?>
    <div class="card widefat">
        <h3>Errors detected</h3>
        <ul>
        <?php foreach($errors as $error): ?>
            <li><?php echo $error;  ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <?php if (isset($bookies) && count($bookies)): ?>
    <div class="card widefat" style="max-width: 100%;">
        <h3>Bookies</h3>
        <table>
            <thead>
                <tr>
                    <td>&nbsp;</td>
                    <td>Name</td>
                    <td>URL / URL(es)</td>
                    <td>License</td>
                </tr>
            </thead>
            <tbody>
            <?php foreach($bookies as $bookie): ?>
                <tr>
                    <td><img class="img-resposive" src="<?php echo $bookie['logo']; ?>" ></td>
                    <td><?php echo $bookie['nombre']; ?></td>
                    <td><?php echo $bookie['url']; ?> <br> <?php echo $bookie['urles']; ?> </td>
                    <td><?php echo $bookie['licencia'] ? 'YES' : 'NO'; ?> </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>