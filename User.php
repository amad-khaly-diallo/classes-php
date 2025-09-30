<?php
class User {
    private $id;
    private $password;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $mysqli;
    private $connected = false;

    // Constructeur
    public function __construct($host, $user, $pass, $dbname) {
        $this->mysqli = new mysqli($host, $user, $pass, $dbname);
        if ($this->mysqli->connect_error) {
            die("Erreur de connexion : " . $this->mysqli->connect_error);
        }
    }

    // Register : créer un utilisateur
    public function register($login, $password, $email, $firstname, $lastname) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->mysqli->prepare("INSERT INTO users (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $login, $passwordHash, $email, $firstname, $lastname);
        $stmt->execute();

        $this->id = $stmt->insert_id;
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $passwordHash;
        $this->connected = true;

        return $this->getAllInfos();
    }

    // Connect : connecter un utilisateur
    public function connect($login, $password) {
        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            $this->password = $user['password'];
            $this->connected = true;
            return true;
        }
        return false;
    }

    // Disconnect
    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->password = null;
        $this->connected = false;
    }

    // Delete
    public function delete() {
        if ($this->id) {
            $stmt = $this->mysqli->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $this->disconnect();
        }
    }

    // Update
    public function update($login, $password, $email, $firstname, $lastname) {
        if (!$this->id) return false;

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->mysqli->prepare("UPDATE users SET login=?, password=?, email=?, firstname=?, lastname=? WHERE id=?");
        $stmt->bind_param("sssssi", $login, $passwordHash, $email, $firstname, $lastname, $this->id);
        $stmt->execute();

        $this->login = $login;
        $this->password = $passwordHash;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        return true;
    }

    // Check connection
    public function isConnected() {
        return $this->connected;
    }

    // Get all infos
    public function getAllInfos() {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    // Getters simples
    public function getLogin() { return $this->login; }
    public function getEmail() { return $this->email; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
}
?>
