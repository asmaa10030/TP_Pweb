<?php
session_start(); // Start the session

// Check if the request is a POST request and if an 'id' parameter is provided
if(isset($_POST['id'])){
    require '../php/config.php'; // Include the database configuration file

    $id = $_POST['id']; // Get the 'id' parameter from the POST data

    // Check if the ID is empty
    if(empty($id)){
        // No ID provided, return error message
        echo "Error: No ID provided";
        exit(); // Terminate further execution
    } else {
        // Check if the database connection is established
        if (!isset($con)) {
            echo "Error: Database connection not established.";
            exit(); // Terminate further execution
        }

        // Check if the user is logged in
        if (!isset($_SESSION['id'])) {
            echo "Error: User not logged in";
            exit(); // Terminate further execution
        }

        // Retrieve user ID from session
        $user_id = $_SESSION['id'];

        // Prepare and execute a query to select the to-do item
        $stmt = $con->prepare("SELECT id FROM todos WHERE id=? AND creater=?");
        $stmt->bind_param('ii', $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the to-do item
        $todo = $result->fetch_assoc();

        // Check if the to-do item exists and if the user has permission to delete it
        if (!$todo) {
            echo "Error: To-do item not found or user does not have permission";
            exit(); // Terminate further execution
        }

        // Prepare and execute a query to delete the to-do item
        $stmt = $con->prepare("DELETE FROM todos WHERE id=? AND creater=?");
        $stmt->bind_param('ii', $id, $user_id);
        $res = $stmt->execute();

        // Check if deletion was successful
        if($res){
            echo "1"; // Deletion successful
        } else {
            echo "Error: Deletion failed";
        }

        // Close the prepared statement
        $stmt->close();

        // Close the database connection
        $con->close();

        exit(); // Terminate further execution
    }
} else {
    // No ID provided, return error message
    echo "Error: No ID provided";
    exit(); // Terminate further execution
}
?>
