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
require_once dirname(__FILE__ ).'/function.php';
require_once dirname(__FILE__ ).'/posts/posts.php';
require_once dirname(__FILE__ ).'/tagsToday/tags.php';
require_once dirname(__FILE__ ).'/specialTeleproject/teleproject.php';
require_once dirname(__FILE__ ).'/tvProgramma/tv.php';
require_once dirname(__FILE__ ).'/categories/categories.php';
require_once dirname(__FILE__ ).'/teleproject/teleproject.php';
require_once dirname(__FILE__ ).'/issues/issues.php';

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


