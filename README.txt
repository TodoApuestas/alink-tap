=== Alink Tap ===
Contributors: mrbrazzi, todoapuestas
Donate link: http://todoapuestas.org/
Tags: link
Requires at least: 3.5.1
Tested up to: 4.2.2
Stable tag: 1.1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is a customization of KB Linker vTAP by Adam R. Brown to TodoApuestas.org. Looks for user-defined phrases in posts and automatically links them. Example: Link every occurrence of "TodoApuestas" to todoapuestas.org. It execute syncronizations task with TodoApuestas.org Server.

== Description ==

= IMPORTANT NOTE TO ANYBODY CONSIDERING ADDING THIS PLUGIN TO A WP-MU INSTALLATION: =

If you aren't sure whether you are using a WP-MU blog, then you aren't. Trust me. If this warning applies to you, then you will know it.

For WP-MU administrators: You should not use this plugin. Your users could use it to place (potentially malicious) javascript into their blogs.

This plugin is PERFECTLY SAFE for non-WP-MU blogs, so ignore this message if you're using regular wordpress (you probably are).

= Considerations =

* URLs should be valid (i.e. begin with http://)
* The same URL can appear on more than one line (i.e. with more than one keyword).
* Because a word can only link to one site, a keyword should not appear on more than one line. If it does, only the last instance of the keyword will be matched to its URL.
* If one of your keywords is a substring of the other--e.g. "download wordpress" and "wordpress"--then you should list the shorter one later than the first one.
* Keywords are case-insensitive (e.g. "wordpress" is the same as "WoRdPrEsS").
* Spaces count, so "wordpress" is not the same as "wordpress ".
* Keywords will be linked only if they occur in your post as a word (or phrase), not as a partial word. So if one of your keywords is "a" (for some strange reason), it will be linked only when it occurs as the word "a"--when the letter "a" occurs within a word, it will not be linked.
* You can use any valid target attribute, not just "_blank"--see <a href="http://www.w3schools.com/tags/tag_a.asp">W3C</a> for a list of valid targets.


= DATABASE STRUCTURE =

The options->alink_tap_linker_remote page will create a set of matching terms and URLs that gets stored as a list.

"alink_tap_linker_remote" is a serialized value as follows:

  array(
    'keyword1' => array(
        'name' => the keyword
        'url' => the original url
        'urles' => the url that serve the content in spanish
        'licencia' => 1, 0  if 1, we check if client's ip is from Spain and use the urles as link
    ),
    'keyword2' => array(
        'name' => the keyword
        'url' => the original url
        'urles' => the url that serve the content in spanish
        'licencia' => 1, 0  if 1, we check if client's ip is from Spain and use the urles as link
    ),
  )

== Installation ==

This section describes how to install the plugin and get it working.

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Alink Tap'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `alink-tap.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `alink-tap.zip`
2. Extract the `plugin-name` directory to your computer
3. Upload the `plugin-name` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==

Nothing for now


== Screenshots ==

Nothing for now


== Changelog ==

= 1.1.0.1 =
* Added some minor changes

= 1.1.0.0 =
* Added support for TAP Api REST's services through OAuth authentication/authorization

= 1.0.1.2 =
* Commented unnecessary actions execution

= 1.0.1.1 =
* Include some missing files in version 1.0.1

= 1.0.1 =
* Refactored method remote_sync of class Alink_Tap
* Refactored method active_remote_sync of class Alink_Tap
* Refactored method execute_linker of class Alink_Tap.
* Change the source result from plain text to json.

= 1.0 =
* Initial release.

== Upgrade Notice ==

Upgrade to lastest version 1.1.0.0 as soon as posible. See Changelog section for details


== Arbitrary section ==

Nothing for now

== Updates ==

The basic structure of this plugin was cloned from the [WordPress-Plugin-Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) project.
This plugin supports the [GitHub Updater](https://github.com/afragen/github-updater) plugin, so if you install that, this plugin becomes automatically updateable direct from GitHub. Any submission to WP.org repo will make this redundant.
