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
	$setExpire = date("Y/m/d H:i:s", time() + $expiretime);
	$lastUpdate = date("Y/m/d H:i:s", time());

	//expire->insert/update
	require("../lib/Db.class.php");
	
    $db = new Db();
	$lolAPI = new lolAPI();
	$c = new Cache(array(
		'path' => '../cache/sbn/',
		'extension' => '.cache'
	) , $name);
	
	function draw_output($getImage,$expireCheck,$getName,$getLevel){
    echo '<div id="container">
		  <img src="assets/img/ad38.png" id="img1">
		  <img src="http://ddragon.leagueoflegends.com/cdn/4.11.3/img/profileicon/'.$getImage.'.png" id="img2">
	      <p id="lup">'.$expireCheck.'</p>
		  <p id="name">'.$getName.'</p>
		  <p id="lev">'.$getLevel.'</p>
		  </div>';
}

$trycache = $c->retrieve($name);
$champs1 = $lolAPI->summoner($name,'by-name', NULL, $reg);
//var_dump($trycache);
	//var_dump($trycache);
	$lolAPI->getRegion($reg);
	//////////////////////////////
	//////////////////////////////
	//////////////////////////////
	//////////////////////////////
	if($trycache != NULL){
		
		$getID = $trycache[0]["response"][$name]["id"];
		$getName = $trycache[0]["response"][$name]["name"];
		$getLevel = $trycache[0]["response"][$name]["summonerLevel"];
		$getImage = $trycache[0]["response"][$name]["profileIconId"];
		$expireCheck 	 =     $db->query("SELECT expiredate FROM summoners WHERE id=:id",array("id"=>$getID));
		draw_output($getImage,$expireCheck[0]["expiredate"],$getName,$getLevel);
		if ($c->isExpired($name) == TRUE){
		$c->store($name, array(
						$champs1
			) , $expiretime);
						//update required {+future cache}
				//echo "Not fresh data";
			//expired handler
			$expireCheck 	 =     $db->query("SELECT expiredate FROM summoners WHERE id=:id",array("id"=>$getID));
				//var_dump($lolAPI->timeDiff($timenow,$expireCheck[0]["expiredate"]));
			//another check for expired but this time for database
			if($lolAPI->timeDiff($timenow,$expireCheck[0]["expiredate"]) < 0){
				echo "<br>[[Dual layer expired check :: for db now]]";
				$update   =  $db->query("UPDATE summoners SET name=:n,level=:l,image=:image,expiredate=:expiredate WHERE id = :id", array("id"=>$getID,"n"=>$getName,"l"=>$getLevel,"image"=>$getImage,"expiredate"=>$setExpire));
				//return the result of update
				if($update > 0){
					echo "<br>[[Updated!]]";
				 }else{
					echo "failed to update/not required";
				 }
			}else{
				echo "<br>[[Performing record update :: No actions to take]]";
			}
		}
		
	}else{
		$champs1 = $lolAPI->summoner($name,'by-name', NULL, $reg);
		
		if($champs1["status"] == "SUCCESS"){

		$getID = $champs1["response"][$name]["id"];
		$getName = $champs1["response"][$name]["name"];
		$getLevel = $champs1["response"][$name]["summonerLevel"];
		$getImage = $champs1["response"][$name]["profileIconId"];
		$summonerLookup 	 =     $db->query("SELECT * FROM summoners WHERE id=:id",array("id"=>$getID));
		//var_dump($champs1);

		if(empty($summonerLookup) || $summonerLookup[0]["id"] != $getID){
		echo "<br>[[Summoner is not in databse!]]";
			//so lets add him
			//perform insert
			$insert	 	=  $db->query("INSERT INTO summoners(id,name,level,image,expiredate) VALUES(:id,:n,:l,:image,:expiredate)",array("id"=>$getID,"n"=>$getName,"l"=>$getLevel,"image"=>$getImage,"expiredate"=>$setExpire));
			//check the success in adding
			if($insert > 0){
				echo "<br>[[ADD : added!]]";
			 }else{
				echo "<br>failed to insert";
			 }
		echo "<created>";
		}
		$expireCheck 	 =     $db->query("SELECT expiredate FROM summoners WHERE id=:id",array("id"=>$getID));
		
		$c->store($name, array(
						$champs1
			) , $expiretime);
		draw_output($getImage,$lastUpdate,$getName,$getLevel);
		}else{
			//error !=Success
			echo "There was an error while finding your summoner";
	}
	}

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);

?>

     
   <script type="text/javascript">
      $(function() {
	  $('.countdown.success').hide();
        $('.countdown.callback').countdown({
          date: +(new Date) + <?PHP echo $lolAPI->timeDiff($timenow,$expireCheck[0]["expiredate"])*1000; ?>,
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
	  <div class="countdown success"></div>
<pre>
	  <p>Last update : <?php echo $expireCheck[0]["expiredate"];  echo '<br><b>Page generated in '.$total_time.' seconds.</b>'; ?>

<?PHP
}
?>
</pre>

