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

// Prepare SQL statement
$todos_query = $con->prepare("SELECT todos.*, categories.name AS category_name, categories.color AS category_color FROM todos LEFT JOIN categories ON todos.category_id = categories.id WHERE todos.creater = ?");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img class="gif" src="img/tasks.gif" alt="Logo">
        </div>
        <ul class="sidebar-menu">
        <li><a href="home.php"><i class="fas fa-list"></i> To-Do List</a></li>
        <li><a href="dashboard.php"><i class="fas fa-tasks"></i> Tasks</a></li>
        <li><a href="timer.php"><i class="fas fa-clock"></i> Countdown Timer</a></li>
        <li><a href="edit.php"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
        <li><a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
    </div>
    <main>
    <div class="table-container">
        <table class="custom-table"> <!-- Add class "custom-table" to apply the CSS styles -->
            <thead>
                <tr>
                    <th style="color:grey;">Title</th>
                    <th style="color:grey;">Description</th>
                    <th style="color:grey;">Category</th>
                    <th style="color:grey;">Created</th>
                    <th style="color:grey;">Status</th>
                </tr>
            </thead>
            <tbody>
               <!-- PHP loop to display todos -->
<?php foreach ($todos as $todo) { ?>
    <tr>
        <td><?php echo sanitize_input($todo['title']); ?></td>
        <td><?php echo sanitize_input($todo['description']); ?></td>
        <td style="background-color: <?php echo $todo['category_color']; ?>"><?php echo sanitize_input($todo['category_name']); ?></td>
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
