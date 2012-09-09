<?php
  
  require_once "lib/FAbstract.php";
  
  class FAbstractTest extends FAbstract
  {
    public $this_variable = 'has a value';
  
  
    public function test ()
      {
      $this->testOutput ();
      $this->testJson ();
      }
  
     
  
    public function testJson ()
      {
      $this->log ($this->json ());
      }
      
  
  
    public function testOutput ()
      {
      $this->output ('testing ouput');
      $this->eco ('testing eco');
      $this->br ('testing br');
      $this->p ('testing p', 'nameOfTheClass');
      $this->log ('testing log');
      $this->warning ('testing warning');
      try { $this->error ('testing error'); } catch (Exception $e)  {}
     
      $object = new stdClass ();
      $object->property = "value";
      $object->another = "yet";
      $object->anArray  = array ('one', 'two', 'three');
      $this->output ($object, 'p', 'this is an object inside a p');
  
      $array = array ('key1' => 'value1', 'key2' => 'value2', 'key3' => array ('blah blah blah', 'and blah'));
      $this->output ($array, 'br', 'and an array');
  
      }
  
  }

