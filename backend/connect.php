<?php
function getDBConnection() {
    $username = "Aish";           // ← the schema that has your tables
    $password = "Aish_123";  // ← AISH user's password
    $connection_string = "//localhost/FREEPDB1";  
    $conn = oci_connect($username, $password, $connection_string);

    if (!$conn) {
        return false;
    }

    return $conn;
}

function create_unique_id() {
    return bin2hex(random_bytes(16));
}
