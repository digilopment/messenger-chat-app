<?php
(new class {

    private $messenger;

    public function __construct()
    {
        $this->messenger = new Messenger();
    }

    public function run()
    {
        $id = 3;
        $newName = "Magda";
        $sql = "UPDATE users SET name = 'Magda' WHERE id = :id";

        // Prepare the query
        $query = $this->messenger->db->prepare($sql);

        // Bind parameters
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        $query->execute();
    }

})->run();
