<?php

(new class {

    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    public function run()
    {
        $response = [];
        $paramId = $_GET['id'] ?? '';
        $id = $_SESSION['user'] ?? '';
        $userId = $paramId ? $paramId : $id;
        if ($userId) {
            $response = $this->messenger->getUser($userId);
        }
        $this->messenger->headers()->getResponse($response);
    }

})->run();
