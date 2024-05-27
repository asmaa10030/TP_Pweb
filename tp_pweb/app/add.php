<?php
session_start(); // Start the session

if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['category'])){
    require '../php/config.php';

    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    if(empty($title) || empty($description) || empty($category)){
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
        $stmt = $con->prepare("INSERT INTO todos (title, description, category, creater) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $title, $description, $category, $user_id);

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
