<?php 
require 'User.php';

$user = new User("localhost", "root", "root", "classes");

// Créer un utilisateur
$infos = $user->register("jdoe", "123456", "jdoe@mail.com", "John", "Doe");
print_r($infos);

// Connecter un utilisateur
if ($user->connect("jdoe", "123456")) {
    echo "Connecté : " . $user->getLogin();
}

// Mettre à jour
$user->update("john.doe", "abcdef", "john@mail.com", "John", "Doe");

// Vérifier si connecté
var_dump($user->isConnected());

// Supprimer l'utilisateur
$user->delete();
var_dump($user->isConnected());
