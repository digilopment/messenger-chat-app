<?php

class Messenger
{
    public $db;

    public function __construct()
    {
        $this->initializeSession();
        $this->db = new SQLite3('../storage/chat.db');

        $query = 'CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY,
            channel_name TEXT,
            user TEXT,
            message TEXT,
            created_at DATETIME
        )';
        $this->db->exec($query);

        $query = 'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY,
            name TEXT,
            password TEXT,
            email TEXT,
            color TEXT,
            created_at DATETIME
        )';
        $this->db->exec($query);
    }

    public function escape($param)
    {
        return htmlspecialchars($param);
    }

    public function initializeSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function nameUrl($inputString)
    {
        $cleanedString = preg_replace('/[^a-z0-9]+/', '-', strtolower($inputString));
        $cleanedString = trim($cleanedString, '-');
        return $cleanedString;
    }

    public function getUser($id)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $query->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $query->execute();

        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getUsers()
    {
        $query = $this->db->prepare('SELECT * FROM users');
        $result = $query->execute();
        $data = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function userExists($email)
    {
        $query = $this->db->prepare('SELECT COUNT(*) AS count FROM users WHERE email = :email');
        $query->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $query->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row['count'];
    }

    public function log($data)
    {
        file_put_contents('../storage/login.txt', json_encode($data), FILE_APPEND);
    }

    public function createUser($data)
    {
        $name = $data['name'];
        $password = md5($data['password']);
        $email = $data['email'];

        $color = $this->stringToHexColor($name);

        if ($this->userExists($email)) {
            $response = [
                'success' => 0,
                'message' => 'Používateľ s týmto emailom už existuje.',
                'data' => $data,
            ];
        } else {
            $createdAt = (new DateTime())->format('Y-m-d H:i:s');

            $query = $this->db->prepare('INSERT INTO users (name, password, email, color, created_at) VALUES (:name, :password, :email, :color, :created_at)');
            $query->bindValue(':name', $name, SQLITE3_TEXT);
            $query->bindValue(':password', $password, SQLITE3_TEXT);
            $query->bindValue(':email', $email, SQLITE3_TEXT);
            $query->bindValue(':color', $color, SQLITE3_TEXT);
            $query->bindValue(':created_at', $createdAt, SQLITE3_TEXT);
            $query->execute();

            $query = $this->db->prepare('SELECT MAX(id) FROM users LIMIT 1');
            $result = $query->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);
            $lastInsertId = $row['max_id'];

            $data = [
                'name' => $name,
                'email' => $email,
                'id' => $lastInsertId,
            ];
            $response = [
                'success' => 1,
                'message' => 'Používateľ bol úspešne vytvorený. ',
                'data' => $data,
            ];
        }
        return $response;
    }

    public function login($data)
    {
        $email = $data['email'];
        $user = $this->getUserByEmail($email);
        if ($user && ($user['password'] == md5($data['password']))) {
            $_SESSION['user'] = $user['id'];
            return [
                'success' => 1,
                'message' => 'Prihlásenie úspešné.',
                'data' => $user,
            ];
        } else {
            return [
                'success' => 0,
                'message' => 'Nesprávne prihlasovacie údaje.',
                'data' => $user,
                'post_data' => $data,
            ];
        }
    }

    public function fbLogin($data)
    {
        $password = md5('fbLogin' . $data['email']);
        $name = $data['name'];
        $email = $data['email'];
        $user = $this->getUserByEmail($email);
        if ($user && ($user['password'] == $password)) {
            $_SESSION['user'] = $user['id'];
        } else {
            $data = [
                'email' => $email,
                'password' => $password,
                'name' => $name,
            ];
            $user = $this->createUser($data);
            $_SESSION['user'] = $user['data']['id'];
        }

        return [
            'success' => 1,
            'message' => 'Prihlásenie úspešné.',
            'data' => $user,
        ];
    }

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        return ['message' => 'Odhlásenie úspešné.'];
    }

    public function headers()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        return $this;
    }

    public function getResponse($response)
    {
        $utf8EncodedData = mb_convert_encoding($response, 'UTF-8');
        echo json_encode($utf8EncodedData);
    }

    private function getUserByEmail($email)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $query->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $query->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function stringToHexColor($str)
    {
        $hash = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $hash = ord($str[$i]) + (($hash << 5) - $hash);
        }
        $color = strtoupper(dechex($hash & 0x00FFFFFF));
        $paddedColor = str_pad($color, 6, '0', STR_PAD_LEFT);
        return "#$paddedColor";
    }
}
