<?php
$allowedIps = ['193.136.230.116'];
$userIp = $_SERVER['REMOTE_ADDR'];

if (!in_array($userIp, $allowedIps)) {
    exit('Unauthorized');
}
?>

<html>

 <head>

 
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
	<style>
		body,html{
			/*height:100%;*/
			font-family: Arial, Helvetica, sans-serif;
			margin:0;
			padding:0;
		}
		h1{
			color:white;
			font-size=40px;
		}
		h2{
			color:white; 
			font-size=30px;
			margin:35px;
		}
		.button {
			 background-color: #3399FF;
			 border: none;
			 color: white;
			 padding: 15px 32px;
			 text-align: center;
			 text-decoration: none;
			 display: inline-block;
			 font-size: 16px;
			 font-weight: bold;
			 margin: 4px 2px;
			 cursor: pointer;
		}
		
		#ten-countdown {
			    
			 text-align: center;
			 border: 10px solid #004853;
			 display:inline;
			 padding: 5px;
			 color: white;
			 font-family: Verdana, sans-serif, Arial;
			 font-size: 30px;
			 font-weight: bold;
			 text-decoration: none;
			 margin-top: 15px;
			 position: absolute;
		}
		#face_div {
			border-radius: 125px;
			top: 30px;
			position: relative;
			border: 10px solid #8fecff;
			height: 430px;
			width: 650px;
			margin:0 auto;
		}

  </style>
 </head>
 
 <body style="background-color:black">
 <input id="close_button" type="button" class="button" value="Click me to stop!" onclick="windowClose();">
 <br>
 <div id="ten-countdown"></div>
	<center>
		<h1 style="position: absolute; top: -10px;right: 40%;">The emotion to learn is: </h1><h1 style="position: absolute; top: -10px;right: 29%;" id="h1_e"> </h1>
<!-- dimentsion big canvas: 600(w)*300(h) -->

<div id="face_div">
<canvas id="eye_l_u" width="300" height="150" style="position:relative; left:-40px"> Your browser does not support the HTML5 canvas tag.</canvas>
<canvas id="eye_r_u" width="300" height="150" style="position:relative; "> Your browser does not support the HTML5 canvas tag.</canvas>
<canvas id="eye_l_d" width="300" height="150" style="position:relative; left:-40px"> Your browser does not support the HTML5 canvas tag.</canvas>
<canvas id="eye_r_d" width="300" height="150" style="position:relative; "> Your browser does not support the HTML5 canvas tag.</canvas>
<canvas id="mouth" width="600" height="400" style="position:absolute; left:30px; bottom:-180px;"></canvas>
</div>


	<h1 id="t1" style="margin-left:auto; margin-right:auto; position:relative; bottom:-20px;">Do you think this expression is coherent with the emotion ? 
	<br>
	<div><input id="b1" type="button" class="button" value="Coherent" onclick="send_coherent();" style="margin-right:150px"><input id="b2" class="button" type="button" value="Incoherent" onclick="send_incoherent();"></div></h1>
	
	<h2 style="position: absolute; top: -10;right: 40; color: yellow; margin-right:100px">Expression</h2><h2 id="counter" style="position: absolute; top: -30;right: 40; color: yellow; font-size:50px;"></h2>
	
	</center>
  <!--script src="js/draw_eyes.js"></script-->
  <!--script src="js/draw_mouth.js"></script-->
 
 <script type="text/javascript" type="text/javascript">


 	
 	
	var expression_number = 0;
	var emotions = ['Angry', 'Disgust', 'Fear', 'Happy', 'Neutral', 'Sad', 'Surprise'];
	var eyes_conf=-1;
	var message = "";
	ws = new WebSocket("ws://193.136.230.116:6543/");
	
	
	
	ws.onmessage = function (event) {

		if(event.data == "full"){
			
			alert("Sorry, there are too many active users at the moment. Please try again later.");
			windowClose();
			
		} else {

			message = event;

			document.getElementById("h1_e").innerHTML=emotions[event.data.split(',')[0]];
			
			draw_eye_l_u(event.data.split(',')[1]);
			draw_eye_r_u(event.data.split(',')[1]);
			draw_eye_l_d(event.data.split(',')[1]);
			draw_eye_r_d(event.data.split(',')[1]);
			draw_mouth(event.data.split(',')[2]);
			
			//Print to screen
			document.getElementById('counter').innerHTML =  expression_number;
			expression_number = expression_number + 1;
		
		}
		
	};
    
	function send_coherent(){
		str = message.data;
		console.log(str);
		str = str.concat(",1");
		console.log(str);	
		console.log("Coherent");
		ws.send(str);
		message = "";
	}
	function send_incoherent(){
		str = message.data;
		console.log(str);
		str = str.concat(",0");
		console.log(str);	
		console.log("Incoherent");
		ws.send(str);
		message = "";	
	}
	
	function windowClose() {
		ws.close()


		window.open('','_parent','');
		window.close();

	}
	
	
	//function send_feedback(){
	//	if (document.getElementById("coherent").checked){
	//		ws.send(JSON.stringify({action:'reward', value:'10'}))
	//		document.getElementById("coherent").checked=false
	//	}
		
	//	if (document.getElementById("incoherent").checked){
	//		ws.send(JSON.stringify({action:'reward', value:'2'}))
	//		document.getElementById("incoherent").checked=false
		//SEND message to server
	//	}
	//location.reload();
	//}


