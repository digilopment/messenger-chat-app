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
            'name' => $_POST['name'],
            'password' => md5($_POST['password']),
            'email' => $_POST['email'],
        ];
        $response = $this->messenger->createUser($data);
        $this->messenger->headers()->getResponse($response);
    }

})->run();
