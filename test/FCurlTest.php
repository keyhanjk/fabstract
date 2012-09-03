<?php

  require_once "lib/FCurl.php";

  class FCurlTest extends FCurl
    {
    
    public function test ()
      {
      $this->testRequest ();
      }

    public function testRequest ()
      {
      $this->log ($this->request ('http://vimeo.com/api/v2/nos2filmes/videos.json', 'json'));
      }

    }
