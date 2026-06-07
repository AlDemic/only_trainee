<?php 
    //SESSION GLOBALS for notification
    $msgOk = $_SESSION['msg'] ?? '';
    unset($_SESSION['msg']);

    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);

?>

<div class="msgBlock">
    <!-- OK msg -->
    <?php if($msgOk) echo "<p style='color:green'>$msgOk</p>"; ?>

    <!-- ERROR msg -->
    <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
</div>