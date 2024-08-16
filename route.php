<!DOCTYPE html>
<html>
<head>
    <title>Maps</title>
    <meta charset="utf-8" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script type='text/javascript' src='http://www.bing.com/api/maps/mapcontrol?callback=GetMap&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR' async defer></script>
    <style>
        html, body{
            padding:0;
            margin:0;
            height:100%;
        }

        .directionsContainer{
          position: absolute;
          top:50%;
          padding:10px;
           width:content;
            height:50%;
            overflow-y:auto;
            float:left;
            background:white;
            font-size:20px;
        }

        #myMap{
            position:relative;
            width:calc(100%);
            height:100%;
            float:right;
        }
        #flip{
  position:relative;
  padding: 5px;
  text-align: center;
  background-image: linear-gradient(to right,#05f7a9 ,orange, #7a6bdd);
  border: solid 1px #c3c3c3;
  font-size:30px;
}
    </style>
</head>
<body>
            <?php
  if($_SERVER["REQUEST_METHOD"]=="GET"){
    $lat ="";
    $lon ="";
   $hlat=$_GET['hlat'];
   $hlon=$_GET['hlon'];
   $m="";
   // connect the database with the server
   require "db.php";
   $sql1 = "SELECT lat,lon from alert;";
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
                $m=$lat.",".$lon."+".$m;
              }
            }
            
  }
?>
     <input type="hidden" value="<?php echo $hlat;?>" id="hlat">
     <input type="hidden" value="<?php echo $hlon;?>" id="hlon">
    <input type="hidden" value="<?php echo $m;?>" id="m">
             <div id="flip">Directions</div>
       <div id="myMap"></div>
    <div class="directionsContainer" id="directions">
        <div id="directionsItinerary"></div>
    </div>
    <script>
      $(document).ready(function(){
  $("#flip").click(function(){
    $("#directions").slideToggle("slow");
  });
});
    </script>
    <script type='text/javascript'>
      var map;
      var directionsManager;

      function GetMap()
      {
          map = new Microsoft.Maps.Map('#myMap', {
              center: new Microsoft.Maps.Location(16.508568,80.640193)
          });

          //Load the directions module.
          Microsoft.Maps.loadModule('Microsoft.Maps.Directions', function () {
              //Create an instance of the directions manager.
              directionsManager = new Microsoft.Maps.Directions.DirectionsManager(map);
              var m=document.getElementById("m").value;
            var c = m.split("+");
            for(var i=0;i<c.length;i++){
                if(c[i]==""){
                    break;
                }
               var m1=c[i];
               var d = m1.split(",");
            var  tlat= d[0];
            var  tlon= d[1];
                          var pin1 = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(tlat,tlon), {
            color:'red',
        });
          map.entities.push(pin1);
            }
            var dlat=document.getElementById("hlat").value;
            var dlon=document.getElementById("hlon").value;
                    var pin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(dlat,dlon), {
            color:'blue',
        });
             map.entities.push(pin);
              directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({location: new Microsoft.Maps.Location(dlat,dlon)}));
          directionsManager.addWaypoint(new Microsoft.Maps.Directions.Waypoint({location: new Microsoft.Maps.Location(tlat,tlon)}));
          //Set the request options that avoid highways and uses kilometers.
          directionsManager.setRequestOptions({
              distanceUnit: Microsoft.Maps.Directions.DistanceUnit.km,
              routeAvoidance: [Microsoft.Maps.Directions.RouteAvoidance.avoidLimitedAccessHighway]
          });

              //Specify where to display the route instructions.
              directionsManager.setRenderOptions({ itineraryContainer: '#directionsItinerary' });

              //Specify the where to display the input panel
              directionsManager.showInputPanel('directionsPanel');
          });
      }
  </script>
</body>
</html>