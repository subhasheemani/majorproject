

<!DOCTYPE html>
<html>
<head>
    <title>Maps</title>
    <meta charset="utf-8" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?callback=GetMap&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR' async defer></script>
     <script lang="javascript">
                if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
                //alert("Latitude: " + position.coords.latitude + 
            //"<br>Longitude: " + position.coords.longitude);
            } else { 
                alert("Geolocation is not supported by this browser.");
            }
        
        function showPosition(position) {
            document.getElementById("lat").value=position.coords.latitude;
            document.getElementById("lon").value= position.coords.longitude;
          }
    </script>
    <style>
        html, body{
            padding:0;
            margin:0;
            height:100%;
        }


        #myMap{
            position:relative;
            width:calc(100%);
            height:100%;
            float:right;
        }
             .directionsContainer{
          position: absolute;
          top:40%;
          padding:10px;
           width:350px;
            height:content;
            left:1%;
            border-radius:20px;
            overflow-y:auto;
            float:left;
            background:white;
            font-size:20px;
            display:none;
        }
        directionsItinerary{
            position:relative;
        }
        img{
            position:relative;
            display:none;
            border-radius:10px;
        }
       .directionsContainer button{
            position:relative;
            margin-left:70%;
            font-size:20px;
            display:none;
            color:#fff;
          background:red;
          border-radius:10px;
        }
    </style>
