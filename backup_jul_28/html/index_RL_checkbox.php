


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
<!-- Click on the button below when you are ready to proceed -->
<p><input type="checkbox" id="terms" onclick="show_start();"> By checking this box, <u>you consent to us processing your data for research purposes.</u></p>
<br><br><input type="submit" id="sbu" name="submit" value="Start" onclick="start_teach();" style="visibility:hidden; width: 80px; height:40px; font-weight:bold;">
</center>
<img src="growmeup.jpg" alt="robot" style="position: fixed; top:30%; left: 10%;">
</form>
 <script type="text/javascript">
 



 function show_start(){
 
 	var check = document.getElementById("terms");
 	var button = document.getElementById('sbu');
 	if(check.checked == true){
 		button.style.visibility = "visible";
 	} else {
 		button.style.visibility = "hidden";
 	}
	
 }
 function start_teach(){

	
		
	<?php
	$file="results.txt";

	$myfile = fopen($file, "a") or die("Unable to open file!");

	if(!empty($_POST['gender']) and isset($_POST['submit'])){
		$text="ID:".$_POST['id'].";Timestamp:" . date("Y-m-d H:i:s") . ";Gender:".$_POST['gender'].";Age:".$_POST['age'].";Nationality:".$_POST['nation']."\n";

		fwrite($myfile,$text);
		fclose($myfile);

	}

  	?>

		var i = document.getElementById('id').value;
		var g = document.getElementById('gender').value;
		var a = document.getElementById('age').value;
		var n = document.getElementById('nation').value;
	
	
		if(i !="" && g!="" && a!="" && n != "" && terms.checked){		
			var href_location='hidden/emotion_RL.php';
			window.open(href_location, ' ');
		}	
		
		
	

	

 }
 </script>
</body>
</html>
