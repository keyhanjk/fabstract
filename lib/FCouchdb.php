<?php

  require_once dirname (__FILE__) . "/FAbstract.php";

  abstract class FCouchdb extends FAbstract
  {
    protected $___host  = null;
    protected $___db    = null;
    protected $___username = null;
    protected $___password = null;


    // host getter/setter
    public function host ($host = null)
      {
      if ($this->___host != null)
        $this->___host = $host;  
      
      return $this->___host;
      }

    // host getter/setter
    public function db ($db = null)
      {
      if ($this->___db != null)
        $this->___db = $db;  
      
      return $this->___db;
      }

    public function dbUrl ($credentials = false, $db = true)
      {
      if (empty ($this->___host) || empty ($this->___db))
        throw new Exception ('need more info to connect');

      return 'http://' . ($credentials ? $this->___username . ':' . $this->___password . '@' : '') . $this->___host . ($db ? '/'. $this->___db : '');
      }

    public function changes ($id = false)
    {
    
      $curl = new FCurl ();
       
      $url = $this->dbUrl (false, true) . '/_changes/';

      $result = $curl->request ($url, 'json', 'get', false);

      if (empty ($result))
        return null;
     
      if ($id)
        return $result->last_seq;

      return $result->results;
    }

    public function register ($username, $password)
    {
      $curl = new FCurl ();
       
      $url = $this->dbUrl (true, false) . '/_users/org.couchdb.user:' . $username;

      $data = new stdClass ();
      $data->_id = 'org.couchdb.user:' . $username;
      $data->name = $username;
      $data->roles = array ();
      $data->type = 'user';
      $data->password = $password;

      $this->log ($url);
      $this->log ($data);
     
      $curl->binary (json_encode ($data));
      $this->log ($curl->binary ());
      $result = $curl->request ($url, 'json', 'put', false);

      $this->log ($result);

      return $result;
    }

    public function doc ($id)
    {
      $curl = new FCurl ();
       
      $url = $this->dbUrl () . '/' . $id . '/';

      $result = $curl->request ($url, 'json', 'get', false);

      return $result;
    }
  }
