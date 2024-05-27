<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit(); // Stop further execution
}

require 'php/config.php'; // Include database connection file

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
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <title>To-Do List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img class="gif" src="img/tasks.gif" alt="Logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="home.php">To-Do</a></li>
            <li><a href="dashboard.php">Tasks</a></li>
            <li><a href="edit.php">edit profile</a></li>
            <li><a href="php/logout.php">logout</a></li>
        </ul>
    </div>
    <main>
        <div class="show-todo-section">
            <?php foreach ($todos as $todo) { ?>
                <div class="todo-item">
                    <span id="<?php echo $todo['id']; ?>" class="remove-to-do">x</span>
                    <?php if($todo['checked']){ ?> 
                        <input type="checkbox" class="check-box" data-todo-id ="<?php echo $todo['id']; ?>" checked />
                        <h2 class="checked"><?php echo $todo['title'] ?></h2>
                    <?php }else { ?>
                        <input type="checkbox" data-todo-id ="<?php echo $todo['id']; ?>" class="check-box" />
                        <h2><?php echo $todo['title'] ?></h2>
                    <?php } ?>
                    <br>
                    <small>Category: <?php echo htmlspecialchars($todo['category']); ?></small>
                    <br>
                    <small>Created: <?php echo $todo['date_time'] ?></small> 
                </div>
            <?php } ?>
            <div class="add-section">
                <form action="app/add.php" method="POST" autocomplete="off">
                    <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error'){ ?>
                        <input type="text" name="title" style="border-color: #ff6666" placeholder="This field is required" />
                        <input type="text" name="category" style="border-color: #ff6666" placeholder="Category is required" />
                        <button type="submit">Add &nbsp; <span>&#43;</span></button>
                    <?php } else { ?>
                        <input type="text" name="title" placeholder="What do you need to do?" />
                        <input type="text" name="category" placeholder="Category" />
                        <button type="submit" class="btn" style="text-align:center;"> <div style="display: block; align-items: center;">Add <i class="bx bx-plus-circle"></i></div></button>
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
