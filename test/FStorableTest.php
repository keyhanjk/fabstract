<?php

  require_once "lib/FStorable.php";
  
  class FStorableTest extends FStorable
  {
    public $___table = 'tests';
    public $___key   = 'testId';
    public $___fields = array ('testId', 'name', 'description');
    public $___types  = array ('key', 'string', 'text');
  
  
    public $name = null;
    public $description = null;
  
    public function test ()
      {
      $this->testConnect ();
      
      try
        {
        $this->testInstall ();
        }
      catch (Exception $e)
        {
        $this->warning ('test install failed, but probably because the database exists');
        }
  
      $this->testUninstall ();
      
      $this->install ();
  
      $this->testSave ();
  
      $this->testLoad ();
  
      $this->testLoadAndSave ();
  
      $this->testCount ();
      
      $this->testFind ();
  
      $this->testFirst ();
  
      }
  
     
  
     public function testConnect ()
       {
       $this->connect ('localhost', 'fabstract');
       return;
       }
  
    public function testInstall ()
      {
      $this->install ();
      }
  
    public function testUninstall ()
      {
      $this->uninstall ();
      }
  
    public function testSave ()
      {
      $this->name = 'testing';
      $this->description = 'this test tests the save functionality of the fstorable object';
      $this->save ();
  
  
      for ($i = 0; $i < 100; $i++)
        {
        $test = new FStorableTest ();
        $test->name = md5 ('name' . $i);
        $test->description = md5 ('description' . $i);
        $test->save ();
        }
  
      }
  
  
    public function testLoad ()
      {
      $test = new FStorableTest ();
      $test->testId = 1;
      $test->load ();
  
      $this->log ($test);
      }
  
    public function testLoadAndSave ()
      {
      $first = new FStorableTest ();
      $first->testId = 1;
      $first->load ();
      $first->description .= ' [object updated]';
      $first->save ();
  
      $second = new FStorableTest ();
      $second->testId = 1;
      $second->load ();
      $this->log ($second);
      }
  
  
    public function testCount ()
      {
      $counter = new FStorableTest ();
      
      $this->log ('there are ' . $counter->count () . ' ' . $counter->___table . ' stored in the database');
      }
  
    public function testFind ()
      {
      $search = new FStorableTest ();
      $search->description = '%42%';
     
      $results = $search->find ();
  
      foreach ($results as $r)
        $this->log ('found ' . $r->testId . ' > ' . $r->name);
      }
  
  
    public function testFirst ()
      {
      $search = new FStorableTest ();
      $search->name = '%42%';
  
      $result = $search->first ();
  
      $this->log ($result);
      }
  



  

  }

