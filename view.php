<?php
   require_once "pdo.php";
   
   
   
   if ( isset($_POST['logout'] ) ) {
       header("Location: index.php");
       return;
   }
   
   
   if ( isset($_SESSION["user_id"]) ) {
     
   
   			$stmt = $pdo->query("SELECT first_name, headline, profile_id FROM profile");
   			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
   
   			?>
<html>
   <head>
      <title>View page</title>
      <?php require_once "head.php"; ?>
	  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
      <style>
         td {padding-right: 10px;}
      </style>
   </head>
   <body>
     
      <?php
         if (empty($rows)){
         	echo ('<h5 style="text-align: center;">No rows found</h5>');
         }
         if ( isset($_REQUEST['email']) ) {
         	echo "<h2>Tracking autos for: ";
         	echo htmlentities($_REQUEST['email']);
         	echo "</h2>\n";
         }
         ?>
      <form method="post">
         <?php
            if ( isset($_SESSION["added"]) ) {
            		echo('<p style="color:green">'.$_SESSION["added"]."</p>\n");
            		unset($_SESSION["added"]);
            	}
            
            
              if ( isset($_SESSION["success"]) ) {
            		echo('<p style="color:green; text-align: center;" >'.$_SESSION["success"]."</p>\n");
            		unset($_SESSION["success"]);
            	}  
             
             ?>
      </form>
      <table>
         <?php
            echo "<tr><th>";
            echo " Name</th>";
            echo "<th> Headline</th>";
            echo "<th> Action</th>";
            foreach ( $rows as $row ) {
            echo "<tr><td>";
            echo('<a href="view1.php?profile_id='.$row['profile_id'].'">'.(htmlentities($row['first_name'])). '</a>');
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td><td>");
            echo('<a class="edit-btn" href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>  ');
            echo('<a class="delete-btn" href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            echo("</td></tr>\n");
            }
            
            ?>
      </table>
      <?php } else { ?>
      <table>
         <?php
            $stmt = $pdo->query("SELECT first_name, headline, profile_id FROM profile");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<tr><th>";
            echo " Name</th>";
            echo "<th> Headline</th></tr>";
            
            foreach ( $rows as $row ) {
            	echo "<tr><td>";
            	echo(htmlentities($row['first_name']));
            	echo("</td><td>");
            	echo(htmlentities($row['headline']));
            	echo("</tr></td>\n");
            	echo("\n");
            } }?>
         <?php
            if ( isset($_SESSION["user_id"]) ) { ?>
         <p class="add-new-button"><a class="add-new-button" href="add.php"  type="submit"  name ="Add" value="Add">Add New Entry</a></p>
         <?php } ?>
      </table>
   </body>
</html>