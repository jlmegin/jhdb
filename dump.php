<?
//  Dump function to display hierarchical contents of db
// default (no get params)    is EVERYTHING, starting with the "root" gallery item  (ID=1)  which is the home page
// to display just a sub-tree,  http://jazzhistorymuseum.org/dump.php?g=1000001   <-- sub tree gallery ID node


require("db/lib-db.php");  // includes db_sfq   (single field query)
require("db/lib-JHDB-definitions.php");

$RootGalleryItemItem = 1;  // default if no GET arg given
if ($_REQUEST['gi'])  $RootGalleryItemItem = intval($_REQUEST['gi']);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JHDB DB Dump Utility</title>
<style type="text/css">
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
table {
   border: 2px solid brown;
   border-collapse: collapse;
} 
th, td {
   border: 1px solid grey;
   padding: 3px;
} 
</style>
</head>

<body>

<? if($RootGalleryItemItem==1){ ?>
<strong>Top Level HomePage as Root</strong> (defined as GalleryItemID==1)<br>
<? }?>
&nbsp;&nbsp;&nbsp;<em>(Click on underscored links to display resulting (live) page for that SubGallery)</em>
<table >
  <tr style="font-weight:bold;font-size:125%;background-color: green;
    color: white;"><td width="25"></td><td width="25"></td><td width="250">TITLE</td>
  <td width="110">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gal ID<br />
    <em><span style='color:grey;'>GalItem ID</span></em></td>
  <td width="75">GI: <br />
Parent <br />
Gal ID</td>
  <td width="10">SubGal<br />
    ID<br />
    pointer</td>
  <td width="50">Gal Item<br />
    Content
    <br />
  Type</td>
  <td width="10">Gallery<br />
  #Chldrn</td> </tr>

<? 




//===========================================   traverse_gallery_items    main recursive function  ================================================


