<?php
/*
 * Initialization file:
 *
 * Initializes the configuration and setup for the cms framework:
 *
 * - Start all sessions.
 * - PHP version check.
 * - Sets the PHP memory limit.
 * - Defines the global constants. (sitemap, root directory, self and start memory)
 * - Calls the core
 * - Calls the model
 * - Calls the controller
 * - Calls the service
 * - Requests templates.
 *
 */
namespace init;

use application\core;
use application\classes\validator;

error_reporting(-1);

/*-----------------
Start all sessions
*-----------------*/

session_start();

/*--------------------
Set the memory limit
*-------------------*/

ini_set('memory_limit', '500M');

/*----------------
Set xdebug limits
*----------------*/

ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

/*------------------------
Set the default time zone
*-----------------------*/

date_default_timezone_set('Europe/Amsterdam');

/*----------------
Global constants
*---------------*/

define('LITENING_SITEMAP', 'quaratio');
define('LITENING_ROOT_DIRECTORY', $_SERVER['DOCUMENT_ROOT'].'/'.LITENING_SITEMAP.'/');
define('LITENING_SELF', 'index.php');
define('LITENING_START_MEMORY', memory_get_usage(false));

/*----
Core
*----*/

require(LITENING_ROOT_DIRECTORY."application/core/core.php");

/*-------------
Configuration
*-------------*/

(new core\Core( "configuration", "config", "config" ))->request();

/*----------
Controller
*----------*/

(new core\Core( "controller", "controller", "controller" ))->request();

/*-------
Service
*-------*/

(new core\Core( "service", "service", "service" ))->request();

/*--------
Validator
*--------*/

( new core\Core( "class", "validator", "validator" ) )->request();
?>