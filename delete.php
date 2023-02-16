<?php
   require_once "pdo.php";
   session_start();
   
    if ( isset($_POST['cancel'] ) ) {
          header("Location: index.php");
          return;
      }
      
   if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
       $sql = "DELETE FROM profile WHERE profile_id = :profile_id";
       $stmt = $pdo->prepare($sql);
       $stmt->execute(array(':profile_id' => $_POST['profile_id']));
       $_SESSION['success'] = 'Record deleted';
       header( 'Location: index.php' ) ;
       return;
   }
   
   // Guardian: first_name sure that user_id is present
   if ( ! isset($_GET['profile_id']) ) {
     $_SESSION['error'] = "Missing profile_id";
     header('Location: view.php');
     return;
   }
   
   $stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM profile where profile_id = :profile_id");
   $stmt->execute(array(":profile_id" => $_GET['profile_id']));
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   if ( $row === false ) {
       $_SESSION['error'] = 'Bad value for profile_id';
       header( 'Location: view.php' ) ;
       return;
   }
   
   ?>
<html>
   <head>
      <title>Delete Confirmation </title>
      <?php require_once "head.php"; ?>
	  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
   </head>
   <body background="background.jpg">
   <header>
		<p class="site-logo"> Resumes registry</p>
		 <?php
               if ( ! isset($_SESSION["user_id"]) ) { ?>
			   <p> <a href="login.php">Please log in</a> to start.</p>  
			   <?php } else { ?>
         <p class="logout"> <a href="logout.php">Logout</a></p>
         <?php } ?>
	  </header>
      <div class="delete-window">
         <h3>Confirm deletion</h3>
         <p> First Name: <?= htmlentities($row['first_name']) ?></p>
         <p>Last Name:  <?= htmlentities($row['last_name']) ?></p>
         <form method="post">
            <input  type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
            <input class = "delete-button" type="submit" value="Delete" name="delete">
            <input class="cancel-button" type="submit" name="cancel" value="Cancel">
      </div>
   </body>
   </form>
</html>