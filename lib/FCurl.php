<?php


  require_once dirname (__FILE__) . "/FAbstract.php";


  class FCurl extends FAbstract
    {
    
    // stores the curl resource. you can populate it manually if you want to
    // it'll be then used on the request method
    public $___curl = null; 

    // stores and array with parameters to use on the request
    public $___data = null;
   
    // binary mode for post requests
    public $___binary = false;
  
    // home path. if this is set, it'll be appended to the request uri
    public $___home = null;

    // cached files ttl in seconds
    public $___ttl = 600; 


    // making sure there's always a curl object instantiated
    public function __construct ()
      {
      $this->___curl = curl_init ();
      }





    // making sure we close the curl object before we die
    public function __destruct ()
      {
      if (is_resource ($this->___curl))
        curl_close($this->___curl);
      
      return;
      }




    // home getter/setter
    public function home ($home = null)
      {
      if ($this->___home != null)
        $this->___home = $home;  
      
      return $this->___home;
      }




    // http auth shortcut implementation
    public function login ($username, $password)
      {
      curl_setopt($this->curl, CURLOPT_USERPWD,  $this->username . ':' . $this->password);

      return true;
      }



    // adds a parameter to the request
    // $name - parameter name
    // $value - parameter value
    // $encode - (optional) use urlencode for $name *and* $value 
    // TODO: support files
    // TODO: support objects
    public function data ($name, $value = null, $encode = true)
      {
      $parameter = new stdClass ();
      
      $parameter->name = $name;
      $parameter->value = null;
      $parameter->encode = true;


      if ($this->___data === null)
        $this->___data = array ();

      array_push ($this->___data, $parameter);

      return true;
      }

 

    // binary flag is set to false on startup
    // $value is set to true by default so that you can call $this->binary ()
    // to activate binary mode
    public function binary ($value = null)
      {
      if (!empty ($value))
        $this->___binary = $value;

      return $this->___binary;
      }






    // shortcut to the curl_getinfo function 
    public function info ()
      {
      return curl_getinfo ($this->___curl);
      }
      




    protected function ___cachePath ($uri)
      {
      return sys_get_temp_dir () . '/' . md5 ($uri);
      }



    public function cached ($uri)
      {
      $path = $this->___cachePath ($uri);

      $time = filemtime ($path);
      $now = time ();
   
      if (!$time || (($this->___ttl !== null) && ($time + $this->___ttl) < $now))
        {
        return null;
        }

      return file_get_contents ($path);
      }



    public function cache ($uri, $content)
      {
      $path = $this->___cachePath ($uri);

      file_put_contents ($path, $content);
      }



    
    // $requests content at $uri using $method
    // $method currently supports only 'get' or 'post'
    // if $mode is 'string' returns the string content (default)
    // if $mode is 'json' tries to return a json object
    // if $mode is 'dom' tries to return a dom document (not implemented)
    // if $mode is 'simple' tries to return a simple xml (not implemented)
    // if $mode is 'object' tries to instantiate a model (not implemented)
    // if $mode is 'auto' it tries to infer a compatible mode (not implmented)
    // if $reset the ___data property is emptied after the request
    public function request ($uri, $mode = 'string', $method = 'get', $cache = true, $reset = true, $force = false)
      {
      if (empty ($uri) || !is_string ($uri))
        $this->error ('no url defined for request!');

      $response = null;


      if ($cache)
        {
        $cached = $this->cached ($uri);

        if (!empty ($cached))
          $response = $cached;
        }

      if (empty ($response))
        {
        if ($this->___curl === null)
          $this->___curl = curl_init ();
        
        $query = '';
  
        if (!empty ($this->___data))
          {
          foreach ($this->___data as $parameter)
            {
            if ($query !== '')
              $query .= "&";
    
            if ($parameter->encode)
              $query .= urlencode ($parameter->name) . "=" . urlencode ($parameter->value);
            else
              $query .= $parameter->name . "=" . $parameter->value;
            }
          }
  
        // hello fellow coder! 
        // this is the url of the request. 
        // you might want to debug it if you're having any problems. 
        $url = (!empty ($this->___home) && is_string ($this->___home) ? $this->___home : '') . $uri . (($method === 'get') && ($query !== '') ? '?' . $query : '') ;        
  
        curl_setopt($this->___curl, CURLOPT_RETURNTRANSFER,  true);
        curl_setopt($this->___curl, CURLOPT_URL,  $url);
  
        if ($method === 'post')
          curl_setopt($this->___curl, CURLOPT_POSTFIELDS,  $query);
        else if ($method !== 'get')
          curl_setopt($this->___curl, CURLOPT_CUSTOMREQUEST, strtoupper ($method)); // -X
  
        curl_setopt($this->___curl, CURLOPT_HEADER, 0);
        curl_setopt($this->___curl, CURLOPT_POST, ($method === 'post') ? 1 : 0);
        
        if (!empty ($this->___binary) && ($method !== 'get'))
          {
          // binary overrides postfields
          curl_setopt($this->___curl, CURLOPT_POSTFIELDS,  $this->___binary);
          curl_setopt($this->___curl, CURLOPT_BINARYTRANSFER, 1);
          }
  
        $response = curl_exec($this->___curl);
        }

      if (!empty ($response) && ($cache || $force))
        $this->cache ($uri, $response);

      switch ($mode)
        {
        case 'json': $response = $this->___responseToJson ($response); break;
        }

      if ($reset)
        $this->___data = array ();

      return $response;
      }


    // protected methods. these you might want to extend but shouldn't be necessary
    protected function ___responseToJson ($response)
      {
      return json_decode ($response);
      }



    }


