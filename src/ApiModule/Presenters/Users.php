<?php
(new class {
    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    public function run()
    {
        $response = $this->messenger->getUsers();
        $this->messenger->headers()->getResponse($response);
    }
})->run();
