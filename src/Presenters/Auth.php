<?php

(new class {

    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    public function run()
    {
        $data = [
            'password' => $_POST['password'],
            'email' => $_POST['email'],
        ];
        $response = $this->messenger->login($data);
        $this->messenger->headers()->getResponse($response);
    }

})->run();
