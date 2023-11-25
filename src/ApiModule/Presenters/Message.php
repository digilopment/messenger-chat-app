<?php
(new class {
    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    private function handlePostRequest()
    {
        $channelName = $this->messenger->escape($_POST['channel']);
        $userName = $this->messenger->escape($_POST['user']);
        $messageText = $this->messenger->escape($_POST['message']);

        $this->channelName = $this->messenger->nameUrl($channelName);

        $query = $this->messenger->db->prepare('INSERT INTO messages (channel_name, user, message, created_at) VALUES (:channel_name, :user, :message, :created_at)');
        $query->bindValue(':channel_name', $this->channelName, SQLITE3_TEXT);
        $query->bindValue(':user', $userName, SQLITE3_TEXT);
        $query->bindValue(':message', $messageText, SQLITE3_TEXT);
        $query->bindValue(':created_at', (new DateTime())->format('Y-m-d H:i:s'), SQLITE3_TEXT);

        $query->execute();
    }

    public function handleGetRequest($data = [])
    {
        $channelName = $_GET['channel'];
        $this->channelName = $this->messenger->nameUrl($channelName);

        $limit = $data['limit'] ?? '';
        $orderBy = $data['orderBy'] ?? 'ORDER BY created_at';
        $andWhere = $data['andWhere'] ?? '';
        $query = $this->messenger->db->prepare('SELECT * FROM messages WHERE channel_name = :channel_name ' . $andWhere . ' ' . $orderBy . ' ' . $limit . '');
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
            if (isset($_GET['push'])) {
                $data = [
                    'orderBy' => 'ORDER BY id DESC',
                    'limit' => 'LIMIT 1',
                ];
                $this->handleGetRequest($data);
            } else if (isset($_GET['fromId'])) {
                $data = [
                    'andWhere' => 'AND id > ' . $this->messenger->escape($_GET['fromId']),
                ];
                $this->handleGetRequest($data);
            } else {
                $this->handleGetRequest();
            }
        }
    }

})->route();
