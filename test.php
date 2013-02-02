<?php

ini_set ('log_errors', true);
ini_set ('error_log', '/var/log/nginx/error.log');

require_once "test/all.php";

/*
$fabstract = new FAbstractTest ();
$fabstract->test ();

$fcurl = new FCurlTest ();
$fcurl->test ();

$fsite = new FSiteTest ();
$fsite->test ();

$fstorable = new FSTorableTest ();
$fstorable->test ();
*/
$fmongo = new FMongoTest ();
$fmongo->test ();
