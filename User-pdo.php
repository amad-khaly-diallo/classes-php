<?php
class Userpdo {
    private $id;
    private $password;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $pdo;
    private $connected = false;

    // Constructeur
    public function __construct($host, $dbname, $user, $pass) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Register
    public function register($login, $password, $email, $firstname, $lastname) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (login, password, email, firstname, lastname) 
                VALUES (:login, :password, :email, :firstname, :lastname)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':login' => $login,
            ':password' => $passwordHash,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname
        ]);

        $this->id = $this->pdo->lastInsertId();
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $passwordHash;
        $this->connected = true;

        return $this->getAllInfos();
    }

    // Connect
    public function connect($login, $password) {
        $sql = "SELECT * FROM users WHERE login = :login";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $this->id]);
            $this->disconnect();
        }
    }

    // Update
    public function update($login, $password, $email, $firstname, $lastname) {
        if (!$this->id) return false;

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users 
                SET login=:login, password=:password, email=:email, firstname=:firstname, lastname=:lastname 
                WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':login' => $login,
            ':password' => $passwordHash,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':id' => $this->id
        ]);

        $this->login = $login;
        $this->password = $passwordHash;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        return true;
    }

    // isConnected
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