///MOUTH AND EYES CONFIGURATION
const radius=10; //original 20

var canvas_eye_lu=document.getElementById("eye_l_u");
var canvas_eye_ru=document.getElementById("eye_r_u");
var canvas_eye_ld=document.getElementById("eye_l_d");
var canvas_eye_rd=document.getElementById("eye_r_d");

function draw_eye_l_u(eyes_conf) {
    var ctx = canvas_eye_lu.getContext("2d");
    ctx.clearRect(0, 0, 300, 150);
    if (eyes_conf==0 || eyes_conf==2){
      ctx.beginPath();
      ctx.arc(90, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(120, 70, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(180, 50, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(240, 70, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(270, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
	}
	else if (eyes_conf==3){
      ctx.beginPath();
      ctx.arc(180, 50, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(240, 70, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(270, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
	}else if(eyes_conf==4){
      ctx.beginPath();
      ctx.arc(270, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
	}else{
        console.log("Eyes not active")
	}
}
function draw_eye_r_u(eyes_conf) {
    var ctx = canvas_eye_ru.getContext("2d");
    ctx.clearRect(0, 0, 300, 150);
    if(eyes_conf==0 || eyes_conf==2){
	  ctx.beginPath();
      ctx.arc(90, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(120, 70, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(180, 50, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(240, 70, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(270, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
        }else if (eyes_conf== 3){
	ctx.beginPath();
      ctx.arc(90, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(120, 70, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
      ctx.beginPath();
      ctx.arc(180, 50, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
       }else if(eyes_conf==4){
	ctx.beginPath();
      ctx.arc(90, 120, radius, 0, 2 * Math.PI);
      ctx.fillStyle="blue"
      ctx.fill();
     }else{
        console.log("Eyes not active")
	} 
}
function draw_eye_l_d(eyes_conf) {
    var crd_ctx = canvas_eye_ld.getContext("2d");
    crd_ctx.clearRect(0, 0, 300, 150);
      if(eyes_conf==1 || eyes_conf==2){
	crd_ctx.beginPath();
        crd_ctx.arc(100, 20, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.beginPath();
        crd_ctx.arc(150, 50, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.beginPath();
        crd_ctx.arc(210, 50, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
	crd_ctx.beginPath();
        crd_ctx.arc(260, 20, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
      }
       else if (eyes_conf==4){
	        crd_ctx.beginPath();
        crd_ctx.arc(210, 50, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.beginPath();
        crd_ctx.arc(260, 20, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
	}else{
        console.log("Eyes not active")
	 }
 } 
  function draw_eye_r_d(eyes_conf) {
    var crd_ctx = canvas_eye_rd.getContext("2d");
    crd_ctx.clearRect(0, 0, 300, 150);
      if(eyes_conf==1 || eyes_conf==2){
        crd_ctx.beginPath();
        crd_ctx.arc(100, 20, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.beginPath();
        crd_ctx.arc(150, 50, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.beginPath();
        crd_ctx.arc(210, 50, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.arc(260, 20, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();

     }else if (eyes_conf==4){
        crd_ctx.beginPath();
        crd_ctx.arc(100, 20, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
        crd_ctx.beginPath();
        crd_ctx.arc(150, 50, radius, 0, 2 * Math.PI);
        crd_ctx.fillStyle="blue"
        crd_ctx.fill();
	}else{
        console.log("Eyes not active")
	 }
}
const color="orange";
const widthLine=10;
var canvas_mouth=document.getElementById("mouth");

function draw_mouth(mouth_conf) {
var ctx_mouth = canvas_mouth.getContext("2d");
    ctx_mouth.clearRect(0, 0, 600, 400);

	console.log(mouth_conf)
	mouth_conf = Number(mouth_conf)
	
	switch(mouth_conf){
	case 0:
	ctx_mouth.beginPath();
        ctx_mouth.arc(15,75,5,0,2*Math.PI)
        ctx_mouth.fillStyle=color;
        ctx_mouth.fill();
        ctx_mouth.beginPath();
        ctx_mouth.arc(540,75,5,0,2*Math.PI)
        ctx_mouth.fillStyle=color;
        ctx_mouth.fill();
	start_width=15
	for(i=1;i<5;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(15*i,90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        	ctx_mouth.beginPath();
		ctx_mouth.arc(480+(15*i),90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
       start_width=18;
        for(i=2;i<30;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,105,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
        for(i=3;i<29;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,130,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
        for(i=4;i<28;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,155,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
	break;
	case 1:
	ctx_mouth.beginPath();
        ctx_mouth.arc(35,155,5,0,2*Math.PI)
        ctx_mouth.fillStyle=color;
        ctx_mouth.fill();
        ctx_mouth.beginPath();
        ctx_mouth.arc(515,155,5,0,2*Math.PI)
        ctx_mouth.fillStyle=color;
        ctx_mouth.fill();
        start_width=15
        for(i=1;i<4;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(20+(15*i),130,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
                ctx_mouth.beginPath()
                ctx_mouth.arc(470+(15*i),130,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
       start_width=18;
        for(i=4;i<28;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,105,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      } 
        for(i=5;i<27;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }

        for(i=6;i<26;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,75,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
	break;
	case 2:
	start_width=18;
        for(i=1;i<33;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,50,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	for (i=1;i<3;i++){
		ctx_mouth.beginPath();
                ctx_mouth.arc(start_width,50+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
                ctx_mouth.beginPath();
                ctx_mouth.arc(576,50+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();

	}
	start_height=125
	start_width_l=0
	start_width_r=594
	for (i=1;i<3;i++){
                for(j=1;j<4;j++){
                	ctx_mouth.beginPath();
                	ctx_mouth.arc(start_width_l+(18*j),start_height,5,0,2*Math.PI)
                	ctx_mouth.fillStyle=color;
                	ctx_mouth.fill();
                        ctx_mouth.beginPath();
                        ctx_mouth.arc(start_width_r-(18*j),start_height,5,0,2*Math.PI)
                        ctx_mouth.fillStyle=color;
                        ctx_mouth.fill();
                        temp_width_left=start_width_l+(18*j);
			temp_width_right=start_width_r-(18*j);
                }
                start_width_l=temp_width_left-18;
		start_width_r=temp_width_right+18;
		start_height+=25;
        }
	start_width=18
	for(i=6;i<28;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,start_height,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	break;
        case 3:
        start_width=18;
        for(i=1;i<33;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
	//Tongue
	start_width_l=380
	start_width_r=550
	start_height=90
	for(i=1;i<3;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_l,start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_r,start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	start_height=140
        for(i=1;i<3;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_l+(18*i),start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_r-(18*i),start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	start_height=190;
	start_width_l=416
	for (i=1;i<4; i++){
		ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_l+(25*i),start_height,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	start_height=90;
        start_width=465;
        for (i=1;i<4; i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width,start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
        break;
	case 4:
	start_width_l=18;
	start_width_r=582
	start_height=25;
	for(i=1;i<6;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_l+(18*i),start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_r-(18*i),start_height+(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	start_width=110
	for (i=1; i< 19; i++){
	        ctx_mouth.beginPath();
                ctx_mouth.arc(start_width+(20*i),185,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	//Tongue
	for(i=1;i<4; i++){
		ctx_mouth.beginPath();
                ctx_mouth.arc(410,185-(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	ctx_mouth.beginPath();
        ctx_mouth.arc(504,110,5,0,2*Math.PI)
        ctx_mouth.fillStyle=color;
        ctx_mouth.fill();
	for(i=1; i<3; i++){
		ctx_mouth.beginPath();
                ctx_mouth.arc(410+(18*i),110-(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
                ctx_mouth.beginPath();
                ctx_mouth.arc(504-(18*i),110-(25*i),5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	break;
	case 5:
        start_width=18;
        start_height=25;
        for (i=1;i<7;i++){
                start_height=start_height+25;
		for(j=1;j<6;j++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width+(18*j),start_height,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
		temp_width=start_width+(18*j);
                }
		start_width=temp_width;
        }
	break;
	case 6:
	start_width=18;
	for(i=1;i<33;i++){
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width*i,90,5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
      }
	break;
	case 7:
	start_height=25;
	end_height=195;
	start_width=250;
	end_width=340;	
	for(i=0;i<5; i++){
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width-(15*i),start_height+(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(end_width+(15*i),start_height+(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width-(15*i),end_height-(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(end_width+(15*i),end_height-(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
	}
	for(i=1;i<6; i++){
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width+(18*i),start_height,5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath(); //internal up
		ctx_mouth.arc(start_width+15 -(12*i),(start_height+5)+(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();		
		ctx_mouth.beginPath(); 
		ctx_mouth.arc(end_width-15+(12*i),(start_height+5)+(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width+(18*i),end_height,5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath(); //internal  down
		ctx_mouth.arc(start_width+15-(12*i),(end_height-5)-(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath(); 
		ctx_mouth.arc(end_width-15+(12*i),(end_height-5)-(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
	}
	break;
	case 8:
        start_width=18;
        for(i=3;i<30;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,75,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
		ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,105,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      } 
        for(i=1;i<32;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
	break;
	case 9:
       start_width=18;
        for(i=3;i<30;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,105,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      } 
        for(i=1;i<32;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
      }
	break;
	case 10:
	start_width=18;
        for(i=1;i<32;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,75,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	start_height=90
	start_width_l=0
	start_width_r=576
	for (i=1;i<3;i++){
                for(j=1;j<4;j++){
                	ctx_mouth.beginPath();
                	ctx_mouth.arc(start_width_l+(18*j),start_height,5,0,2*Math.PI)
                	ctx_mouth.fillStyle=color;
                	ctx_mouth.fill();
                        ctx_mouth.beginPath();
                        ctx_mouth.arc(start_width_r-(18*j),start_height,5,0,2*Math.PI)
                        ctx_mouth.fillStyle=color;
                        ctx_mouth.fill();
                        temp_width_left=start_width_l+(18*j);
			temp_width_right=start_width_r-(18*j);
                }
                start_width_l=temp_width_left-18;
		start_width_r=temp_width_right+18;
		start_height+=25;
        }
	start_width=18
	for(i=4;i<29;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,start_height,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	for(i=4;i<22;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width_l+(18*i),90,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	break;
	case 11:
       	start_width=18;
       	for(i=2;i<31;i++){
       	         ctx_mouth.beginPath();
               ctx_mouth.arc(start_width*i,75,5,0,2*Math.PI)
                ctx_mouth.fillStyle="orange";
                ctx_mouth.fill();
      	} 
        for(i=1;i<32;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,90,5,0,2*Math.PI)
                ctx_mouth.fillStyle="orange";
                ctx_mouth.fill();
      	}
     	for(i=6;i<27;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,130,5,0,2*Math.PI)
                ctx_mouth.fillStyle="orange";
                ctx_mouth.fill();
      	} 
	break;
	case 12:
	start_height=75;
	end_height=155;
	start_width=250;
	end_width=304;	
	for(i=0;i<3; i++){
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width-(15*i),start_height+(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(end_width+(15*i),start_height+(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width-(15*i),end_height-(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(end_width+(15*i),end_height-(18*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
	}
	for(i=1;i<3; i++){
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width+(18*i),start_height,5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath(); //internal up
		ctx_mouth.arc(start_width+15 -(12*i),(start_height+5)+(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();		
		ctx_mouth.beginPath(); 
		ctx_mouth.arc(end_width-15+(12*i),(start_height+5)+(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath();
		ctx_mouth.arc(start_width+(18*i),end_height,5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath(); //internal  down
		ctx_mouth.arc(start_width+15-(12*i),(end_height-5)-(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
		ctx_mouth.beginPath(); 
		ctx_mouth.arc(end_width-15+(12*i),(end_height-5)-(15*i),5,0,2*Math.PI)
        	ctx_mouth.fillStyle=color;
        	ctx_mouth.fill();
	}
	break;
	case 13:
	start_width=18; //smaller width 28
        for(i=1;i<32;i++){ //smaller width 19
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,75,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	start_height=90;
	start_width_l=0;
	start_width_r=576; //smaller width 536
	for (i=1;i<4;i++){
                for(j=1;j<3;j++){
                	ctx_mouth.beginPath();
                	ctx_mouth.arc(start_width_l+(18*j),start_height,5,0,2*Math.PI) //smaller width 28
                	ctx_mouth.fillStyle=color;
                	ctx_mouth.fill();
                        ctx_mouth.beginPath();
                        ctx_mouth.arc(start_width_r-(18*j),start_height,5,0,2*Math.PI) //smaller width 28
                        ctx_mouth.fillStyle=color;
                        ctx_mouth.fill();
                        temp_width_left=start_width_l+(18*j); //smaller width 28
			temp_width_right=start_width_r-(18*j); //smaller width 28
                }
                start_width_l=temp_width_left-18; //smaller width 28
		start_width_r=temp_width_right+18; //smaller width 28
		start_height+=25;
        }
	start_width=18; //smaller width 28
	for(i=4;i<29;i++){ //smaller width 4 and 16
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,start_height,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	break;
	case 14:
	start_width=18;
        for(i=1;i<32;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,75,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
	}
	start_height=90
	start_width_l=0
	start_width_r=576
	for (i=1;i<3;i++){
                for(j=1;j<4;j++){
                	ctx_mouth.beginPath();
                	ctx_mouth.arc(start_width_l+(18*j),start_height,5,0,2*Math.PI)
                	ctx_mouth.fillStyle=color;
                	ctx_mouth.fill();
                        ctx_mouth.beginPath();
                        ctx_mouth.arc(start_width_r-(18*j),start_height,5,0,2*Math.PI)
                        ctx_mouth.fillStyle=color;
                        ctx_mouth.fill();
                        temp_width_left=start_width_l+(18*j);
			temp_width_right=start_width_r-(18*j);
                }
                start_width_l=temp_width_left-18;
		start_width_r=temp_width_right+18;
		start_height+=25;
        }
	start_width=18
	for(i=4;i<29;i++){
                ctx_mouth.beginPath();
                ctx_mouth.arc(start_width*i,start_height,5,0,2*Math.PI)
                ctx_mouth.fillStyle=color;
                ctx_mouth.fill();
        }
	break;
	default:
        console.log("Mouth not active");
	}
}

function countdown( elementName, minutes, seconds )
{
    var element, endTime, hours, mins, msLeft, time;

    function twoDigits( n )
    {
        return (n <= 9 ? "0" + n : n);
    }

    function updateTimer()
    {
        msLeft = endTime - (+new Date);
        if ( msLeft < 1000 ) {
            element.innerHTML = "Time is up!";
            document.getElementById('close_button').style.backgroundColor = "#009900";
            document.getElementById('b1').style.visibility = "hidden";
            document.getElementById('b2').style.visibility = "hidden";
            document.getElementById('t1').style.visibility = "hidden";
            alert("Thank you !! This was the last request.\n Click on the button on the left side to exit from this page.");
        } else {
            time = new Date( msLeft );
            hours = time.getUTCHours();
            mins = time.getUTCMinutes();
            element.innerHTML = (hours ? hours + ':' + twoDigits( mins ) : mins) + ':' + twoDigits( time.getUTCSeconds() );
            setTimeout( updateTimer, time.getUTCMilliseconds() + 500 );
        }
    }

    element = document.getElementById( elementName );
    endTime = (+new Date) + 1000 * (60*minutes + seconds) + 500;
    updateTimer();
}

countdown( "ten-countdown", 10, 0 );
 </script>

</body>

</html>
