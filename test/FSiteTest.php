<?php

  require_once ("lib/FSite.php");

  class FSiteTest extends FSite
    {
    public $___www = 'http://fabstract/';
    public $___scripts = 'http://fabstract/scripts/';
    public $___styles = 'http://fabstract/styles/';
    public $___images = 'http://fabstract/images/';
    public $___bones = '/var/www/bones/';
   

    public function test ()
      {
      $this->testParam ();
      $this->testWww ();
      $this->testA ();
      $this->testScript ();
      $this->testStyle ();
      $this->testImage ();
      $this->testBone ();
      }


    public function testParam ()
      {
      $this->log ($this->param ('PWD', null, 'server'));
      }


    public function testWww ()
      {
      $this->log ($this->www ('fabstract.php'));
      }

    public function testA ()
      {
      $this->log ($this->a());
      $this->log ($this->a ('link.php'));
      $this->log ($this->a ('link.php', 'with a title'));
      $this->log ($this->a ('link.php', 'with a title', 'andAClass'));
      $this->log ($this->a ('link.php', 'with a title', 'andAClass', '<p>and a content</p>'));
      $this->log ($this->a ('link.php', 'with a title', 'andAClass', '<p>and a content</p>', 'and="some" extra="properties"'));
      }


    public function testScript ()
      {
      $this->log ($this->script ('fabstract.js'));
      $this->log ($this->script ('http://fabstract/scripts/fabstract.js'));
      }
 

    public function testStyle ()
      {
      $this->log ($this->style ('fabstract.css'));
      $this->log ($this->style ('https://fabstract/styles/fabstract.css'));
      }
    

    public function testImage ()
      {
      $this->log ($this->image ('fabstract.png', 'some alternative text here.'));
      $this->log ($this->image ('https://fabstract/images/fabstract.png'));
      }


    public function testBone ()
      {
      $this->log ($this->bone ('fabstract.php'));
      }




    }
