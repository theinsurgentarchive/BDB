<?php
    //Current Iteration is used to manually generate Password Hashes
    $password = readline("Enter Password, Minimum Length is 8: ");
    echo "Hashing Password...\n";
    $passhash = password_hash($password, PASSWORD_DEFAULT);
    echo "Verifying Hash...\n";
    if (!password_verify($password, $passhash)) {
        echo "ERROR, Hashing Failed.....\n";
        exit();
    }
    echo "Success!! Hashing Complete, Your New Password Hash: " . $passhash . "\n";
    exit();
?>