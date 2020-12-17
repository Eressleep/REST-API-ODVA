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
function autoloader($class){
	require_once dirname(__FILE__ ) . '/collection.php';
}
spl_autoload_register('autoloader');

$requests = new collections();

$requests->restPost();

$requests->restIssues();

$requests->restSpecialTeleproject();

$requests->restTags();

$requests->restTvProgramma();

$requests->restIssues();


