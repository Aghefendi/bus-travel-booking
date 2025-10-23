<?php
require_once './includes/db.inc.php';


$stmt = $db->prepare("SELECT id, email, password FROM User");
$result = $stmt->execute();

$rehash_count = 0;

while ($user = $result->fetchArray(SQLITE3_ASSOC)) {

    $password = $user['password'];

    if (!preg_match('/^\$2[ayb]\$/', $password)) {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $update = $db->prepare("UPDATE User SET password = :password WHERE id = :id");
        $update->bindValue(':password', $hashed, SQLITE3_TEXT);
        $update->bindValue(':id', $user['id'], SQLITE3_INTEGER);
        $update->execute();


        $rehash_count++;
    }
}


?>