<?php

require_once dirname (__FILE__) . "/FAbstract.php";
require_once dirname (__FILE__) . "/FTool.php";

abstract class FMongo extends FAbstract
{
    protected static $___connection = null;

    protected $___key = null;
    protected $___collection = null;
    protected $___db = null;
    private $___loaded = null;
    
    public function prototype ()
    {
        return array ();
    }    

    public function connect ($servers, $options = null)
    {
        if (empty ($servers))
            $this->error ('not enough information to connect to mongodb.');

        if (empty ($options))
            $options = array ('connect' => true);

        // checking dependency
        if (!class_exists('MongoClient'))
            $this->error ('no mongo client class. remember to sudo pecl install mongo.');

        try
        {
            $connection = new MongoClient ($servers, $options);
        
            $ok = $connection->connect ();
        
            if (!$ok)
                $this->error ('could not connect to mongo.');
        
            self::$___connection = $connection;
        }
        catch (Exception $e)
        {
            $this->error ('couldn\'t connect to the mongo:' . $e->getMessage ());
        }

        $this->log ('connected to ' . $servers . '.');

        return self::$___connection;
    }
    
    // $key var must be set with value before invoking this function
    public function load ($connection = null)
    {
        if (empty ($this->___db))
            $this->error ('cannot load a doc without db.');

        if (empty ($this->___collection))
            $this->error ('cannot load a doc without collection.');

        if (empty ($this->___key))
            $this->error ('cannot load a doc without key.');

        if (empty ($this->{$this->___key}))
            $this->error ('cannot load a doc without a valued key.');

        $connection = $this->check ($connection);

        try
            {
            $collection = self::$___connection->selectCollection ($this->___db, $this->___collection);
            }
        catch (Exception $e)
            {
            $this->error ('selecting collection generated an exception.');
            }

        $doc = $collection->findOne (array ($this->___key => $this->{$this->___key}));

        if (empty ($doc))
            {
            $this->warning ('empty load.');
            $this->warning (array ($this->___key => $this->{$this->___key}));

            $this->___loaded = false;

            return $this->___loaded;
            }

        $this->fill ($doc);

        $this->___loaded = true;

        $this->log ('loaded ' . $this->___key . ' > ' . $this->{$this->___key} . '.');

        return $this->___loaded;
        
    }

    public function loaded ()
    {
        return $this->___loaded;
    }

    public function fill ($doc)
    {
        if (empty ($doc))
            $this->error ('filling me with an empty doc.');
 
        $prototype = $this->prototype ();

        $this->log ($doc);
 
        foreach ($doc as $key => $value)
        {
            if (empty ($value))
                continue;

            if (!array_key_exists($key, $prototype))
                continue;

            if (empty ($prototype [$key]))
            {
                $this->{$key} = $value;
                continue;
            }

            if (is_array ($value) && is_string ($prototype [$key]) && class_exists ($prototype [$key]))
            {
                $this->{$key} = new $prototype [$key] ();
                $this->{$key}->fill ($value);
                
                continue;
            }
        }
        return true;
    }

    public function save ($connection = null)
    {
        if (empty ($this->___db))
            $this->error ('cannot save a doc without db');

        if (empty ($this->___collection))
            $this->error ('cannot save a doc without collection');

        $connection = $this->check ($connection);
        
        try
            {
            $collection = $connection->selectCollection ($this->___db, $this->___collection);
            }
        catch (Exception $e)
            {
            $this->error ('selecting collection generated an exception.');
            }

        if (empty ($this->{$this->___key}))
            {
            $this->{$this->___key} = FTool::uuid ();
            }

        $criteria = array ($this->___key => $this->{$this->___key});
      
        $doc = $this->doc (get_object_vars($this));

        $result = $collection->update ($criteria, $doc, array ('upsert' => true));

        if (empty ($result ['ok']))
            $this->error ('write was unacknowledged!');

        $this->log ('saved ' . $this->___key . ' > ' . $this->{$this->___key});

        return true;
    }

    protected function doc ($data)
    {
        $prototype = $this->prototype ();

        foreach ($data as $key => $value)
        {
            if (!array_key_exists($key, $prototype))
                continue;

            if (is_null ($value))
            {
                unset ($prototype [$key]);
                continue;
            }

            if (is_string ($prototype [$key]) && class_exists ($prototype [$key]))
            {
                $dummy = new $prototype [$key] ();

                if (is_array ($value))
                    $prototype [$key] = $dummy->doc ($value);
                if (is_object ($value))
                    $prototype [$key] = $dummy->doc (get_object_vars($value));

                continue;
            }

            $prototype [$key] = $value;
        }

        return $prototype;
    }


    private function check ($connection = null)
    {
        if (empty ($connection) && empty (self::$___connection))
            $this->error ('no connection!');

        if (empty ($connection))
            $connection = self::$___connection;

        return $connection;
    }

    
    public function first ($connection = null)
    {
        $result = $this->find ($connection, 1);

        if (empty ($result))
            return null;

        if (!is_array ($result))
            return null;

        return $result [0];
    }

    public function find ($connection = null, $number = 10)
    {
        if (empty ($this->___db))
            $this->error ('cannot load a doc without db.');

        if (empty ($this->___collection))
            $this->error ('cannot load a doc without collection.');

        if (empty ($this->___key))
            $this->error ('cannot load a doc without key.');

        $connection = $this->check ($connection);

        try
        {
            $collection = self::$___connection->selectCollection ($this->___db, $this->___collection);
        }
        catch (Exception $e)
        {
            $this->error ('selecting collection generated an exception.');
        }

        $docs = $collection->find ()->limit ($number);

        if (empty ($docs))
        {
            $this->warning ('empty find.');
            $this->warning (array ($this->___key => $this->{$this->___key}));

            $this->___loaded = false;

            return $this->___loaded;
        }
        
        $result = array ();
        $clazz = get_class ($this);
        foreach ($docs as $doc)
        {
            $object = new $clazz ();
            $object->fill ($doc);
            $result []= $object;
        }
        
        $this->log ($result);


        $this->log ('found ' . count ($result) . ' objects.');

        return $result;
        
    }


}
