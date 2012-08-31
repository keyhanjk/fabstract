<?php

require_once "lib/FAbstract.php";

?>
<!doctype html>
<html>
  <head>
    <title>FAbstract</title>
  </head>
  <body>
    <p>
      FAbstract project home.
    </p>
    <p>
      <a href="test.php">test here.</a>
    </p>
    <ul>
      <li>FAbstract</li>
      <li>
        <ul>
          <li>eco ($message)</li>
          <li>br ($message)</li>         
          <li>p ($message, label = null)</li>
          <li>log ($message)</li>
          <li>warning ($message)</li>
          <li>error ($message, $throw = true)</li>
          <li>param ($name, $default = null, $method)</li>
          <li>json ($json, $hide = null, $header = true)</li>
          <li>output ($message, $mode = null, $label = null)</li>
       </ul>
      </li>
      <li>FStorable</li>
      <li>
        <ul>
          <li>connect ($host, $name, $user = 'root', $password = '', $default = true)</li>
          <li>loaded ()</li>
          <li>query ($query, $connection = null)</li>
          <li>load ($connection = null)</li>
        </ul>
      </li>
    </ul>
  </body>
</html>
