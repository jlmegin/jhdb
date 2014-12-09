<?  // utilities to support gallery traversal & display





// ================================================  primary GALLERY display mechanism  =========================================
function display_SubGallery_child_GI_thumbs($DisplayGalleryID)   // "Parent"GalleryID  is the gallery contents to be displayed
{
	// This context is only for (sub)gallery sets of Child Gallery Items within this Gallery,  it would not be called if the (parent) GI were a leaf node(since there's no subgallery)
	
	// Get This Gallery Info
	list ($GalleryTitle, $GallerySummary, $ThumbURL, $HeaderImageURL) = 
		db_sfq("SELECT Title, Summary, ThumbURL, HeaderImageURL FROM tblGalleries WHERE ID='{$DisplayGalleryID}'");
	        
	echo "<span style='font-weight:bold;font-size:16px;font-style:italic;color:darkblue;'>{$GallerySummary}</span><br><br>\n<!-- GalleryID={$DisplayGalleryID}-->\n";
	
	
	// Get children Gallery Items 
	if ($DisplayGalleryID==1)  $OrderBy = " ORDER BY HomePageFeatureSort ASC "; else $OrderBy = " ORDER BY Sort ASC "; // home page special sorting
	if ($_REQUEST['d']>0 OR $_SESSION['ContributorID']>0) $WhereOnline = ""; else $WhereOnline = " AND Online=1 ";

	$GIResults = db_sql($Q="SELECT * FROM tblGalleryItems WHERE ParentGalleryID = '{$DisplayGalleryID}' {$WhereOnline}  {$OrderBy} ");

echo "\n\n<!--==============\n {$Q} -->\n\n";
	$GIDisplayCount =0; // for even-odd (left/right columnization)

// --------------------------------------------  DUMP MODE TABLE HEADERS  -------------------------------------	
if ($_REQUEST['d'])
{  
	echo "<a href='/db/admin/tblGalleries_view.php?SelectedID={$DisplayThisGalleryItemID}#detail-view' target='_blank' style='color: brown;'>Edit This GalleryID[{$DisplayThisGalleryItemID}] Record</a> <em>(in new window)</em>;  &nbsp;&nbsp;<a href='/dump.php?g={$DisplayThisGalleryItemID}'  target='_blank' style='color: brown;'>Display List Format for this Gallery</a><br><br>\n";
    echo "<table style='word-wrap: break-word; max-width: 200px;'>".// dump mode
		"<tr><td align='center'><strong>GalItemID</strong><br><em>Click to Edit</em></td><td >	URL</td><td>	SubGID	</td><td>	
			ConTypID	</td><td>CaptTxt</td><td style=\"width:'100px';\">ThumbURL</td><td>			
			AltTxt		</td><td>	CSSClr	</td><td>	MenuTl</td><td>		GIPageTitle</td></tr>\n";
} // end dump headers
// --------------------------------------------  END DUMP MODE TABLE HEADERS  -------------------------------------	


else // not dump mode
	{ ?>
		<table width="340" border="0" align="center" cellpadding="5" cellspacing="0"> <!-- begin thumb cells for this Gallery's Gal Items -->
	<? }
		
	while ($GIRow = mysqli_fetch_assoc($GIResults))
	{ // ----------tblGalleryItems-----------
		$ChildGalleryItemID		= $GIRow['ID'];
		$URL					= $GIRow['URL'];
		$ChildGIContentTypeID	= $GIRow['GIContentTypeID'];
		$CaptionText			= $GIRow['CaptionText'];
		$ThumbURL				= $GIRow['ThumbURL'];		// GAllery THumbURL to show in gallery (collection) directory  landscape wide thumb
		$ThumbAltText			= $GIRow['ThumbAltText'];
		   if ($ThumbAltText=="") $UseThumbAltText = $CaptionText; // default if blank
		$CSSClearBothAfter		= $GIRow['CSSClearBothAfter']; // Column mgmt Values:  -1 (default) after every other(even);   0 NO <div>;   1 YES force: <div class="clearboth"></div>	
		$MenuTitle				= $GIRow['MenuTitle'];
		$GIPageTitle			= $GIRow['PageTitle'];




//  ---------------------------------------------------- DUMP MODE --------------------------------------
		if ($_REQUEST['d']) // dump mode -- display for This Child Gallery Item
		{
			echo "<tr><td style='background-color: blue; color: white; font-weight:bold;'><a href='http://jazzhistorymuseum.org/db/admin/tblGalleryItems_view.php?SelectedID={$ChildGalleryItemID}#detail-view' target='_blank' style='color: white;'>{$ChildGalleryItemID}</a></td><td>	$URL</td><td>	$DisplayGalleryID	</td><td align='center'>	
			$ChildGIContentTypeID	</td><td>$CaptionText	</td><td>
			$ThumbURL<br>URL=".gallery_thumb_URL_from_itemID($ChildGalleryItemID)."			</td><td>			
			$ThumbAltText		</td><td>	$CSSClearBothAfter	</td><td>	$MenuTitle</td><td>		$GIPageTitle</td></tr>\n";
		}// end debug dump
//  ---------------------------------------------------- END DUMP MODE --------------------------------------
	
		
		
		
		else   // not dump mode
		{// display thumbs (w/links) for this Child GI
		
echo "\n<!-- GIID=$ChildGalleryItemID;	 ChildGIContentTypeID=$ChildGIContentTypeID; -->\n";
		switch ($ChildGIContentTypeID)	
		 {	
			
		case GIContentType_SubGallery:
			{
	//======================================== Type SubGallery =================================
			?>
            <tr><td>
			<div class="index-item">
			<a href="<?= gallery_link_from_GalleryItemID($ChildGalleryItemID, false) ?>">
				<div class="index-item-title"><?=$GIPageTitle?></div>
				<img src="<?=gallery_thumb_URL_from_itemID($ChildGalleryItemID) ?>" alt="<?=$ThumbAltText?>" />
				<div class="index-item-note"><?=$CaptionText?></div>
			</a>
			</div> <!-- end index; GI ID <?=$ChildGalleryItemID?>--> 
            </td></tr>
			<?
			} // end GIContentType_SubGallery
			break;
	// =========================================== end Type SubGallery =====================================
			
			
			
		case GIContentType_ImageWithPlayer:
			{
	// --------------------------------- image gallery items "template" ---------------------------------
			?>
			<tr> <!-- GIContentType_ImageWithPlayer GalItmID=<?=$ChildGalleryItemID?> -->
			  <td align="center" valign="top">
			   <p><a rel="shadowbox" title="<?=$CaptionText?>" href="<?=$URL?>">
				<img src="<?=gallery_thumb_URL_from_itemID($ChildGalleryItemID) ?>" alt="<?=$UseThumbAltText?>" border="0" class="photogallery" /></a>
			   </p>
			  </td>
			</tr>
			<tr>
			  <td align="center" valign="top">
				<p class="gallery_caption"><?=$CaptionText?></p> 
			  </td>
			</tr>
			<?
	// -------------------------------------  end image gallery item  ---------------------------------------
			} // end GIContentType_ImageWithPlayer
			break;
			
			
			
	// --------------------------------- audio gallery items "template" ---------------------------------
		case GIContentType_AudioWithPlayer:
			{
				if (!$GIPageTitle) $GIPageTitle = "Track";
			?>
			<tr> <!-- GIContentType_AudioWithPlayer GalItmID=<?=$ChildGalleryItemID?> -->
			  <td align="center" valign="top">
			   <p><div class="audiotrackblock"><a href="JavaScript:player.sendEvent('LOAD', '<?=$URL?>'), player.sendEvent('PLAY')"><?=$GIPageTitle?></a></div>
				
			   </p>
			  </td>
			</tr>
			<?
	// -------------------------------------  end MP3 gallery item  ---------------------------------------
			} // end GIContentType_AudioWithPlayer
			break;
			
			
	// --------------------------------- video gallery items "template" ---------------------------------
		case GIContentType_VideoEmbedYouTube:
			{
				if (!$GIPageTitle) $GIPageTitle = "Video";
			?>
			<tr> <!-- GIContentType_AudioWithPlayer GalItmID=<?=$ChildGalleryItemID?> -->
			  <td align="center" valign="top">
			   <center><iframe width="640" height="360" src="<?=$URL?>" frameborder="0" allowfullscreen></iframe>
            <br /> <?=$GIPageTitle?></center>
			  </td>
			</tr>
			<?
	// -------------------------------------  end MP3 gallery item  ---------------------------------------
			} // end GIContentType_VideoEmbedYouTube
			break;
	
	
	
	
			case GIContentType_RemoteURL: 
			case GIContentType_JHDBURL: 
			case GIContentType_JHDBContentFile:
			{
	// ---------------------------------  URL thumbnail  ---------------------------------
			?>
            <tr><td>
			<div class="index-item"> <!--GIContentType_URL  GI ID <?=$ChildGalleryItemID?> -->
			<a href="<?= gallery_link_from_GalleryItemID($ChildGalleryItemID, false) ?>">
				<div class="index-item-title"><?=$GIPageTitle?></div>
				<img src="<?=gallery_thumb_URL_from_itemID($ChildGalleryItemID) ?>" alt="<?=$CaptionText?>" />
				<div class="index-item-note"><?=$CaptionText?></div>
			</a>
			</div> <!-- end index --> 
			</td></tr>
			<?
	// -------------------------------------  end URL thumbnail  ---------------------------------------
			} // end GIContentType_URL
			break;



			
			default:  echo "\n <tr><td> <!-- No Template: ChildGalleryItemID={$ChildGalleryItemID}; GIContentType={$ChildGIContentTypeID} --> \n</td></tr>\n";
			
				$GIDisplayCount++;
				if ( 0 AND  /*disable until switch back to using no tables*/ ($CSSClearBothAfter<0 AND $GIDisplayCount %2 ==0) OR $CSSClearBothAfter==1) 
				{
				?>		<div class="clearboth"></div><?  // CSS next row after TWO entries <-- a very hardcoded format design (inherited)
				}// end if CSSClearBoth
		 } // end switch
		}// end else not debug dump
		
	}// end while visit each GalleryItem

	if (1 )   // consider if type-dependent, etc.
		{ ?>
		</table> <!-- end table of thumbs or debug -->
		<? }

	//if ($_REQUEST['d'])  echo "</table>"; // dump mode

} // end function display




?>