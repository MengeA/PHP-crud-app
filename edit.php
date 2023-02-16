<?php
require_once "pdo.php";
require_once "util.php";

session_start();

 if ( isset($_POST['cancel'] ) ) {
       header("Location: index.php");
       return;
   }
// Guardian: make sure that profile is present
if ( ! isset($_REQUEST['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

	$stmt =$pdo->prepare("SELECT * FROM profile WHERE profile_id =:profile_id AND user_id = :uid");
	$stmt->execute(array(
	'profile_id'=>$_REQUEST['profile_id'],
	':uid'=>$_SESSION['user_id']));
$profile=$stmt->fetch(PDO::FETCH_ASSOC);
if( $profile === false){
	$_SESSION['error'] = "Could not load profile";
	header('Location: index.php');
	return;
}

//handle incoming data
if ( isset($_POST['first_name']) &&
	isset($_POST['last_name'])&& 
	isset($_POST['email']) && 
	isset ($_POST['headline'])
     && isset($_POST['summary']) ) {
		 
	$msg = validateProfile();
	if(is_string($msg)){
		$_SESSION['error'] = $msg;
		header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
		return;
	} 

    $msg = validatePos();
    if (is_string($msg)){
		$_SESSION['error']= $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
	}
	$msg = validateEdu();
	if (is_string($msg)){
		$_SESSION['error']= $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
	}

	$sql = "UPDATE profile SET first_name = :first_name,
								last_name = :last_name,
								email = :email,
								summary= :su,
								headline = :headline
								WHERE profile_id = :profile_id AND user_id=:uid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
			':uid' => $_SESSION['user_id'],
			':first_name' => $_POST['first_name'],
			':last_name' => $_POST['last_name'],
			':email' => $_POST['email'],
			':su' => $_POST['summary'],
			':headline' => $_POST['headline'],
			':profile_id' => $_REQUEST['profile_id']));
	  
	  //clear out the old position entries
	$stmt = $pdo->prepare('DELETE FROM position where profile_id = :profile_id');
	$stmt->execute(array(':profile_id' => $_REQUEST['profile_id']));

	//insert the position entries
	insertPositions($pdo,$_REQUEST['profile_id']);
	
	//clear out the old education entries
	$stmt = $pdo->prepare('DELETE FROM education where profile_id = :profile_id');
	$stmt->execute(array(':profile_id' => $_REQUEST['profile_id']));
	
	//insert the education entries
	insertEdu($pdo,$_REQUEST['profile_id']);
	
	$_SESSION['success'] = " Profile updated";
	header('Location: index.php');
	return;
}

$positions = loadPos($pdo,$_REQUEST['profile_id']);
$schools = loadEdu($pdo,$_REQUEST['profile_id']);
?>

<html>
<head> <title> Edit page</title>
<?php require_once "head.php"?>
 <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body background="background.jpg" >
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
<h1>Editing Profile</h1>

<?php 
flashMessages();
?>

<form method="post" action= "edit.php">
<input type="hidden" name="profile_id" value="<?=htmlentities($_GET['profile_id'])?>"/>
<label for="fname"> First name: </label> <br>
<input type="text" name="first_name" id="fname" value="<?= htmlentities($profile['first_name']) ?>"><br>
<label for="lname"> Last name: </label> <br>
<input type="text" id="lname" name="last_name" value="<?= htmlentities($profile['last_name']) ?>"><br>
<label for="email">Email: </label> <br>
<input type="text" name="email" value="<?= htmlentities($profile['email']) ?>"><br>
<label id="headline" > Headline: </label> <br>
<input type="text" name="headline" value="<?= htmlentities($profile['headline']) ?>"><br>
<label for="summary"> Summary: </label> <br>
<input type="text" id="summary" name="summary" value="<?= htmlentities($profile['summary'])?>"><br>


<?php
$countEdu = 0;
echo (' <label> Education: </label> <input  class="add-remove-buttons" type="submit" id="addEdu" value="+">'."\n");
echo ('<div id="edu_fields">'."</div>\n");
if(count($schools)>0){
	foreach($schools as $school){
		$countEdu ++;
		echo ('<div id="edu'. $countEdu. '">'."\n");
		echo('<p>Year: <input type="text" name= "year'.$countEdu.'"');
		echo('value="'.htmlentities($school['year']).'"\div>');
		echo('<input class="add-remove-buttons"  type="button" value="-"');
		echo ('onclick="$(\'#edu'.$countEdu.'\').remove(); return false;">'." </p>\n");
		echo('<p> School: <input type "text"size"80"name = "edu_school'.$countEdu.'" class="school" value= "'.htmlentities($school['name']).'"\>');
		echo ("\n</div>\n"); 
	}
}
?>

<?php
$pos = 0;
echo('<label>Position: </label><input  class="add-remove-buttons" type="submit" id="addPos" value="+">'. "\n");
echo ('<div id= "position_fields">'."</div>\n");
foreach($positions as $position){
	$pos++;
	echo('<div id="position'.$pos.'">'."\n");
	echo('<p>Year: <input type="text" name= "year'.$pos.'"');
	echo('value="'.htmlentities($position['year']).'"div\>'."\n");
	echo('<input class="add-remove-buttons" type="button"value="-"');
	echo('onclick="$(\'#position'.$pos.'\').remove(); return false;">'."</p>\n");
	
	echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
    echo(htmlentities($position['description'])."\n");
    echo("\n</textarea>\n</\n");
	echo("</p>\n");
}
?>

<script>
countPos = 0;


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
            <input class="add-remove-buttons" type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p><br>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });



countEdu = 0;
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
			 <input class="add-remove-buttons" type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"/><br>\
            <p>School: <input  type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });
});
	
</script>

	<p class="buttons-p">	
		<input class="save-button" type="submit" value="Save" name = "save"  href= "view.php" />
		<input class="cancel-button" type="submit" name="cancel" value="Cancel">
		
	</p>
</form>
</div>
</body>
<html>