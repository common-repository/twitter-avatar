=== Twitter Avatar ===
Contributors: businessxpand
Tags: twitter, username, avatar, comments, twitter link
Requires at least: 2.7.0
Tested up to: 2.8.4
Stable tag: trunk

Allows a User to display their Twitter picture and link next to a comment they make on a post.

== Description ==

Allows a User to enter their Twitter username when posting a comment on your blog and a link to their Twitter page will appear next to their comment. This plugin will also replace the avatar on the comment with their picture on Twitter, both on the front-end and the Admin of the Blog.

Two small changes to your theme's comments.php file are needed for this plugin to work correctly.

Any feedback is greatly appreciated:- team@inmeres.com

= Change Log & Bug Fixes =
= 1.1 =
* Add an Admin page so that the user can specify the size of the avatar

= 1.0.2 =
* Fixed bug which showed an empty image as the avatar if a user didn't enter a Twitter account when posting a comment

= 1.0.1 =
* Grabs larger profile picture from Twitter (73 x 73 pixels), rather than the standard 48 x 48 pixel version

= 1.0 =
* Improved performance if the same Twitter user has posted more than one comment on a post

= 0.9.1 =
* Form will remember your Twitter username for future comments (Change needs to be made to `comments.php` theme file, can be found in `upgrade.txt`)

= 0.9 =
* Basic Functionality, no Admin area

== Installation ==

1. Upload `twitter-avatar` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Follow the simple instructions in the `install.txt` file

== Frequently Asked Questions ==

= Can I turn this functionality off in the Admin? =

Currently no, however this will be implemented in future versions

== Screenshots ==

1. The first user that commented on the post did not enter a Twitter Username, however the second and third users' did. Notice the link next to their name and the change in avatar.

== Changelog ==
= 1.1 =
* Add an Admin page so that the user can specify the size of the avatar

= 1.0.2 =
* Fixed bug which showed an empty image as the avatar if a user didn't enter a Twitter account when posting a comment

= 1.0.1 =
* Grabs larger profile picture from Twitter (73 x 73 pixels), rather than the standard 48 x 48 pixel version

= 1.0 =
* Improved performance if the same Twitter user has posted more than one comment on a post

= 0.9.1 =
* Form will remember your Twitter username for future comments (Change needs to be made to `comments.php` theme file, can be found in `upgrade.txt`)

= 0.9 =
* Basic Functionality, no Admin area

`<?php code(); // goes in backticks ?>`