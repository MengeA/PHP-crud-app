<?php
   require_once "pdo.php";
   require_once "util.php";
   
   session_start();
   
   // Guardian: first_name sure that user_id is present
   if ( ! isset($_REQUEST['profile_id']) ) {
     $_SESSION['error'] = "Missing profile_id";
     header('Location: index.php');
     return;
   }
   
   	$stmt =$pdo->prepare("SELECT * FROM profile WHERE 
   	profile_id =:profile_id 
   	AND user_id = :uid");
   	$stmt->execute(array(
   	'profile_id'=>$_REQUEST['profile_id'],
   	':uid'=>$_SESSION['user_id']));
   	
   
   $profile=$stmt->fetch(PDO::FETCH_ASSOC);
   
   $positions = loadPos($pdo,$_REQUEST['profile_id']);
   $schools = loadEdu($pdo,$_REQUEST['profile_id']);
   
   if( $profile === false){
   	$_SESSION['error'] = "Could not load profile";
   	header('Location: index.php');
   	return;
   }
   ?>
<html>
   <head>
      <title>View</title>
	  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
   </head>
   <?php require_once "head.php" ?>
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
      <div class="view-page">
         <h1> Profile Information </h1>
         <form method="get" action= "view1.php">
            <input type="hidden" name="profile_id" value="<?=htmlentities($_GET['profile_id'])?>"/>
            <p><label>First name: </label>
               <span type="text"><?= htmlentities($profile['first_name'])?>
            </p>
            <p><label>Last name: </label>
               <span type="text"><?= htmlentities($profile['last_name'])?>
            </p>
            <p><label>Email: </label>
               <span type="text"><?= htmlentities($profile['email'])?>
            </p>
            <p><label>Headline: </label>
               <span type="text"><?= htmlentities($profile['headline'])?>
            </p>
            <p><label>Summary: </label>
               <span type="text"><?= htmlentities($profile['summary'])?>
            </p>
            <p><label> Position: </label> </p>
            <ul>
               <?php 
                  $statement = $pdo->prepare("SELECT * FROM position WHERE profile_id = :pid");
                  $statement->execute(array(
                      ":pid" => $profile['profile_id']
                  ));
                  
                  
                  while($position = $statement->fetch(PDO::FETCH_ASSOC)) {
                      echo "<li>".$position['year']." : ".$position['description']."</li>";      
                  }
                  ?>
            </ul>
            <p> <label> Educations: </label></p>
            <ul>
               <?php 
                  foreach ($schools as $school) {
                      $year = htmlentities($school['year']);
                      $name = htmlentities($school['name']);
                      echo('<li>' . $year . ': ' . $name . '</li>');
                  }
                  ?>
            </ul>
            <a href="index.php">Back</a></p>
         </form>
      </div>
   </body>
</html>