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
	unset($_POST);
	empty($_POST);
	$expiretime = 33;
	$timeexpires = date("m/d/Y h:i:s a", time() + $expiretime);
	$timenow = date("m/d/Y h:i:s a", time());
	$name = $_POST['name'];
	$name = strtolower(preg_replace('/\s+/', '', $name));
	$reg = strtolower($_POST['reg']);

	// setup 'default' cache

	$c = new Cache(array(
		'path' => '../cache/sbn/',
		'extension' => '.cache'
	) , $name);
	require("../lib/Db.class.php");
	// store a string
		    $db = new Db();
			$person 	 =     $db->query("SELECT * FROM summoners");
			

	
$trycache = $c->retrieve($name);


			$selector = $db->single("SELECT id FROM summoners WHERE id = :id ", array('id' => $trycache[1][strtolower($name)]["id"] ));
			
			
			$profileimage = $trycache[1][strtolower($name)]["profileIconId"];
			$summname = $trycache[1][strtolower($name)]["name"];
			$slevel = $trycache[1][strtolower($name)]["summonerLevel"];
			$summid = $trycache[1][strtolower($name)]["id"];
			$lolAPI = new lolAPI();
$lolAPI->getRegion('eune');
$champs1 = $lolAPI->summoner($name,'by-name', NULL, $reg);


if ($c->isCached($name)){
echo '<br><br>====================================================================';
echo '<br>DEBUGGER ';
echo '<br>====================================================================';
echo "<pre>";
var_dump($champs1["response"][$name]);
echo "</pre>";
if ($c->isExpired($name) == TRUE)
			{
			$c->store($name, array(
				$timeexpires,
				$c->call_riot("https://$reg.api.pvp.net/api/lol/$reg/v1.4/summoner/by-name/$name?api_key=10c9ecfe-a983-4e2a-aa69-035a78fb28f8")
			) , $expiretime);
			if ($selector > 0){
					//does exist need to update
						$delete	  =  $db->query("DELETE FROM summoners WHERE id = :id",array("id"=>$summid)); 				 
						$insert	 	=  $db->query("INSERT INTO summoners(id,name,level,image) VALUES(:id,:n,:l,:image)",array("id"=>$summid,"n"=>$summname,"l"=>$slevel,"image"=>$profileimage));
						echo "Cache : expired<br>Performing database update record : ".$summid ."";
					}else{
					//need to create a summoner
						$insert	 	=  $db->query("INSERT INTO summoners(id,name,level,image) VALUES(:id,:n,:l,:image)",array("id"=>$summid,"n"=>$summname,"l"=>$slevel,"image"=>$profileimage));
						echo "<br>Performing database insert record : ".$summid."";
			}
			}elseif ($c->isExpired($name) == FALSE){
				echo '<br>Chache didn\'t expired yet - no action to take ';
			}
}else{
echo '<br>isnt soo lets add it';
$c->store($name, array(
				$timeexpires,
				$c->call_riot("https://$reg.api.pvp.net/api/lol/$reg/v1.4/summoner/by-name/$name?api_key=10c9ecfe-a983-4e2a-aa69-035a78fb28f8")
			) , $expiretime);
}
echo '<br>====================================================================';



	$result = $c->retrieve($name);


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

