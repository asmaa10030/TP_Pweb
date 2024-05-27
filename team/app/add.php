<?php
session_start(); // Start the session

if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['category'])){
    require '../php/config.php';

    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_name = $_POST['category'];

    if(empty($title) || empty($description) || empty($category_name)){
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

        // Retrieve category ID based on category name
        $category_stmt = $con->prepare("SELECT id FROM categories WHERE name = ?");
        $category_stmt->bind_param('s', $category_name);
        $category_stmt->execute();
        $category_result = $category_stmt->get_result();

        // Check if category exists
        if($category_result->num_rows === 0) {
            header("Location: ../home.php?mess=error");
            exit();
        }

        $category_row = $category_result->fetch_assoc();
        $category_id = $category_row['id'];

        $user_id = $_SESSION['id'];
        $stmt = $con->prepare("INSERT INTO todos (title, description, category_id, creater) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssii', $title, $description, $category_id, $user_id);

        // Execute the prepared statement
        $res = $stmt->execute();

if($res){
    header("Location: ../home.php?mess=success");
    exit();
} else {
    // Capture and log the error message
    $error_message = $stmt->error;
    error_log("Error executing SQL query: $error_message");

    header("Location: ../home.php?mess=error");
    exit();
}

        // Close the database connection
        $con->close();
        exit();
    }
} else {
    header("Location: ../home.php?mess=error");
}
?>
