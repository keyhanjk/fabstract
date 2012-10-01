<?php


  // some static useful functions
  abstract class FTool extends FAbstract
    {


    // given an array of objects, retrieves the first object whos property $name == $value
    public static function find ($haystack, $name, $value)
      {
      if (!is_array ($haystack) || count ($haystack) == 0)
        return null;

      foreach ($haystack as $needle)
        {
        if ($needle->{$name} == $value)
          return $needle;
        }

      return null;
      }
    }
