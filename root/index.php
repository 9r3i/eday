<?php
/* e-day version 2.1.0 as library
 * ~ using namespace eday
 * started at november 28th 2019
 */

/* define eday as true */
defined('EDAY') or define('EDAY',true);

/* initialize eday's engine
 * then start the engine
 */
(new eday\init(__DIR__))->engine->start();