function traverse_gallery_items($GalleryID, $Level=0)   // find all GalleryItems within Gallery  GalleryID
{
	$GalleryTitle			= db_sfq("SELECT Title  FROM tblGalleries WHERE ID='{$GalleryID}'");
	$NumGIChildren	 		= db_sfq("SELECT COUNT(ID) FROM tblGalleryItems 
														WHERE ParentGalleryID='{$GalleryID}'");
//	echo str_repeat(". ",$Level) . "G[$GalleryID]<b>{$GalleryTitle}</b> -{$Level}- &nbsp;GI children:$NumGIChildren<br>";
	
	
	
	
// ----------------------------------------	 GALLERY RECORD  ---------------------------------------------
    ?>
<tr style="background-color: yellow; color: blue;">
    <td style='background-color:pink;'><a href="http://jazzhistorymuseum.org/db/admin/tblGalleries_view.php?SelectedID=<?=$GalleryID?>#detail-view" target="_blank">E</a></td>
  <td style="font-weight:bold;font-size:110%;color:white;background-color: brown;">G</td>
    <td style="padding-left:<?=($Level*16) ?>px; text-indent:<?=(-$Level*16) ?>px;"><b><?=str_repeat(".&nbsp;&nbsp;&nbsp;",$Level) . $GalleryTitle?></b></td>
    <td align="right"><!-- need GI ID   a href='<?=GalleryBaseURL."?gallery={$GalleryID}"?>' --><?=$GalleryID?><!-- /a --></td>  <!------------------------>
    <td></td><td></td><td></td>
    <td><?=db_sfq("SELECT COUNT(ID) FROM tblGalleryItems WHERE ParentGalleryID='{$GalleryID}'")?></td> </tr>
<?
// ----------------------------------------	 END GALLERY RECORD  ---------------------------------------------


    
   
	$Results = db_sql("SELECT * FROM tblGalleryItems WHERE ParentGalleryID='{$GalleryID}' ORDER BY Sort ASC, ID");
	if (mysqli_num_rows($Results)==0) echo "<tr><td colspan=7>&nbsp;&nbsp;&nbsp;<i>[no GalleryItems found for ParentGalleryID=={$GalleryID}]</i></td></tr>";
	
	
	
	while ($Row=mysqli_fetch_assoc($Results))
	{ //  -------------------------------------------- FOR EACH GALLERY ITEM in this gallery $GalleryID -----------------------------------------


		$GalleryItemID			= $Row['ID'];
		$GalleryItemURL			= $Row['URL'];
		$SubGalleryID			= $Row['SubGalleryID'];
		$ParentGalleryID		= $Row['ParentGalleryID']; 
		$GIPageTitle			= $Row['PageTitle'];
		$GIContentTypeID		= $Row['GIContentTypeID'];
		$SubGalleryTypeDisplay	= $GIContentTypeID;
			if ($GIContentTypeID==GIContentType_RemoteURL OR $GIContentTypeID==GIContentType_JHDBURL) 	
						$SubGalleryTypeDisplay = "<a href='$GalleryItemURL' target='_blank'>URL</a>";  // complete URL self-contained
			if ($GIContentTypeID==GIContentType_ContentURL) 	$SubGalleryTypeDisplay = "<a href='".GalleryBaseURL."?u={$GalleryItemURL}' target='_blank'>URL</a>";
			if ($GIContentTypeID==GIContentType_SubGallery) 	$SubGalleryTypeDisplay = "SubGallery";
			if ($GIContentTypeID==GIContentType_Image) 			$SubGalleryTypeDisplay = "Image";
			if ($GIContentTypeID==GIContentType_Audio) 			$SubGalleryTypeDisplay = "Audio";
			if ($GIContentTypeID==GIContentType_Video) 			$SubGalleryTypeDisplay = "Video";
		if ($GIContentTypeID==GIContentType_SubGallery AND ($SubGalleryID <= 0 OR $SubGalleryID == $GalleryID)) 
		{
			?><tr><td colspan=8>Warning traverse_gallery_items(<?=$GalleryID?>)   GalleryItemsID(<?=$GalleryItemID?>): ParentGalleryID=<?=$GalleryID?> with SubGalleryID=<?=$SubGalleryID?> </td></tr>
            <?
		} else //no error
		{
			$NextLevelGalleryInfo 	= "";
			//if ($SubGalleryTypeID==MediaType_SubGallery)  FIX ME
				$NextLevelGalleryExists = db_sfq("SELECT COUNT(ID) FROM tblGalleries WHERE ID='{$SubGalleryID}'");
			if ($GIContentTypeID==GIContentType_SubGallery AND $NextLevelGalleryExists==0) $NextLevelGalleryInfo 	= "(SubGallery entry does not exist yet) ";
		
		
		
		
// ----------------------------------------	 GALLERY ITEM RECORD ---------------------------------------------
		?>
<tr <?= ($GIContentTypeID==GIContentType_SubGallery?"style='background-color:lightblue;'":"style='background-color:lightgrey;'")?>>
<td style='background-color:cyan;'><a href="http://jazzhistorymuseum.org/db/admin/tblGalleryItems_view.php?SelectedID=<?=$GalleryItemID?>#detail-view" target="_blank">E</a></td>
<td>GI</td><td  style="padding-left:<?=($Level*16) ?>px; text-indent:<?=(-$Level*16) ?>px;"><?=str_repeat(".&nbsp;&nbsp;&nbsp;",$Level) . $GIPageTitle?></td>
<td align="left" style='color:grey;'  ><em><a href='<?=GalleryBaseURL."?gi={$GalleryItemID}"?>'><?=$GalleryItemID?></a>
</em></td><td><?=$ParentGalleryID?></td><td><?=$SubGalleryID?></td>
<td><?=$SubGalleryTypeDisplay?></td><td><?=$NextLevelGalleryInfo?></td> </tr>
    	<?	
// ----------------------------------------	 END GALLERY ITEM RECORD ---------------------------------------------

		
		
		
			if ($GIContentTypeID==GIContentType_SubGallery)
				traverse_gallery_items($SubGalleryID, $Level+1 );
		} // end else no error 
	}// end while
	
}// ============================= end function traverse_gallery_items ==================================




?>

<?
// ----------------------------------------------------------   RECURSIVE CALL  -------------------------------------------------------
traverse_gallery_items($RootGalleryItemItem);  // defaults to $_REQUEST['gi'] = 1  ( = HOME PAGE  which is the gallery ROOT )






?>
</table>
<p><br />
G= Gallery (Collection)<br />
GI = GalleryItem (displayed above in 'Sort' order)</p>
<p>E= Edit Record</p>
<p><br />
</p>
<table width="594" border="0">
  <tr>
    <td width="160"><strong>Gallery ID Range(**)</strong></td>
    <td width="420"><strong>Comment</strong></td>
  </tr>
  <tr>
    <td>1-99</td>
    <td><p>Top-level Galleries (Musicians, Events, Media, &quot;Collections&quot;)</p></td>
  </tr>
  <tr>
    <td>1,000-999,999</td>
    <td>Collections (Galleries)</td>
  </tr>
  <tr>
    <td>1,000,000-1,999,999</td>
    <td>Main (top-level) Entity(*) Collections</td>
  </tr>
  <tr>
    <td>10M &amp; up</td>
    <td>Supporting (Sub)Galleries</td>
  </tr>
</table>
<p>(*) <strong>Entities</strong> are People, Musicians, Events, Venues, Bands, etc. - for the want of a better all-encompassing term<br />
(**) <strong>ID</strong>s technically are NOT range-dependent. This convention is to help humans readily know what a Gallery or GalleryItem is in general. </p>
<p><strong>Accessions</strong> are items in the museum: mediums (images, audio, video)<br />
<strong>Galleries</strong> are <em>groups</em> of &quot;anything&quot;  (e.g., Collection categories in the museum, musicians, images, audio interviews, etc.) </p>
<p>&nbsp;</p>
<strong>DUMP of URL/Path definitions:</strong><br />
<?
echo "	ServerAccountUsername=".ServerAccountUsername."<BR>
	SiteBaseURL		=".SiteBaseURL."<BR>	
	GalleryBaseURL	=".GalleryBaseURL."<BR>		
	PHPPathBase		=".PHPPathBase."<BR>	
	EntityContentPHPBasePath=".EntityContentPHPBasePath."<BR>
	EntityContentBaseURL=".EntityContentBaseURL."<BR>
	";
	?>

</body>
</html>