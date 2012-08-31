<?php


  // fstorable is an abstraction class for simple mysql querying
  // it does not support more than simple single table queries
  // it may, one day, support joins, but complete sql implementation
  // is unnecessary. they have frameworks for that.

  abstract class FStorable extends FAbstract
    {
    public static $___connection = null; // connection to the database
    public $___table = null; // name of the table
    public $___key = null; // name of the key property
    public $___fields = null; // array of fields to store on the database
    public $___types = null; // array of types for the fields. same order than the fields array. same length as well.

    public $___created = null;
    public $___updated = null;
    

    

    // $host is the hostname for the database
    // $name is the name of the database
    // $user is the username to access the database, default is 'root'
    // $password is the password to access the database, default is ''
    // if !$default, we connect and return the connection, not updating the $___connection
    public function connect ($host, $name, $user = 'root', $password = '', $default = true)
      {
      if (empty ($host) || empty ($name))
        $this->error ('not enough information to connect to database.');

      try
        {
        $connection = mysql_pconnect($host, $user, $password, true);
        
        if ($connection === false)
          $this->error ('couldnt connect to the database');

        mysql_select_db($name, $connection);
        }
      catch (Exception $e)
        {
        $this->error ('couldn\'t connect to the db:' . $e->getMessage ());
        }

      if ($default)
        self::$___connection = $connection;
      
      $this->log ('connected to ' . $name . '@' . $host);

      return $connection;
      }








    
    // creates the table to store the class
    // if you pass a $connection, it'll use that one, else it'll use self::___connection
    // this means that you should call $this->connect () first
    public function install ($connection = null)
      {
      $connection = $this->check ($connection);

      $structure = '';
      $fields = $this->___fields;
      $types = $this->___types;

      $fields = array_merge ($fields, array ('___created',  '___updated'));
      $types  = array_merge ($types,  array ('datetime', 'datetime'));
    
      for ($i = 0; $i < count ($fields); $i++)
        {
        $isKey = false;
        $type = null;

        switch ($types [$i])
          {
          case 'key':
            $isKey = true;
            $type = 'int(10)';
          break;

          case 'string'  : $type = 'varchar(255)'; break;
          case 'text'    : $type = 'text'; break;
          case 'boolean' : $type = 'int(1)'; break;
          case 'number'  : $type = 'int(10)'; break;
          case 'datetime': $type = 'datetime'; break;
          default: $this->error ('weird type: ' . $types [$i]); break;       
 
          }

        if ($isKey)
          $structure .= $fields [$i] . ' ' . $type . ' NOT NULL AUTO_INCREMENT, ';
        else
          $structure .= $fields [$i] . ' ' . $type . ', ';
        }

      $structure .= 'PRIMARY KEY (' . $this->___key  . ')';

      $query = 'CREATE TABLE ' . $this->___table . ' (' . $structure . ') ENGINE=MyISAM DEFAULT CHARSET=utf8';

      $result = $this->query ($query);

      return $result;
      }  






    public function uninstall ($connection = null)
      {
      $connection = $this->check ($connection);

      $query = 'DROP TABLE ' . $this->___table;

      $this->query ($query);

      return;
      } 









    public function save ($connection = null)
      {
      $connection = $this->check ($connection);

      $isNew = (empty ($this->{$this->___key})) ? true : false;
    
      if ($isNew)    
        {
        $values = '';
        $fields = $this->___fields;
        $types  = $this->___types;
        $value  = null;

        $this->___created = time ();
        $this->___updated = time ();

        $fields = array_merge ($fields, array ('___created', '___updated'));
        $types  = array_merge ($types,  array ('datetime', 'datetime'));     

        for ($i = 0; $i < count ($fields); $i++)
          {
          switch ($types [$i])
            {
            case 'key'      : $value = 'NULL';                            break;
            case 'boolean'  : $value = ($this->{$fields [$i]}) ? 1 : 0;   break;
            case 'string': case 'text'   : $value = ($this->{$fields [$i]} === null) ? 'NULL' : '\'' . mysql_real_escape_string ($this->{$fields [$i]}) . '\''; break;
            case 'number'   : $value = '\'' . $this->{$fields [$i]} . '\''; break;
            case 'datetime' : $value =  $this->{$fields [$i]}; break;
            }  

          if ($values == '')
            $values = $value;
          else
            $values .= ' ,' . $value;
      
          }
      
        $query = 'INSERT INTO ' . $this->___table . ' VALUES (' . $values . ')';
      
        $result = $this->query ($query);
        $this->{$this->___key} = mysql_insert_id ($connection);
        }
      else
        {
        $values  = '';
        $fields  = $this->___fields;
        $types   = $this->___types;
        $value   = null;

        $this->___updated = time ();

        $fields = array_merge ($fields, array ('___created', '___updated'));
        $types  = array_merge ($types,  array ('datetime', 'datetime'));
      
        for ($i = 0; $i < count ($fields); $i++)
          {
          if ($types [$i] == 'key')
            continue;
          
          switch ($types [$i])
            {
            case 'string': case 'text'   : $value = ($this->{$fields [$i]} === null) ? 'NULL' : '\'' . mysql_real_escape_string ($this->{$fields [$i]}) . '\''; break;
            case 'boolean'  : $value = ($this->{$fields [$i]}) ? 1 : 0;   break;
            case 'datetime' : $value = '\'' . $this->{$fields [$i]} . '\''; break;
            case 'number'   : $value = '\'' . $this->{$fields [$i]} . '\''; break;
            }  

          if ($values == '')
            $values = $fields [$i] . '=' . $value;

          else
            $values .= ' ,' . $fields [$i] . '=' . $value;

          }
      
        $query = 'UPDATE ' . $this->___table . ' SET ' . $values . ' WHERE ' . $this->___key . '=' . $this->{$this->___key };
        $result = $this->query ($query);
        }
    
      return;  
 
      }











    public function loaded ()
      {
      if (!empty ($this->{$this->___key}))
        return true;

      return false;
      }



    public function query ($query, $connection = null)
      {
      if (empty ($connection))
        $connection = self::$___connection;

      $result = mysql_query ($query, $connection);

      $this->log ($query);

      if (!$result)
        $this->error ('Error querying database. ' . mysql_error($connection) . ' query: ' . $query);

      return $result;      
      }


    private function check ($connection = null)
      {
      if (empty ($connection) && empty (self::$___connection))
        $this->error ('no connection!');

      if (empty ($connection))
        $connection = self::$___connection;

      if (empty ($this->___fields))
        $this->error ('no fields to retrieve!');

      if (empty ($this->___table))
        $this->error ('no table to select!');

      return $connection;
      }


    
    // loads an object by its key value
    public function load ($connection = null)
      {
      $connection = $this->check ($connection);

      if (empty ($this->{$this->___key}))
        $this->error ('no valid key defined for ' . get_class ($this));

      $fields = '';

      foreach ($this->___fields as $field)
        $fields .= (($fields === '') ? '' : ', ') . $field;

      $query = 'SELECT ' . $fields . ' FROM ' . $this->___table . ' WHERE ' . $this->___key . '=' . $this->{$this->___key};

      $result = $this->query ($query, $connection);

      $ok = (is_resource ($result)) ? true : false;

      if ($ok)
        {
        $row    = mysql_fetch_assoc ($result);

        for ($i = 0; $i < count ($this->___fields); $i++)
          $this->{$this->___fields [$i]} = $row [$this->___fields [$i]];

        }

      return $ok;
      }

















    public function count ($connection = null)
      {
      $connection = $this->check ($connection);

      $query = 'SELECT count(*) as total FROM ' . $this->___table;

      $fields  = $this->___fields;
      $defined = array ();
      $types   = array ();
      $t       = $this->___types;
    
      $fields = array_merge ($fields, array ('___created', '___updated'));
      $t      = array_merge ($t,      array ('datetime', 'datetime'));

      for ($i = 0; $i < count ($fields); $i++)
        {
        // we don't do an empty () here because we want to accept for empty strings and 0s
        if (isset ($this->{$fields [$i]}))
          {
          $types[] = $t[$i];
          $defined[] = $fields [$i];
          }
        }

      $where = '';      
      for ($i = 0; $i < count ($defined); $i++)
        {
        if ($where === '')
          $where .= ' WHERE ';
        else
          $where .= ' AND ';

        if (($types [$i] === 'string') || ($types [$i] === 'text'))
          $where .= $defined [$i] . ' LIKE \'' . $this->{$defined [$i]} . '\'';
        else
          $where .= $defined [$i] . '=' . $this->{$defined [$i]};
        }
    
      $query .= $where;
    
      $result = $this->query ($query);

      $row = mysql_fetch_assoc($result);
    
      return $row ['total'];
      }
 


















    // shortcut to the find method: gets the first element of a table
    public function first ($connection = null, $delimiters = null, $order = null)
      {
      $connection = $this->check ($connection);

      $objects = $this->find ($connection, 1, $delimiters, $order);
    
      if (!empty ($objects))
        return $objects [0];

      return null;
      }





     public function find ($connection, $limit = null, $delimiters = null, $order = null)
      {
      $connection = $this->check ($connection);
    
      $query = 'SELECT * FROM ' . $this->___table;

      $fields = $this->___fields;
      $defined = array ();
      $types   = array ();
      $t       = $this->___types;
   
      $fields = array_merge ($fields, array ('___created', '___updated'));
      $t      = array_merge ($t,      array ('datetime', 'datetime'));
 
      for ($i = 0; $i < count ($fields); $i++)
        {
        if (isset ($this->{$fields [$i]}))
          {
          $types[] = $t[$i];
          $defined[] = $fields [$i];
          }
        }

      $where = '';      
      for ($i = 0; $i < count ($defined); $i++)
        {
        if ($where === '')
          $where .= ' WHERE ';
        else
          $where .= ' AND ';

     
        if (($types [$i] === 'string') || ($types [$i] === 'text'))
          {
          $where .= $defined [$i] . ' ' . ((($delimiters != null) && (isset ($delimiters->{$defined[$i]}))) ? $delimiters->{$defined[$i]} : 'LIKE') .  ' \'' . $this->{$defined [$i]} . '\'';
          }
        else
          {
          if ($where == '')
            $where .= $defined [$i] . ((($delimiters != null) && (isset ($delimiters->{$defined[$i]}))) ? $delimiters->{$defined[$i]} : '=') . '\'' . $this->{$defined [$i]} . '\'';
          }
        }

      $query .= $where;


      if ($order != null)
        $query .= ' ORDER BY ' . $order->field . ' ' . $order->value;

      if ($limit != null)
        $query .= ' LIMIT ' . $limit;
    
      $result = $this->query ($query);
      $objects = null;
      $class = get_class ($this); // caching class name so we don't reflect that much
    
      if (mysql_num_rows ($result) > 0)
        {
        while ($row = mysql_fetch_assoc($result))
          {
          if ($objects === null)
            $objects = array (); 
        
          $object = new $class ();
      
          for ($i = 0; $i < count ($fields); $i++)
            $object->{$fields [$i]} = $row [$fields [$i]];
        
          array_push ($objects, $object);
          }
        
        }
      
      return $objects;
      }

    }


