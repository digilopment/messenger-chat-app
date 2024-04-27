<?php
(new class {

    const STREAM_CHANNEL_NAME = 0;
    const STREAM_CHANNEL_LIMIT = 100;

    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    public function headers()
    {
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache,no-store');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('Connection: keep-alive');
        header('Access-Control-Allow-Origin: *');
    }

    
    public function run()
    {
        $this->headers();
        $lastID = 0;
        while (true) {
            $query = "SELECT * FROM messages WHERE channel_name != 0 AND id > $lastID ORDER BY created_at LIMIT 100";
            $result = $this->messenger->db->query($query);

            $hasNewData = false;

            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $line = json_encode($row);
                echo "data: $line\n\n";
                @ob_flush();
                flush();
                $lastID = $row['id'];
                $hasNewData = true;
            }

            if (!$hasNewData) {
                echo "\n";
                @ob_flush();
                flush();
                usleep(0.125 * 1000000);
            }
            sleep(1);
        }
        $this->db->close();
    }

})->run();
