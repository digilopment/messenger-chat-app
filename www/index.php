<?php
(new class {

    public function __construct()
    {
        require_once '../src/Messenger.php';
    }

    public function convertToFileNameFormat($input)
    {
        $words = explode('-', $input); // Split the input string by hyphens
        $capitalizedWords = array_map('ucfirst', $words); // Capitalize the first letter of each word
        $fileName = implode('', $capitalizedWords) . '.php'; // Join the words and add the .php extension
        return $fileName;
    }

    public function run()
    {
        $route = $_GET['route'] ?? '';
        if ($route) {
            $presenter = '../src/Presenters/' . $this->convertToFileNameFormat($route);
            require_once $presenter;
        } else {
            require_once '../src/Presenters/Html.php';
        }
    }

})->run();
