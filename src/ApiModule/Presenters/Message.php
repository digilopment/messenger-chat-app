<?php
(new class {
    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    private function handlePostRequest()
    {
        $channelName = $_POST['channel'];
        $userName = $_POST['user'];
        $messageText = $_POST['message'];

        $this->channelName = $this->messenger->nameUrl($channelName);

        $query = $this->messenger->db->prepare('INSERT INTO messages (channel_name, user, message, created_at) VALUES (:channel_name, :user, :message, :created_at)');
        $query->bindValue(':channel_name', $this->channelName, SQLITE3_TEXT);
        $query->bindValue(':user', $userName, SQLITE3_TEXT);
        $query->bindValue(':message', $messageText, SQLITE3_TEXT);
        $query->bindValue(':created_at', (new DateTime())->format('Y-m-d H:i:s'), SQLITE3_TEXT);

        $query->execute();
    }

    private function handleGetRequest()
    {
        $channelName = $_GET['channel'];
        $this->channelName = $this->messenger->nameUrl($channelName);

        $query = $this->messenger->db->prepare('SELECT * FROM messages WHERE channel_name = :channel_name ORDER BY created_at');
        $query->bindValue(':channel_name', $this->channelName, SQLITE3_TEXT);

        $result = $query->execute();
        $data = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }

        echo json_encode($data);
    }

    public function route()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
        } else {
            $this->handleGetRequest();
        }
    }
})->route();
