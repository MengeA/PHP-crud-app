<?php
   require_once "pdo.php";
   require_once "util.php";
   session_start();
   
   
   if (!isset($_SESSION['user_id'])){
   	die("ACCESS DENIED");
   	return;
   }
     
   if ( isset($_POST['cancel'] ) ) {
       header("Location: index.php");
       return;
   }
   ?>
<?php
   if ( isset($_POST['first_name']) 
   	&& isset($_POST['last_name'])
   	&& isset($_POST['email']) 
   	&& isset($_POST['headline']) 
   	&& isset($_POST['summary'])) 
   	{
   		
   	$msg = validateProfile();
   	if(is_string($msg)){
   		$_SESSION['error'] = $msg;
   		header("Location: add.php");
   		return;
   	}
   	
   	$msg = validatePos();
   	if(is_string($msg))
   	{
   		$_SESSION['error'] = $msg;
   		header("Location: add.php");
   		return;
   	}
   	
   	 $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)";
   
           $statement = $pdo->prepare($sql);
   
           $statement->execute(array(
               ":uid" => $_SESSION['user_id'],
               ":fn" => $_POST['first_name'],
               ":ln" => $_POST['last_name'],
               ":em" => $_POST['email'],
               ":he" => $_POST['headline'],
               ":su" => $_POST['summary']
           ));
   
           $profile_id = $pdo->lastInsertId();
   
   		insertPositions($pdo, $profile_id);
   	  
   		insertEdu($pdo,$profile_id);
   
   		
   	
           $_SESSION['success'] = "Profile added";
           header("Location: index.php");
           return;
   
   	}
   	
   
   ?>
<html>
   <head>
      <title>Add page </title>
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
      <div class="add-edit-container">
         <h1> Adding Profile for <?=htmlentities($_SESSION['name']); ?> </h1>
         <?php
            if ( isset($_REQUEST['name']) ) {
                echo "<h2>Adding profile for: ";
                echo htmlentities($_REQUEST['name']);
                echo "</h2>\n";
            }
            flashMessages();
            ?>
         <form method="post">
            <label>First Name: </label><br>
            <input class="input-add" type="text" name="first_name" size="60"><br>
            <label>Last Name:</label><br>
            <input class="input-add"  type="text" name="last_name" size="60"><br>
            <label>Email:</label><br>
            <input class="input-add"  type="text" name="email" size="30"><br>
            <label>Headline:</label><br>
            <input class="input-add" type="text" name="headline" size="80"><br>
            <label>Summary:</label><br>
            <textarea name="summary" rows="8" cols="80"></textarea>
            <br>
            <label>Position:</label><br> 
            <input class="add-remove-buttons" type="submit" id="addPos" value="+"><br>
            <div id="position_fields"></div>
            <label>Education: </label><br>
            <input class="add-remove-buttons"  type="submit" id="addEdu" value="+"></p>
            <div id="edu_fields"></div>
            <p class="buttons-p">
               <input class="add-button"type="submit" value="Add">
               <input class="cancel-button" type="submit" name="cancel" value="Cancel">
            </p>
         </form>
      </div>
      <script>
         countPos = 0;
         countEdu = 0;
         
         // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
         $(document).ready(function(){
             window.console && console.log('Document ready called');
         
             $('#addPos').click(function(event){
                 // http://api.jquery.com/event.preventdefault/
                 event.preventDefault();
                 if ( countPos >= 9 ) {
                     alert("Maximum of nine position entries exceeded");
                     return;
                 }
                 countPos++;
                 window.console && console.log("Adding position "+countPos);
                 $('#position_fields').append(
                     '<div id="position'+countPos+'"> \
                     <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                     <input  class= "add-remove-buttons" type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"><br>\
                     <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                     </div>');
             });
         
             $('#addEdu').click(function(event){
                 event.preventDefault();
                 if ( countEdu >= 9 ) {
                     alert("Maximum of nine education entries exceeded");
                     return;
                 }
                 countEdu++;
                 window.console && console.log("Adding education "+countEdu);
         
                 $('#edu_fields').append(
                     '<div id="edu'+countEdu+'"> \
                     <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
                     <input class="add-remove-buttons"  type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
                     <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
                     </p></div>'
                 );
         
                 $('.school').autocomplete({
                     source: "school.php"
                 });
         
             });
         
         });
         
      </script>
      </div>
   </body>
</html>