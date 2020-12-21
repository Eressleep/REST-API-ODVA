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
require_once dirname(__FILE__ ). '/function.php';

require_once dirname(__FILE__) . '/collections/posts.php';
require_once dirname(__FILE__) . '/collections/tags.php';
require_once dirname(__FILE__) . '/collections/special.php';
require_once dirname(__FILE__) . '/collections/tv.php';
require_once dirname(__FILE__) . '/collections/categories.php';
require_once dirname(__FILE__) . '/collections/teleproject.php';
require_once dirname(__FILE__) . '/collections/issues.php';

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


