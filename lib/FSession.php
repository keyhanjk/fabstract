<?php

abstract class FSession extends FAbstract implements SessionHandlerInterface
{
    protected $___session = null;

    public function close ()
    {
        $this->log ('closing session.');
//        $this->error ('not implemented');
        return true;

    }
    public function destroy ($session_id)
    {
        $this->log ('destroying session.');

//        $this->error ('not implemented');

        return true;
    }
    public function gc ($maxlifetime)
    {
        $this->log ('gcing session.');
//        $this->error ('not implemented');

        return true;
    }
    public function open ($save_path, $session_id)
    {
        return true;

    }

    public function read ($session_id)
    {
        $this->log ('reading session.');

        $this->___session = new FMongoSession ();
        $this->___session->session_id = $session_id;

        $this->___session->load ();

        return $this->___session->data;
    }

    public function write ($session_id, $session_data)
    {
        $this->log ('writing session.');
        $this->___session->data = $session_data;

        return $this->___session->save ();
    }
}


class FMongoSession extends FMongo
{
    public $___db = 'cache';
    public $___collection = 'sessions';
    public $___key = 'session_id';

    public $session_id = null;
    public $last = null;
    public $data = null;

    public function prototype ()
    {
        $prototype = array ();

        $prototype ['session_id'] = null;
        $prototype ['last'] = null;
        $prototype ['data'] = null;

        return $prototype;
    }


}



