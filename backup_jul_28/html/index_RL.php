


<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <title>FACE</title>
<meta charset="utf-8">
<style>
body,html{
height:100%;
font-family: Arial, Helvetica, sans-serif;
margin:0;
padding:0;
color:cadetblue;
}
h1{
/*color:orange;*/
font-size=40px;
}
h2{
/*color:orange;*/
font-size=30px;
margin:35px;
}

  </style>
 </head>
 <body style="background-color:white">
<form method="post"action="">
<center>
  <h1>Towards an empathic robot<h1>
<h2>We would like to make the GrownMeUp robot capable of expressing the appropriate emotion during an interaction.
First, we must teach the robot to express appropriate facial expressions... and here, you can make a difference !
</h2>
<br><h2>Please, fill the form below </h2>
  ID (insert the ID provided you): <input type="text" name="id" id="id" required maxlength="5"><br><br>
  Gender (F/M): <input type="text" name="gender" id="gender" required maxlength="1"><br><br>
 Age: <input type="age" name="age" id="age" required maxlength="2"><br><br>
 Nationality: <input type="nation" name="nation" id="nation" required maxlength="15"><br><br>
<br><h2>Click on the button below when you are ready to proceed </h2>
<input type="submit" name="submit" value="Start" onclick="start_teach();">
<br><br><label>* By pressing the button, you agree to give us the consent to process your data for research purposes. </label>
</center>
<img src="growmeup.jpg" alt="robot" style="position: fixed; top:30%; left: 10%;">
</form>
 <script type="text/javascript">

 function start_teach(){
console.log("I am here");
<?php
	$file="results.txt";

	$myfile = fopen($file, "a") or die("Unable to open file!");

	if(!empty($_POST['gender']) and isset($_POST['submit'])){
		$text="ID_".$_POST['id'].";Timestamp_" . date("Y-m-d H:i:s") . ";Gender_".$_POST['gender'].";Age_".$_POST['age'].";Nationality_".$_POST['nation'];

		fwrite($myfile,$text);
		fclose($myfile);

	}
	
	


  ?>

var id = document.getElementById('id').value;
var age = document.getElementById('age').value;
var gender = document.getElementById('gender').value;
var nation = document.getElementById('nation').value;
if(id != "" && age != "" && gender != "" && nation != ""){	
	console.log('hi');	
	var href_location='emotion_RL.html';
	window.open(href_location, ' ');
}		

 }
 </script>
</body>
</html>
