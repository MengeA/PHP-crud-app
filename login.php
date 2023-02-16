<?php // Do not put any HTML above this line
   session_start();
   require_once "pdo.php";
   require_once 'util.php';
   $salt = 'XyZzy12*_';
   $hashedValue = hash('md5',$salt.'test');
   
   
   $stored_hash = $hashedValue;  // Pw is test
   $emailPregMatch = "/[a-zA-Z0-9._-]{3,}@[a-zA-Z0-9._-]{3,}[.]{1}[a-zA-Z0-9._-]{2,}/";
   $failure = false;  
   
   if ( isset($_POST["email"]) && isset($_POST["pass"]) ) {
          unset($_SESSION["email"]);  
   
   	if ( isset($_POST['email']) && isset($_POST['pass']) ) {
   		if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
   		$_SESSION['error'] = "Email name and password are required";	
   	}
   		$check = hash('md5', $salt.$_POST['pass']);
   		$stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
   		$stmt->execute (array(':em' =>$_POST['email'], ':pw'=> $check)); 
   		$row = $stmt-> fetch(PDO::FETCH_ASSOC);
   
          if ( $row !== false ) {
              $_SESSION["name"] = $row["name"];
              $_SESSION["user_id"] = $row['user_id'];
              header("Location: index.php?name=".urlencode($_POST['email']));
              return;
          } else {
              $_SESSION["error"] = "Incorrect password.";
              header( 'Location: login.php' ) ;
              return;
          }
      }
   
   
   }
   ?>
<?php 
   if ( isset($_POST['cancel'] ) ) {
       // Redirect the browser to index.php
       header("Location: index.php");
       return;
   }
   
   
   // Fall through into the View
   ?>
<!DOCTYPE html>
<html>
   <head>
      <?php require_once "head.php"; ?>
      <title> Login </title>
	  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
   </head>
   <header>
		<p class="site-logo"> Resumes registry</p>
	  </header>
   <body background="background.jpg">
      <div class="container">
         <h1 class="login"> Login</h1>
         <?php 
            flashMessages();
            ?>
         <form method="post" class=" login-form" >
            <label  for="nam" placeholder="User Name">User Name</label>
            <input class="input input-login"  type="text" name="email" id="email" value = ""><br/>
            <label for="id_1723">Password</label>
            <input class="input input-login" type="password" name="pass" id="id_1723" value = ""><br/>
            <p>
               <input class="login-button" type="submit" onclick="return doValidate();" value="Login">
            </p>
            <a class= "cancel"type="submit" href="index.php"name="cancel" value="Cancel">Cancel</a>
         </form>
         <?php
            flashMessages();
            ?>
         <p>
            <script>
               function doValidate() {
                   console.log('Validating...');
                   try {
                       addr = document.getElementById('email').value;
                       pw = document.getElementById('id_1723').value;
                       console.log("Validating addr="+addr+" pw="+pw);
                       if (addr == null || addr == "" || pw == null || pw == "") {
                           alert("Both fields must be filled out");
                           return false;
                       }
                       if ( addr.indexOf('@') == -1 ) {
                           alert("Invalid email address");
                           return false;
                       }
                       return true;
                   } catch(e) {
                       return false;
                   }
                   return false;
               }
            </script>
         </p>
      </div>
   </body>
</html>