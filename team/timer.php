<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Countdown Timer</title>
    <style>
        #countdown {
            font-size: 5em; /* Adjust font size as needed */
        }
    </style>
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
        <div class="add-section">
            <header>Countdown Timer</header>
            <form id="countdown-form">
                <div class="field input">
                    <label for="hours" style="color:grey;">Hours</label>
                    <input type="number" name="hours" id="hours" min="0" required>
                </div>

                <div class="field input">
                    <label for="minutes" style="color:grey;">Minutes</label>
                    <input type="number" name="minutes" id="minutes" min="0" max="59" required>
                </div>

                <div class="field input">
                    <label for="seconds" style="color:grey;">Seconds</label>
                    <input type="number" name="seconds" id="seconds" min="0" max="59" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" value="Start Countdown">
                </div>
            </form>
            <div id="countdown-container" style="display: none;">
                <div id="countdown" style="text-align:center;"></div>
                <button type="button" class="btn" id="pause-button" style="display: none;">Pause</button>
            </div>
            <div id="countdown-finished" style="display: none;">
                <p>Countdown finished!</p>
                <audio id="audio" src="alarm_beep-clock-165474.mp3" preload="auto"></audio>
                <button type="button" class="btn" id="restart-button">Restart</button>
            </div>
        </div>
    </div>
</main>
<script>
    var intervalId;
    var totalSeconds = 0; // Initialize totalSeconds
    var paused = false;
    var countdownFinished = false;

    document.getElementById('countdown-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission

        // Get user input
        var hours = parseInt(document.getElementById('hours').value) || 0;
        var minutes = parseInt(document.getElementById('minutes').value) || 0;
        var seconds = parseInt(document.getElementById('seconds').value) || 0;

        // Calculate total seconds
        totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

        // Hide form
        document.getElementById('countdown-form').style.display = 'none';

        // Show countdown container
        document.getElementById('countdown-container').style.display = 'block';

        // Start countdown
        var countdown = document.getElementById('countdown');
        intervalId = setInterval(function() {
            if (!paused && !countdownFinished) {
                // Calculate remaining time
                var hoursRemaining = Math.floor(totalSeconds / 3600);
                var minutesRemaining = Math.floor((totalSeconds % 3600) / 60);
                var secondsRemaining = totalSeconds % 60;

                // Display remaining time
                countdown.innerHTML = hoursRemaining + 'h ' + minutesRemaining + 'm ' + secondsRemaining + 's';

                // Decrease totalSeconds
                totalSeconds--;

                // Check if countdown has finished
                if (totalSeconds < 0) {
                    clearInterval(intervalId);
                    countdownFinished = true;
                    // Play audio
                    document.getElementById('audio').play();
                    // Hide pause button
                    document.getElementById('pause-button').style.display = 'none';
                    // Show countdown finished message and restart button
                    document.getElementById('countdown-finished').style.display = 'block';
                } else {
                    // Show pause button if countdown is ongoing
                    document.getElementById('pause-button').style.display = 'block';
                }
            }
        }, 1000); // Update every second
    });

    // Pause button functionality
    document.getElementById('pause-button').addEventListener('click', function() {
        paused = !paused;
        if (paused) {
            clearInterval(intervalId);
            this.innerHTML = 'Resume';
        } else {
            // Resume countdown
            intervalId = setInterval(function() {
                if (!paused && !countdownFinished) {
                    // Calculate remaining time
                    var hoursRemaining = Math.floor(totalSeconds / 3600);
                    var minutesRemaining = Math.floor((totalSeconds % 3600) / 60);
                    var secondsRemaining = totalSeconds % 60;

                    // Display remaining time
                    document.getElementById('countdown').innerHTML = hoursRemaining + 'h ' + minutesRemaining + 'm ' + secondsRemaining + 's';

                    // Decrease totalSeconds
                    totalSeconds--;

                    // Check if countdown has finished
                    if (totalSeconds < 0) {
                        clearInterval(intervalId);
                        countdownFinished = true;
                        // Play audio
                        document.getElementById('audio').play();
                        // Hide pause button
                        document.getElementById('pause-button').style.display = 'none';
                        // Show countdown finished message and restart button
                        document.getElementById('countdown-finished').style.display = 'block';
                    }
                }
            }, 1000); // Update every second
            this.innerHTML = 'Pause';
        }
    });

    // Restart button functionality
    document.getElementById('restart-button').addEventListener('click', function() {
        // Reset form
        document.getElementById('countdown-form').reset();
        // Show form
        document.getElementById('countdown-form').style.display = 'block';
        // Hide countdown container and countdown finished message
        document.getElementById('countdown-container').style.display = 'none';
        document.getElementById('countdown-finished').style.display = 'none';
        // Reset countdown state
        paused = false;
        countdownFinished = false;
        // Reset pause button text
        document.getElementById('pause-button').innerHTML = 'Pause';
    });
</script>
</body>
</html>
