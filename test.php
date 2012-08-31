<?php

      ini_set ('log_errors', true);
      ini_set ('error_log', '/var/log/nginx/error.log');

require_once "test/all.php";

$fabstract = new FAbstractTest ();
$fabstract->test ();

$fstorable = new FSTorableTest ();
$fstorable->test ();


