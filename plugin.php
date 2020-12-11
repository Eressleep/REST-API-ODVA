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

require_once dirname(__FILE__ ) .'/function.php';

$collections = opendir(dirname(__FILE__ ).'/collections/');
//добавить поик по мульти сущностям
while ($collection = readdir($collections))
	require_once $collections.$collection;

closedir($collections);

//setting multi post search
odvaMultiPostSearch();
//setting up posts
odvaPost();
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
odvaIssues();


