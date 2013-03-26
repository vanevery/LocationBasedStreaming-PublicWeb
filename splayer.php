<?
	
$google_cloud_messaging_api_key = "YOUR API KEY";  // http://developer.android.com/google/gcm/gs.html
$default_rtmp_url = "rtmp://PUT YOUR SERVER/APPLICATION HERE";	// I use Wowza but FMS or Red5 would work too

// Get the variables passed in for the server and stream	
if (isset($_POST['rtmp']))
{
    $rtmp = $_POST['rtmp'];
}
else if (isset($_GET['rtmp']))
{
    $rtmp = $_GET['rtmp'];
}
else {
	$rtmp = $default_rtmp_url; 
}

if (isset($_POST['stream']))
{
    $stream = $_POST['stream'];
}
else if (isset($_GET['stream']))
{
    $stream = $_GET['stream'];
}
else {
	$stream = "locationbasedstream";
}

// Send GCM message to streamer
$surl = "https://android.googleapis.com/gcm/send";
	
$data['registration_id'] = $stream;
$data['data.hello'] = "Hi";
	
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $surl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization:key=".$google_cloud_messaging_api_key));
curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 

$res = curl_exec($ch);
curl_close($ch);	


?>
<html>
    <head>
        <title>Flash Streaming Video Player</title>
    </head>
    <body>
    	<!-- This is my silly Flash player but any Flash player that does live streams would work -->
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="550" height="400" id="player_stream" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="new_player_stream.swf" />        
			<param name="quality" value="high" />        
			<param name="bgcolor" value="#ffffff" />       
			<param name="flashvars" value="rtmp_url=<?=$rtmp?>&thestream=<?=$stream?>" />   
			<embed src="new_player_stream.swf" quality="high" bgcolor="#ffffff" width="550" height="400" name="player_stream" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="rtmp_url=<?=$rtmp?>&thestream=<?=$stream?>" />
		</object>
		<?= $res ?>
	</body>
</html>
