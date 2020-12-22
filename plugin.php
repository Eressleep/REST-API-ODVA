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
foreach (glob(dirname(__FILE__ ). '/collections/*.php') as $collection)
	require_once $collection;


collectionsForRestAPI::init();



