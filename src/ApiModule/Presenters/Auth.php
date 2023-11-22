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
            'password' => $this->messenger->escape($_POST['password']),
            'email' => $this->messenger->escape($_POST['email']),
            'oauth' => $this->messenger->escape($_POST['oauth'] ?? ''),
            'name' => $this->messenger->escape($_POST['name'] ?? ''),
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