</head>
<body>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the required files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

  if($_SERVER["REQUEST_METHOD"]=="GET"){
   $lat1="";
   $lon1="";
   $h_name="";
   $email="";
   $lon="";
   $lat="";
   $s=""; $m="";$t="";$p="";$i="";
  require "db.php"; 
    if ($conn -> connect_errno)
    {
       echo "Failed to connect to MySQL: " . $conn -> connect_error;
       exit();
    }
    else{
        $today = date("Y-m-d");
          $sql1 = "SELECT lat,lon from alert WHERE DATE(`timestamp`) = '$today'";
    $result = ($conn->query($sql1));
    //declare array to store the data of database
  
    if ($result->num_rows > 0) 
    {
        // fetch all data from db into array 
        $row = $result->fetch_all(MYSQLI_ASSOC);  
    }  
               if(!empty($row))
               foreach($row as $rows)
              { 
                if($rows['lat']!=NULL){
                $lat=$rows['lat'];
                $lon=$rows['lon'];
              }
            }
          
                $sql = "SELECT h_name,email,lat,lon,h_img, ABS( lon - '$lon' ) AS distance FROM hospitals ORDER BY distance LIMIT 3;";
    $result = ($conn->query($sql));
    //declare array to store the data of database
  
    if ($result->num_rows > 0) 
    {
        // fetch all data from db into array 
        $row = $result->fetch_all(MYSQLI_ASSOC);  
    }  
               if(!empty($row))
               foreach($row as $rows)
              { 
                if($rows['lat']!=NULL){
                $lat1=$rows['lat'];
                $lon1=$rows['lon'];
                $h_name=$rows['h_name'];
                $i=$rows['h_img']."+".$i;
                $m=$h_name."+".$m;
                $s=$lat1."+".$s;
                $t=$lon1."+".$t;
                 if ($rows['email'] != NULL) {
                $email = $rows['email'];

                // Sending an email using PHPMailer
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                     $mail->isSMTP();
                     $mail->Host       = 'smtp.gmail.com';
                     $mail->SMTPAuth   = true;
                     $mail->Username   = '201fa04327teja@gmail.com'; // Your Gmail email address
                     $mail->Password   = 'yiuvdltajxzqdfmp';  // Your Gmail password
                     $mail->SMTPSecure = 'tls';
                     $mail->Port       = 587;

                    //Recipients
                    $mail->setFrom('201fa04327teja@gmail.com', 'Emergency');
                    $mail->addAddress($email, 'Recipient Name');

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Emergency';
                    $mail->Body = 'Visit the below link to spot the Emergency Area: <a href="https://mevn.in/apps/qis/exit/safety.php">Emergency Spot</a>';
                    $mail->isHTML(true);

                    $mail->send();
                   // echo 'Email has been sent!';
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
              }
            }
            }
  }  
?>
    <input type="hidden" value="<?php echo $m;?>" id="m">
     <input type="hidden" value="<?php echo $s;?>" id="s">
       <input type="hidden" value="<?php echo $t;?>" id="t">
          <input type="hidden" value="<?php echo $p;?>" id="p">
          <input type="hidden" value="<?php echo $i;?>" id="i">
          <input type="hidden" value="<?php echo $lat;?>" id="alat">
          <input type="hidden" value="<?php echo $lon;?>" id="alon">
       <div id="myMap"></div>
           <div class="directionsContainer" id="directions"><button id="b" onclick="show()">X</button><br>
        <img src="" id="img" width="250" height="250">
        <form action="route.php">
     <input type="hidden" name="hlat" id="hlat">
     <input type="hidden" name="hlon" id="hlon">
     <input type="submit" value="VIEW ROUTE">
 </form> 
        <div id="directionsItinerary"></div>
    </div>
    <script>
      $(document).ready(function(){
  $("#flip").click(function(){
    $("#directions").slideToggle("slow");
  });
});
 function show(){
          document.getElementById("directions").style.display="none";
          document.getElementById("img").style.display="none";
         document.getElementById("b").style.display="none";   
}
    </script>
	<script type='text/javascript'>
    var map, infobox;

    function GetMap() {
        map = new Microsoft.Maps.Map('#myMap', {});

        //Create an infobox at the center of the map but don't show it.
        infobox = new Microsoft.Maps.Infobox(map.getCenter(), {
            visible: false
        });

        //Assign the infobox to a map instance.
        infobox.setMap(map);

            //var pin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(16.246661,80.644785));
            var s=document.getElementById("s").value;
           var m=document.getElementById("m").value;
            var t=document.getElementById("t").value;
            var p=document.getElementById("p").value;
            var im=document.getElementById("i").value;
            var a = s.split("+");
            var b=t.split("+");
            var c=m.split("+");
            var d=p.split("+");
            var e=im.split("+");
           var alat=document.getElementById("alat").value;
           var alon=document.getElementById("alon").value;
            var pin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(alat,alon), {
            color:'red',
        });
         map.entities.push(pin);
        
            for(var i=0;i<a.length;i++){
                if(a[i]==""){
                    break;
                }
                    var pin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(a[i],b[i]), {
            color:'blue',
            title:c[i],
            text:i,
        });
            //Store some metadata with the pushpin.
            pin.metadata = {
                title: c[i],
              //  description:'<b>Phone No: </b>'+d[i],
              latt:a[i],
              lonn:b[i],
                img:e[i]
            };

            //Add a click event handler to the pushpin.
            Microsoft.Maps.Events.addHandler(pin, 'click', pushpinClicked);

         map.entities.push(pin);
             Microsoft.Maps.Events.addHandler(pin, 'mouseover',pushpinClicked);

        /*Microsoft.Maps.Events.addHandler(pin, 'mousedown', function (e) {
            e.target.setOptions({ color: "red" });
        });

        Microsoft.Maps.Events.addHandler(pin, 'mouseout', function (e) {
            e.target.setOptions({ color:"blue" });
        });*/
        }
    }

    function pushpinClicked(e) {
        //Make sure the infobox has metadata to display.
        if (e.target.metadata) {
            infobox.setOptions({
                location: e.target.getLocation(),
                title: e.target.metadata.title,
             //   description: e.target.metadata.description,
                latt:e.target.metadata.latt,
                lonn:e.target.metadata.lonn,
                visible: true,
            });
        document.getElementById("directionsItinerary").innerHTML="<h2>"+e.target.metadata.title+"</h2>"+"<h4>"+e.target.metadata.latt+","+e.target.metadata.lonn+"</h4>";
         document.getElementById("hlat").value=e.target.metadata.latt;
         document.getElementById("hlon").value=e.target.metadata.lonn;
         document.getElementById("img").src="./image/"+e.target.metadata.img;
        document.getElementById("directions").style.display="block";
          document.getElementById("img").style.display="block";
         document.getElementById("b").style.display="block";
        }
    }
    </script>
</body>
</html>
