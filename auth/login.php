<?php

session_start();

include "../config/db.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = trim($_POST['email']);
    $password = ($_POST['password']);

    if(empty($email) || empty($password)) {
        $error = "All fiels are required";
    } 
    
    else {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password,$user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['$user_email'] = $user['email'];


                header("Location: ../Public/");
                exit();

            } else {
                $error = "Password incorrect";
            }
        } else {
            $error = "Emial Not Found";
        }


        }


    }

?>