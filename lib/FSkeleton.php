<?php


  // abstract class for html page structure shortcuts
  abstract class FSkeleton
    {
    public var $___home = '/var/www/';
    public var $___tmp = '/var/www/';
    public var $___runtime = '/var/www/bin';
    public var $___bones = '/var/www/bones';
    public var $___root = 'http://localhost';
    public var $___scripts = 'http://localhost/scripts';
    public var $___styles = 'http://localhost/styles';
    public var $___images = 'http://localhost/images';

    // home getter/setter
    public function home ($home = null)
      {
      if ($this->___home != null)
        $this->___home = $home;  
      
      return $this->___home;
      }

    

    // root getter/setter
    public function root ($root = null)
      {
      if ($this->___root != null)
        $this->___root = $root;  
      
      return $this->___root;
      }


    

 
    }