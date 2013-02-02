<?php
  
  require_once "lib/FMongo.php";
  
  class FMongoTest extends FMongo
  {
    public $___db = 'tests';
    public $___collection   = 'fmongo';
    public $___key = 'mid';
    public $___loaded = false;
  
  
    public $name = null;
    public $description = null;

    public function prototype ()
    {
        $prototype = array ();

        $prototype ['mid'] = null;
        $prototype ['name'] = null;
        $prototype ['description'] = null;
        $prototype ['destiny'] = null; // address
        $prototype ['origin'] = 'FMongoTest'; // address
        $prototype ['steps'] = null; // array
        
        return $prototype;
    }
 
    public function test ()
      {
      $this->testConnect ();
      $this->testSave ();
      $this->testLoad ();
      $this->testLoadAndSave ();
      
/*      try
        {
        $this->testInstall ();
        }
      catch (Exception $e)
        {
        $this->warning ('test install failed, but probably because the database exists');
        }
  
      $this->testUninstall ();
      
      $this->install ();
  
  
      $this->testLoad ();
  
      $this->testLoadAndSave ();
  
      $this->testCount ();
      
      $this->testFind ();
  
      $this->testFirst ();
  */
      }
  
     
  
     public function testConnect ()
       {
       $this->connect ('mongodb://localhost:27017');

       return;
       }

    public function testSave ()
      {
      $this->name = 'box';
      $this->description = 'this is not a box.';
      $this->origin = array ('address' => 'Schönhauser Allee');
      $this->save ();
      }


    public function testLoad ()
      {
      $test = new FMongoTest ();
      $test->mid = '510c568ec98e36.19003607';
      $test->load ();
  
      $this->log ($test);
      }



    public function testLoadAndSave ()
      {
      $this->log ('>>>>>>>>>>> testing load and save');
     
      $test = new FMongoTest ();
      $test->mid = '10c57eba48f61.89283657';
      $test->load ();
      $test->description = 'this is a nice box';
      $test->steps = 'kkza';
      $test->save ();
  
      $this->log ($test);
      }

/*  
    public function testInstall ()
      {
      $this->install ();
      }
  
    public function testUninstall ()
      {
      $this->uninstall ();
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
  


*/
  

  }

