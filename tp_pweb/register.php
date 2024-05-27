<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Register</title>
</head>
<body>
    <style>
        body{
            display: flex; /* Use flexbox to make the body a flex container */
  justify-content: flex-start; /* Align items at the start of the flex container */
  background-image: url('img/back.png');
  background-size: cover; /* Ensure the image covers the entire background */
  background-position: center; /* Center the image */
  background-repeat: no-repeat; /* Prevent the image from repeating */
  background-attachment: fixed; /* Keep the background image fixed in place */
  margin: 0; /* Remove default margin */
  height: 100vh; /* Ensure the body takes up the full height of the viewport */
  font-family: 'Poppins',sans-serif;
            
        }
          
    </style>
<main>
<div class="show-todo-section">
        <div class="add-section">
        <?php 
         
         include("php/config.php");
         if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password']; // New line added for confirm password

            // Check if passwords match
            if($password !== $confirm_password) {
                echo "<div class='message'>
                          <p>Passwords do not match!</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                exit(); // Exit script if passwords do not match
            }

            // Verifying the unique email
            $verify_query = mysqli_query($con,"SELECT Email FROM users WHERE Email='$email'");
            if(mysqli_num_rows($verify_query) != 0 ){
                echo "<div class='message'>
                          <p>This email is used, Try another One Please!</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            } else {
                mysqli_query($con,"INSERT INTO users(Username,Email,Password) VALUES('$username','$email','$password')") or die("Error Occurred");
                echo "<div class='message'>
                          <p>Registration successful!</p>
                      </div> <br>";
                echo "<a href='index.php'><button class='btn'>Login Now</button>";
            }
         } else {
        ?>
            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username" style="color:grey;">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email" style="color:grey;">Email</label>
                    <input type="email" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password" style="color:grey;">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="confirm_password" style="color:grey;">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register">
                </div>
                <div class="links"style="color:grey;">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
         </div>
         </main>
</body>
</html>
