<?php
(new class {
    public function __construct()
    {
        require_once '../src/Messenger.php';
    }

    public function convertToFileNameFormat($input)
    {
        $path = [];
        foreach (explode('/', $input) as $i => $part) {
            $words = explode('-', $part);
            $capitalizedWords = array_map('ucfirst', $words);
            //$name = ($i == 0 ? $capitalizedWords . 'Module' : $capitalizedWords);
            if ($i == 0) {
                $path[] = implode('', $capitalizedWords) . 'Module/Presenters';
            } else {
                $path[] = implode('', $capitalizedWords) . '';
            }
        }
        return join('/', $path) . '.php';
    }

    public function run()
    {
        $route = $_GET['route'] ?? '';
        if ($route) {
            $presenter = '../src/' . $this->convertToFileNameFormat($route);
            require_once $presenter;
        } else {
            require_once 'app.php';
        }
    }
})->run();
