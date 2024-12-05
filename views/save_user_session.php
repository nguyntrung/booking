<?php
    session_start();

    if (isset($_POST['userEmail'])) {
        $_SESSION['userEmail'] = $_POST['userEmail'];
        
        echo 'Session set successfully';
    } else {
        echo 'No email received';
    }
?>
