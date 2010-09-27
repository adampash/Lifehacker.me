<?php require_once('conf/config.php') ?>
<?php
/*** EVERYBODY FUNCTIONS ***/

// Curl helper function
function curl_get($url)
{
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	$return = curl_exec($curl);
	curl_close($curl);
	return $return;
}

/*** FLICKR ***/
// The Flickr gallery is handled through an open-source, free JavaScript called Galleria.  For more information on the license, etc., check out the docs folder in this package.
if (isset($accounts['flickr']['username']) && isset($accounts['flickr']['apikey']) && $accounts['flickr']['apikey'] != '' && $accounts['flickr']['username'] != '')
{ 
	$flickr_on = true;
}

/*** VIMEO ***/
if (isset($accounts['vimeo']['username']) && $accounts['vimeo']['username'] != '')
{
	$video_bubble = true;
	$vimeo_on = true;
	$api_endpoint = 'http://www.vimeo.com/api/v2/'.$accounts['vimeo']['username'];
	$vimeo_user = simplexml_load_string(curl_get($api_endpoint.'/info.xml'));
	$vimeo_videos = simplexml_load_string(curl_get($api_endpoint.'/videos.xml'));
}

/*** YOUTUBE ***/
if (isset($accounts['youtube']['username']) && $accounts['youtube']['username'] != '')
{
	$video_bubble = true;
	$youtube_on = true;
	$youtube_rss_feed = 'http://gdata.youtube.com/feeds/api/users/'.$accounts['youtube']['username'].'/uploads?v=2';
	$youtube_simple_xml = simplexml_load_file($youtube_rss_feed);
	/*echo '<pre>';
	print_r($youtube_simple_xml);
	echo '</pre>';*/
}

/*** TWITTER ***/
if (isset($accounts['twitter']['username']) && $accounts['twitter']['username'] != '')
{
	$twitter_on = true;
	$twitter_xml_feed = 'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name='.$accounts['twitter']['username'] . '&include_rts=' . $accounts['twitter']['rts'];
	$twitter_simple_xml = simplexml_load_file($twitter_xml_feed);
	$twitter_status_feed = $twitter_simple_xml->status;
}

/*** FACEBOOK ***/


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name']);} ?> <?php if (isset($general['last_name']) && $general['last_name'] != '') {echo strtolower($general['last_name']);} ?></title>
	<meta http-equiv="Content-category" content="text/html; charset=ISO-8859-1" />
	<link href="css/splash.css" rel="stylesheet" category="text/css" />
    <link rel="icon" type="image/vnd.microsoft.icon" href="/favicon.ico" /> 
	<link rel="SHORTCUT ICON" href="/favicon.ico" />
	<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
	<?php if (isset($accounts['flickr']['username']) && isset($accounts['flickr']['apikey']) && $accounts['flickr']['apikey'] != '' && $accounts['flickr']['username'] != '')
	{
	?>
	
	<script type="text/javascript" src="js/galleria.js"></script>
	<script type="text/javascript" src="js/plugins/galleria.flickr.js"></script> 
	<script>
	    // Load theme
	    Galleria.loadTheme('js/themes/lightbox/galleria.lightbox.js');
	</script>
	<?php } ?>
	
	<?php if (isset($accounts['vimeo']['username']) || isset($accounts['youtube']['username']))
	{
	?>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function(){
			$("a[rel^='prettyPhoto']").prettyPhoto();
		});
	</script>
	<script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
	<?php } ?>
	
	<script type="text/javascript" src="js/switches.js"></script>
	<script type="text/javascript">
		//document.getElementById('nav').style = 'display: none;';
	</script>
	<style>
		body
		{
			
			<?php if(isset($visual_style['background_image']) && $visual_style['background_image'] != '') {echo 'background-image: url('.$visual_style['background_image'].');';} ?>
			
		}
		div#nav, div#nav a
		{
			
			<?php if(isset($visual_style['navigation_color']) && $visual_style['navigation_color'] != '') {
				echo('color: '.$visual_style['navigation_color'].';');
				} ?>
		
			<?php if(isset($visual_style['navigation_shadows']) && $visual_style['navigation_shadows'] != '') {
				echo 'text-shadow:0px 0px 6px #666';
				} ?>
			
		}
	</style>
