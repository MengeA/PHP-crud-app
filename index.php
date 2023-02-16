<?php session_start(); ?>
<!DOCTYPE html>
<html background="background.jpg">
   <head>
      <title >Resumes Registry </title>
      <?php require_once "head.php"; ?>
	  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

   </head>
   <body background="background.jpg">
	  <header>
		<p class="site-logo"> Resumes registry</p>
		 <?php
               if ( ! isset($_SESSION["user_id"]) ) { ?>
			   <p> <a href="login.php">Please log in</a> </p>  
			   <?php } else { ?>
         <p class="logout"> <a href="logout.php">Logout</a></p>
         <?php } ?>
	  </header>
	  <h1 class="title-welcome"> Welcome to resumes registry </h1>
      <div class="index-container">
	  
         <p>
            <?php
               if ( ! isset($_SESSION["user_id"]) ) { ?>
          <h2> Please log in to start</h2>
		   
         <?php   require_once "view.php";?>
         <?php } else { ?>
         <?php 	 
            require_once "view.php";?>
         <?php } ?>
         </p>
      </div>
   </body>
 </html>