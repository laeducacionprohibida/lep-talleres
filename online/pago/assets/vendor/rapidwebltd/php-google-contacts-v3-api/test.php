<?php

require_once '../../../vendor/autoload.php';

use rapidweb\googlecontacts\factories\ContactFactory;

// $name = "Frodo Baggins";
// $phoneNumber = "06439111222";
// $emailAddress = "frodo@example.com";
// $note = "Note for example";
//
// $newContact = ContactFactory::create($name, $phoneNumber, $emailAddress, $note);


$contacts = ContactFactory::getAll();

if (count($contacts)) {
    echo 'Test retrieved '.count($contacts).' contacts.';
} else {
    echo 'No contacts retrieved!';
}

print_r($contacts);
