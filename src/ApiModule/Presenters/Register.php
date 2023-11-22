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
            'name' => $this->messenger->escape($_POST['name']),
            'password' => md5($this->messenger->escape($_POST['password'])),
            'email' => $this->messenger->escape($_POST['email']),
        ];
        $response = $this->messenger->createUser($data);
        $this->messenger->headers()->getResponse($response);
    }
})->run();
