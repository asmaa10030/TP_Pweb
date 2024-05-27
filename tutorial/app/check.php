<?php
session_start(); // Start the session

if(isset($_POST['id'])){
    require '../php/config.php';

    $id = $_POST['id'];

    if(empty($id)){
        header("Location: ../home.php?mess=error");
        exit(); // Terminate further execution
    } else {
        if (!isset($con)) {
            die("Database connection not established.");
        }
        
        // Check if the user is logged in
        if (!isset($_SESSION['id'])) {
            header("Location: ../index.php");
            exit(); // Terminate further execution
        }

        // Retrieve user ID from session
        $user_id = $_SESSION['id'];

        // Prepare and execute a query to retrieve the to-do item
        $stmt = $con->prepare("SELECT id, checked FROM todos WHERE id=? AND creater=?");
        $stmt->bind_param('ii', $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the to-do item
        $todo = $result->fetch_assoc();

        if (!$todo) {
            echo "error"; // To-do item not found or user does not have permission
            exit(); // Terminate further execution
        }

        // Toggle the checked status
        $checked = $todo['checked'] ? 0 : 1;

        // Prepare and execute a query to update the checked status
        $stmt = $con->prepare("UPDATE todos SET checked=? WHERE id=? AND creater=?");
        $stmt->bind_param('iii', $checked, $id, $user_id);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            echo $checked; // Return the updated checked status
        } else {
            echo "error"; // Failed to update the checked status
        }

        $con->close();
        exit(); // Terminate further execution
    }
} else {
    header("Location: ../home.php?mess=error");
    exit(); // Terminate further execution
}
?>
