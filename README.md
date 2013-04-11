Wordpress-Theme-Utils
=====================

A little collection of utils i use while developing themes for WP.
Originally developed to be used as a Parent Theme. 

Usage
-----

1. Go to your theme directory: usually <wordpress-base-dir>/wp-content/themes/
2. clone this repository with 
````bash
	git clone https://github.com/setola/Wordpress-Theme-Utils.git
````

3. Make a new directory with the name of your theme
````bash

	mkdir my-new-theme-name
````

4. Start writing down your theme with this parameter in style.css header
````css
	/*
	TEMPLATE: Wordpress-Theme-Utils
	*/
````


If you plan to overwrite functions.php you have to hook your customizations
to 'after_setup_theme' action hook.
````php

function wtu_child_theme_customizations(){
	$test = new LipsumGenerator();
	$test->hook();

}

add_action('after_setup_theme', 'wtu_child_theme_customizations');
````
more infos on this:
http://justintadlock.com/archives/2010/12/30/wordpress-theme-function-files
and thank you Justin!

Classes
-------

In this folder you'll find some .class.php files 
with some useful classes to implement some 
standard features of a generic website.

For further info please refer to the php doc in the source file
or find some example of use in a finished theme

TODO: move some examples here



JS
--

In this fonder there is a collection of JavaScript used in many themes.
They're registered in DefaultAssets object, you can find there the handle for the js
you need and enqueue it where you need it by using wp_enqueue_script() function.



Images
------

Store here the images your theme will use.
By default there are:
* FancyBox default style images
* a standard favicon 
* jQuery UI Smoothness theme images


Less
----

Here you can find some .less files: mixins, variables 
and a compile.ini configuration for the less to css render engine.



Partials
------------------

Here are stored some template parts that can be useful as starting point for building your theme
To use it simply call for:
````php
<?php get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'slideshow'); ?>
````

Templates
------------------

Here you can find some different templates