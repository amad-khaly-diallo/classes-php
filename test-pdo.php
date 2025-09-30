<?php
require 'user-pdo.php';

$user = new Userpdo("localhost", "classes", "root", "root");

// Register un user
$infos = $user->register("jane", "azerty", "jane@mail.com", "Jane", "Doe");
print_r($infos);

// Connecter un user
if ($user->connect("jane", "azerty")) {
    echo "Connecté : " . $user->getLogin() . "<br>";
}

// Mettre à jour
$user->update("jane.doe", "123456", "jane.doe@mail.com", "Jane", "Doe");

// Vérifier si connecté
var_dump($user->isConnected());

// Supprimer le compte
$user->delete();
var_dump($user->isConnected());
?>
