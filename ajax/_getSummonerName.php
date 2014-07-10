<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
?>

    <script type="text/javascript" src="assets/js/jquery.countdown.js"></script>

<?PHP
require_once '../cache.class.php';
require_once '../riot.php';

if (isset($_POST['name']) and isset($_POST['reg']) and !empty($_POST['name']) and !empty($_POST['reg']))
	{
	//unset($_POST);
	//empty($_POST);
	$expiretime = 33;
	
	$timenow = date("m/d/Y h:i:s a", time());
	$name = $_POST['name'];
	$name = strtolower(preg_replace('/\s+/', '', $name));
	$reg = strtolower($_POST['reg']);



	require("../lib/Db.class.php");
	
    $db = new Db();
	$lolAPI = new lolAPI();
	
	$lolAPI->getRegion($reg);
	$champs1 = $lolAPI->summoner($name,'by-name', NULL, $reg);
	
	if($champs1["status"] == "SUCCESS"){

		$setExpire = date("m/d/Y h:i:s a", time() + $expiretime);
		$getID = $champs1["response"][$name]["id"];
		$getName = $champs1["response"][$name]["name"];
		$getLevel = $champs1["response"][$name]["summonerLevel"];
		$getImage = $champs1["response"][$name]["profileIconId"];
		$summonerLookup 	 =     $db->query("SELECT * FROM summoners WHERE id=:id",array("id"=>$getID));
		var_dump($summonerLookup);
		if($summonerLookup[0]["id"] != $getID){
			echo "Summoner is not in databse!";
			//so lets add him
			$insert	 	=  $db->query("INSERT INTO summoners(id,name,level,image,expiredate) VALUES(:id,:n,:l,:image,:expiredate)",array("id"=>$getID,"n"=>$getName,"l"=>$getLevel,"image"=>$getImage,"expiredate"=>$setExpire));
			if($insert > 0){
				echo "added";
			 }else{
				echo "error";
			 }
		}else{
			//update
			//$insert	 	=  $db->query("INSERT INTO summoners(id,name,level,image) VALUES(:id,:n,:l,:image)",array("id"=>$summid,"n"=>$summname,"l"=>$slevel,"image"=>$profileimage));
		}
		//
		//var_dump($champs1);
	}else{
		//failed to connect
		echo $champs1["status"];
	
	}
	
?>

   
<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

?>
<div id="container">
    <img src="assets/img/ad38.png" id="img1">
    <img src='http://ddragon.leagueoflegends.com/cdn/4.11.3/img/profileicon/<?PHP echo $profileimage;?>.png' id="img2">
<p id="lup"><?PHP echo $result[0]; ?></p>
<p id="name"><?PHP echo $summname; ?></p>
<p id="lev"><?PHP echo $slevel; ?></p>
</div>
     
 
   <script type="text/javascript">
      $(function() {
	  $('.countdown.success').hide();
        $('.countdown.callback').countdown({
          date: +(new Date) + <?PHP echo $c->timeDiff($timenow,$result[0])*1000; ?>,
          render: function(data) {
            $(this.el).html(this.leadingZeros(data.min, 2) + " <span>min</span> " + this.leadingZeros(data.sec, 2) + " <span>sec</span>");
          },
          onEnd: function() {
		    $(this.el).remove();
			$('.countdown.success').show();
            $('.countdown.success').text("You can now refresh your data");
          }
        });

      });
    </script>
	 <h2>Refresh your data</h2>
      <div class="countdown callback"></div>
	  <div class="countdown success"></div><pre>
	  <p>Last update : <?php echo $result[0];  echo '<br><b>Page generated in '.$total_time.' seconds.</b>'; ?>

<?PHP
}


?>
</pre>

