<?php
/**
 * Plugin Name: WP-REST-API-ODVA
 * Plugin URI:  nope
 * Description: nope
 * Author:      Alex Sem
 * Author URI: 	nope
 * Version: 	0.1
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 **/
define('SHORTINIT',1);

//hang listener to connect

foreach (glob(dirname(__FILE__ ). '/collections/*.php') as $collection){
	require_once $collection;
}


//setting up posts
collections::odvaPost();
//setting up tags for today
odvaTags();
//setting up special teleproject
specialTeleproject();
//setting up tvProgramma
odvaTvProgramma();
//setting up to show all categories without os
odvaCategories();
//setting up teleproject
odvaTeleproject();
//setting up issues
colodvaIssues();


