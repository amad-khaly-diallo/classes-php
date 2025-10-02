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
    public function __construct() {
        $this->mysqli = new mysqli("localhost", "root", "root", "classes");
        if ($this->mysqli->connect_error) {
            die("Erreur de connexion : " . $this->mysqli->connect_error);
        }
    }

    // Register : créer un utilisateur
    public function register($login, $password, $email, $firstname, $lastname) {
        if($this->getUserwithLogin($login)) {
            return "Login already exists.";
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->mysqli->prepare(
            "INSERT INTO users (`login`, `password`, `email`, `firstname`, `lastname`) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $login, $passwordHash, $email, $firstname, $lastname);

        if(!$stmt->execute()) {
            $stmt->close();
            return "Erreur SQL : " . $this->mysqli->error;
        }

        $this->id = $this->mysqli->insert_id;
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $passwordHash;
        $this->connected = true;

        $stmt->close();
        return $this->getAllInfos();
    }

    // Connect : connecter un utilisateur
    public function connect($login, $password) {
        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

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
            $stmt = $this->mysqli->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $this->id);
            if(!$stmt->execute()) {
                $stmt->close();
                return false;
            }
            $stmt->close();
            $this->disconnect();
            return true;
        }
        return false;
    }

    // Update
    public function update($login, $password, $email, $firstname, $lastname) {
        if (!$this->id || !$this->connected) return false;

        $existingUser = $this->getUserwithLogin($login);
        if ($existingUser) return false;

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->mysqli->prepare(
            "UPDATE users SET login=?, password=?, email=?, firstname=?, lastname=? WHERE id=?"
        );
        $stmt->bind_param("sssssi", $login, $passwordHash, $email, $firstname, $lastname, $this->id);

        if(!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $stmt->close();

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

    // Get user by login
    public function getUserwithLogin($login) {
        $id = $this->id ?? 0;
        $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE login = ? AND id != ?");
        $stmt->bind_param("si", $login, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    // Getters simples
    public function getLogin() { return $this->login; }
    public function getEmail() { return $this->email; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
}




$user = new User();

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
if ($user->update("john", "123456", "janedoe@mail.com", "Jane", "Doe")) {
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