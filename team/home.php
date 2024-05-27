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
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <title>To-Do List</title>
    <link rel="stylesheet" href="css/style.css">
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
    <div class="show-todo-section">
    <div class="todos-container">
        <?php foreach ($todos as $todo) { ?>
            <div class="todo-item">
                <span id="<?php echo $todo['id']; ?>" class="remove-to-do">x</span>
                <input type="checkbox" class="check-box" data-todo-id="<?php echo $todo['id']; ?>" <?php if($todo['checked']) echo 'checked'; ?> />
                <h2 class="<?php if($todo['checked']) echo 'checked'; ?>"><?php echo sanitize_input($todo['title']); ?></h2>
            </div>
        <?php } ?>
    </div>
    <div class="add-section">
                <form action="app/add.php" method="POST" autocomplete="off">
                    <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error'){ ?>
                        <input type="text" name="title" style="border-color: #ff6666" placeholder="This field is required" />
                        <input type="text" name="description" style="border-color: #ff6666" placeholder="description is required" />
                        <select name="category" class="todo-input" style="display: block; margin: 0 auto;">
                            <option value="">Select Category</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Study">Study</option>
                            <option value="Work">Work</option>
                            <option value="Gym">Gym</option>
                        </select>

                       <button type="submit" class="btn" style="text-align:center; width:50%;">
                        <div style="display: block; align-items: center; ">Add <i class="bx bx-plus-circle"></i></div>
                        </button>
                        <?php } else { ?>
                        <input type="text" name="title" placeholder="Add a task" />
                        <input type="text" name="description" placeholder="Description for more details" />
                        <select name="category" class="todo-input" style="display: block; margin: 0 auto;">
                            <option value="">Select Category</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Study">Study</option>
                            <option value="Work">Work</option>
                            <option value="Gym">Gym</option>
                        </select>
        
    <button type="submit" class="btn" style="text-align:center; width:50%;">
        <div style="display: block; align-items: center; ">Add <i class="bx bx-plus-circle"></i></div>
    </button>
                    <?php } ?>
                </form>
            </div>
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
                console.log("Todo ID:", id);
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
