<?php
	// permsBlurb: Consumes an optional name, and returns the permissions blurb using that name.
	function permsBlurb($names=NULL) {
		if ($names != NULL) {
			echo "All materials are presented here with the express permission of their respective copyright owners and " . $names . ".  Materials may have been edited to remove content for which explicit permission has not been granted."; 
		} else if ($names == NULL) { // If no name is given, special case format.
			echo "All materials are presented here with the express permission of their respective copyright owners.  Materials may have been edited to remove content for which explicit permission has not been granted."; 
		}
	}

	// newStamp: Shorthand for a stamp in the new-stamp class, for marking new collections.
	function newStamp() {
		echo "
        <div class=\"new-stamp\"><img src=\"/images/index-splashes/new.png\" /></div>" ;
	}
	
	// audioPlayer: Generates the audio player.  Requires the appropriate scripts to be included in the <head> of the file.
	function audioPlayer() {
		echo "
        <div id=\"player\"></div>
        <script type=\"text/javascript\">
	var so = new SWFObject('http://www.jazzhistorydatabase.com/Scripts/jwplayer/player.swf','mpl','350','60','9');
	so.addParam('allowscriptaccess','always');
	so.addParam('allowfullscreen','false');
	so.addParam('wmode','transparent');
	so.addParam('flashvars','&type=sound&backcolor=521010&frontcolor=f5f4f4&lightcolor=eeeeee&screencolor=f5f4f4&skin=http://www.jazzhistorydatabase.com/Scripts/jwplayer/overlay.swf&bufferlength=2&volume=75&displayclick=none');
	so.write('player');
					</script>
	" ;
	}

?>    

    
        <div id="header_logo"><a href="/index.php"><img src="/images/sitewide/logo_w_head.jpg" width="260" height="169" alt="Jazz History Database" /></a></div>

        <div id="header_contribute"><a href="/contact.php" title="Contribute Materials to the Jazz History Database" class="header_contribute">
        <span class="header-plaintext">Become a</span><br />CONTRIBUTOR</a>
        </div><br /><br /><br />

        <div id="header_subscribe"><a href="http://eepurl.com/lRLFP" title="Subscribe to our Newsletter" class="header_subscribe">
        <span class="header-plaintext">Receive our </span><br />NEWSLETTER</a>
        </div>
        
        <div id="header_nav"><a href="/content/musicians/index.php" title="Jazz History Musicians" class="navs_musicians">MUSICIANS</a><a href="/content/events/index.php" title="Jazz History Events" class="navs_events">EVENTS</a><a href="/content/media/index.php" title="Jazz History Media" class="navs_media">MEDIA</a><a href="/content/collections/index.php" title="Jazz History Collections" class="navs_collections">COLLECTIONS</a></div>
        
        <div id="header_fb"><a href="https://www.facebook.com/jazzhistorydatabase" target="_BLANK"><img src="/images/index-splashes/facebook.png" /></a></div>