<?php
session_start(); // Start the session

if(isset($_POST['title']) && isset($_POST['category'])){
    require '../php/config.php';

    $title = $_POST['title'];
    $category = $_POST['category'];

    if(empty($title) || empty($category)){
        header("Location: ../home.php?mess=error");
    } else {
        if (!isset($con)) {
            die("Database connection not established.");
        }
        
        // Check if the user is logged in
        if (!isset($_SESSION['id'])) {
            header("Location: ../index.php");
            exit();
        }
        
        $user_id = $_SESSION['id'];
        $stmt = $con->prepare("INSERT INTO todos (title, category, creater) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $title, $category, $user_id);
        
        // Execute the prepared statement
        $res = $stmt->execute();
        
        // Check if execution was successful
        if($res){
            header("Location: ../home.php?mess=success"); 
        } else {
            header("Location: ../home.php");
        }
        
        // Close the database connection
        $con->close();
        exit();
    }
} else {
    header("Location: ../home.php?mess=error");
}
?>
