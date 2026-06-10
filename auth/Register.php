<?php

require "./config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($password != $_POST["confirm_password"]){
        die("Passwords dont match");
    }

    $hashpassword = password_hash($password,PASSWORD_DEFAULT);

    $check_email = $pdo->prepare(
        "SELECT id FROM users WHERE email = :email"
    );

    $check_email->execute([
        ":email" => $email
    ]);

    if ($check_email->rowCount() > 0 ) {
        die("Email Alraedy Exists");
    }


    try {
    
        $sql = "INSERT INTO users(full_name, email, password) VALUES (:full_name, :email, :password)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":full_name" => $fullname,
            ":email" => $email,
            ":password" => $hashpassword
        ]);

        echo "Signup succes";
    }

    catch (PDOException $e) {
        echo "Invalide " . $e->getMessage();
    }



}


?>