<?php

  require_once dirname (__FILE__) . "/FAbstract.php";

  // abstract class for html page structure shortcuts
  abstract class FSite extends FAbstract
    {


    // override this on your extended class to configure
    // you should remember to define constants for diferent 
    // environments (dev, staging, production)
    // we won't do that kind of logic here right now.
    public $___www = 'http://localhost';
    public $___scripts = 'http://localhost/scripts';
    public $___styles = 'http://localhost/styles';
    public $___images = 'http://localhost/images';
    public $___bones = '/var/www/bones';
    public $___handles = '/var/www/handles';
    public $___section = null;
    


    protected $___sufixes = array ('.js', '.php', '.css', '.png', '.jpg');


    public function session ($name = null)
      {
      if (!empty ($name))
        session_name ($name);

      session_start ();
      }

    public function www ($url = '')
      {
      return $this->___www;
      }


    public function url ($path)
      {
      return $this->___www . $path;
      }


    public function a ($href = null, $title = null, $class = null, $content = null, $extra = null)
      {
      $href = empty ($href) ? $this->___www : $href;
      $title = empty ($title) ? $href : $title;
      $class = empty ($class) ? null : $class;
      $content = empty ($content) ? $title : $content;

      return '<a href="' . $href . '" title="' . $title . '" alt="' . $title . '"' . (($class !== null) ? ' class="' . $class . '"': '') . (($extra !== null) ? ' ' . $extra : '') . '>' . $content . '</a>';
      }



    // TODO: implement isAbsolute and prepend $___scripts if not
    public function script ($path)
      {
      $path = $this->___prefixable ($path) ? $this->___scripts . $path : $path;

      return '<script src="' . $path . '"></script>';
      }



    public function style ($path)
      {
      $path = $this->___prefixable ($path) ? $this->___styles . $path : $path;

      return '<link href="' . $path . '" type="text/css" rel="stylesheet" />';
      }

    
    public function image ($path, $alt = null)
      {
      $alt = empty ($alt) ? $path : $alt;

      $path = $this->___prefixable ($path) ? $this->___images . $path : $path;

      return '<img src="' . $path . '" alt="' . $alt . '" />';
      }


    public function bone ($path)
      {
      $extension = $this->___extension ($path);
     
      return $this->___bones . $path . (($extension === null) ? '.php' : '');
      }
  
    public function handle ($path)
      {
      $extension = $this->___extension ($path);

      return $this->___handles . $path . (($extension === null) ? '.php' : '');
      }




    // gets a $name param from a global arrays
    // $default is the value returned when no param is found
    // $method can be 'server', 'get', etc... to go for the $_METHOD collection
    // if $method === null we go for the $_REQUEST
    public function param ($name, $default = null, $method = null)
      {
      $collection = null;

      if ($method === null)
        $collection = $_REQUEST;
      else
        {
        switch ($method)
          {
          case 'server' : $collection = $_SERVER; break;
          case 'get' : $collection = $_GET; break;
          case 'post' : $collection = $_POST; break;
          case 'files' : $collection = $_FILES; break;
          case 'cookie' : $collection = $_COOKIE; break;
          case 'session' : $collection = $_SESSION; break;
          case 'request' : $collection = $_REQUEST; break;
          case 'env' : $collection = $_ENV; break;
          }
        }

      if ($collection === null)
        $this->error ('invalid param call. method name is probably unknown.');

      if (isset ($collection [$name]))
        return $collection [$name];

      return $default;
      }    





    public function mail ($from, $to, $subject, $body)
      {
      $headers = '';
      
      $headers .= 'From: ' . $from . "\r\n";
      $headers .= 'Reply-To: ' . $from . "\r\n";
      $headers .= 'X-Mailer: PHP/' . phpversion();

      $this->log ("$to, $subject, $body, $headers");
      $ok = mail($to, $subject, $body, $headers);
      

      if (!$ok)
        $this->error ('Failed to send your email');

      return true;
      }



    // if $site->section returns current top level section
    // if $site->section (true) returns array with section tree
    // if $site->section ('produts', 'product', 'detail') defines section and subsections
    public function section ()
      {
      $args = func_get_args ();

      if (empty ($args) && !empty ($this->___section) && is_array ($this->___section))
        return $this->___section [0];
      
      if (is_array ($args) && (count ($args) == 1) && ($args [0] === true))
        return $this->___section;
        
      if (is_array ($args))
        $this->___section = $args;
      }


 
    protected function ___prefixable ($path)
       {
       if ((strpos($path, 'http://') === 0 ) || (strpos($path, 'https://') === 0 ))
         return false;

       return true;
       }



    protected function ___extension ($path)
       {
       preg_match("/\.([^\.]+)$/", $path, $matches);    
       
       if (isset ($matches [1]))
         return $matches[1];

       return null;
       }

    protected function ___sufixable ($path)
       {
       }




    }
