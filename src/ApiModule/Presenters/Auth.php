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
            'oauth' => $_POST['oauth'] ?? '',
            'name' => $_POST['name'] ?? '',
        ];
        if ($data['oauth'] && $data['oauth'] == 'facebook') {
            $response = $this->messenger->fbLogin($data);
            $this->messenger->headers()->getResponse($response);
        } else {
            $response = $this->messenger->login($data);
            $this->messenger->headers()->getResponse($response);
        }
    }
})->run();