</head>
<body>
	<div id="nav">
		<h1>
		<?php if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name']);} ?> <?php if (isset($general['last_name']) && $general['last_name'] != '') {echo strtolower($general['last_name']);} ?>
		</h1>
		<div id="elements">
			<ol>
				<li><a href="javascript:switchto('about');" id="nav_about">about</a></li>
				<?php if ($flickr_on == true) { ?><li><a href="javascript:switchto('photos');" id="nav_photos">photos</a></li><?php } ?>
				<?php if ($video_bubble == true) { ?><li><a href="javascript:switchto('videos');" id="nav_videos">videos</a></li><?php } ?>
				<?php if ($twitter_on == true) { ?><li><a href="javascript:switchto('twitter');" id="nav_twitter">twitter</a></li><?php } ?>
			</ol>
		</div>
	</div>
	
	<div id="triangle">
		<img src="images/bubble_triangle_100.png" width="30" height="15" />
	</div>
	
	<div id="about" class="content_bubble">
		<h3>about</h3>
		<p><?php echo $general['about_me']; ?></p>
	</div>
	
	<div id="photos" class="content_bubble">
		<h3><?php if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name'])."'s ";} ?>photos</h3>
		<?php if ($flickr_on == true) { ?>
		<p>
			<div id="galleria">Loading...</div> 
			<script>
                // Flickr init
                var api_key = <?php echo '\''.$accounts['flickr']['apikey'].'\'' ?>;
                var flickr = new Galleria.Flickr(api_key);
                // Get my photostream
                flickr.setOptions({
                    size: 'large', max: 25, sort: 'date-posted-desc'
                }).getUser('<?php echo $accounts['flickr']['username'] ?>', function(data) {
                    $('#galleria').galleria({
                        data_source: data,
                        debug: true
                    });
                });
				document.getElementById('photos').style.display = 'none';
            </script>
		</p>	
		<p id="more">
			<a href="http://flickr.com/photos/<?php echo $accounts['flickr']['username'] ?>">More...</a>
		</p>
		<?php } ?>
	</div>
	
	<div id="videos" class="content_bubble">
		<?php if ($video_bubble == true) { ?>
		<h3><?php if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name'])."'s ";} ?>videos</h3>
		<?php
		if (isset($general['about_videos']) && $general['about_videos'] != '')
		{
			echo '<p>'.$general['about_videos'].'</p>';
		}
		?>
		<p>
			<?php if ($vimeo_on == true) { ?>
			<!-- Vimeo -->
			<div id="vimeo_videos">
				<?php foreach ($vimeo_videos->video as $video): ?>
	            <a href="<?php echo $video->url ?>&width=640" rel="prettyPhoto" title="<?php echo $video->title ?>"><img src="<?php echo $video->thumbnail_small ?>" width="120" height="90" /></a>
				<?php endforeach; ?>
			</div>
			<?php } ?>
			
			<?php if ($youtube_on == true) { ?>
			<!-- YouTube -->
			<div id="youtube_videos">
				<?php
				// iterate over entries in feed
				foreach ($youtube_simple_xml->entry as $entry)
				{
					// Namespace info...
					$media = $entry->children('http://search.yahoo.com/mrss/');

					// Get the video URL...
					$attrs = $media->group->player->attributes();
					$video_url = $attrs['url'];
					$video_title = $media->group->title; 

					// Get the video thumbnail...
					$attrs = $media->group->thumbnail[0]->attributes();
					$thumbnail = $attrs['url'];
					
					echo '<a href="'.$video_url.'&width=640" rel="prettyPhoto" title="'.$video_title.'"><img src="'.$thumbnail.'" width="120" height="90" /></a>';
				}
				?>
			</div>
			<?php } ?>
		</p>
		<?php } ?>
	</div>
	
	<div id="twitter" class="content_bubble">
		<h3><?php if (isset($general['first_name']) && $general['first_name'] != '') {echo strtolower($general['first_name'])."'s ";} ?>tweets</h3>

		<p>
			<div id ="twitter_feed">
				<?php if ($twitter_on == true) { ?>
				<?php
				foreach ($twitter_simple_xml->status as $tweet)
				{
					if ($accounts['twitter']['@'] == 'true' || substr($tweet->text,0,1) != "@") echo '<p class="tweet"><img src="'.$tweet->user->profile_image_url.'" style="float: left; margin: 0 8px 8px 0;" />'.$tweet->text.'<br /><span style="font-size: 10px; font-style: italic;">'.$tweet->created_at.'</span></p><hr />';
				}
				?>
				<p id="more">
					<a href="http://twitter.com/<?php echo $accounts['twitter']['username'] ?>">More...</a>
				</p>
				<?php } ?>
			</div>
		</p>
	</div>
	
	<div id="footer">
		Lifehacker.me by <a href="http://lifehacker.com">Lifehacker</a>.
	</div>
	
	<div id="spacer" style="padding-bottom: 12px; float: right; clear: both;">&nbsp;</div>
	
</body>
</html>
