<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit(); // Stop further execution
}

require 'php/config.php'; // Include database connection file

// Function to sanitize input data
function sanitize_input($data) {
    // Remove leading and trailing whitespace
    $data = trim($data);
    // Remove backslashes
    $data = stripslashes($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data);
    return $data;
}

// Retrieve current user's ID from session
$user_id = $_SESSION['id'];
$todos_query = $con->prepare("SELECT todos.* FROM todos JOIN users ON todos.creater = users.id WHERE users.id = ?");
$todos_query->bind_param('i', $user_id);

// Execute prepared statement
if ($todos_query->execute()) {
    $result = $todos_query->get_result();
    $todos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Handle query execution error
    // For example, redirect with an error message
    header("Location: home.php?error=database");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To-Do List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img class="gif" src="img/tasks.gif" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="home.php">To-Do List</a></li>
            <li><a href="dashboard.php">Tasks</a></li>
            <li><a href="timer.php">Countdown Timer</a></li>
            <li><a href="edit.php">Edit Profile</a></li>
            <li><a href="php/logout.php">Logout</a></li>
        </ul>
    </div>
    <main>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- PHP loop to display todos -->
                    <?php foreach ($todos as $todo) { ?>
                        <tr>
                            <td><?php echo sanitize_input($todo['title']); ?></td>
                            <td><?php echo sanitize_input($todo['category']); ?></td>
                            <td><?php echo sanitize_input($todo['date_time']); ?></td>
                            <td class="<?php echo $todo['checked'] ? 'done' : 'pending'; ?>"><?php echo $todo['checked'] ? 'Done' : 'Pending'; ?></td>
                        </tr>
                    <?php } ?>
                    <!-- End of PHP loop -->
                </tbody>
            </table>
        </div>
    </main>
    
    <script src="js/jquery-3.2.1.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.remove-to-do').click(function(){
                const id = $(this).attr('id');
                
                $.post("app/remove.php", 
                      {
                          id: id
                      },
                      (data)  => {
                         if(data){
                             $(this).parent().hide(600);
                         }
                      }
                );
            });

            $(".check-box").click(function(e){
                const id = $(this).attr('data-todo-id');
                
                $.post('app/check.php', 
                      {
                          id: id
                      },
                      (data) => {
                          if(data != 'error'){
                              const h2 = $(this).next();
                              if(data === '1'){
                                  h2.removeClass('checked');
                              }else {
                                  h2.addClass('checked');
                              }
                          }
                      }
                );
            });
        });
    </script>
</body>
</html>
