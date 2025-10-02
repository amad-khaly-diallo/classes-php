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
        if($this->getUserwithLogin($login)) {
            return "Login already exists.";
        }
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
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->password = null;
        $this->connected = false;
        return true;
    }

    // Delete
    public function delete() {
        if ($this->id) {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $this->id]);
            $this->disconnect();
            return true;
        }
        return false;
    }

    // Update
    public function update($login, $password, $email, $firstname, $lastname) {
        if (!$this->id) return false;
        if($this->getUserwithLogin($login)) return false;

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

    function getUserwithLogin($login) {
        $sql = "SELECT * FROM users WHERE login = :login";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':login' => $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Getters simples
    public function getLogin() { return $this->login; }
    public function getEmail() { return $this->email; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
}

$user = new Userpdo("localhost", "classes", "root", "root");

// Register un user
echo "<h2>Inscription</h2>";
$infos = $user->register("jane", "azerty", "jane@mail.com", "Jane", "Doe");
print_r($infos);

// Connecter un user
echo "<h2>Connexion</h2>";
if ($user->connect("jane", "azerty")) {
    echo "Connecté : " . $user->getLogin() . "<br>";
}

// Mettre à jour
echo "<h2>Mise à jour</h2>";
if ($user->update("jane.doe", "123456", "jane.doe@mail.com", "Jane", "Doe")) {
    print_r($user->getAllInfos());
}else {
    echo "Le login existe déjà.<br>";
}

// Vérifier si connecté
echo "<h2>Vérification de la connexion</h2>";
var_dump($user->isConnected());

// déconnexion 
echo "<h2>Vérification de la déconnexion</h2>";
if($user->disconnect()) {
    echo "Déconnecté.<br>";
}else {
    echo "Échec de la déconnexion.<br>";
}

// Supprimer le compte
echo "<h2>Suppression du compte</h2>";
if ($user->delete()) {
    echo "Compte supprimé.<br>";
}else {
    echo "Échec de la suppression du compte.<br>";
}

?>