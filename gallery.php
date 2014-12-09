<?  // gallery.php - main display page which calls header, footer, sidebar, and main content area (based on URL GET parameters) 
// (page display layout based on original 2010 design, and conventions are mostly followed from there, altho generalized and smoothed for a common denominator to eliminate variations)



 session_start();  // (to see if a contributor is logged in)

/*
#BEGIN GALLERY DISPLAY TABLE
  GALLERY.PHP   -  main displaying code
  DISPLAY JHDB "GALLERY" (Collection)   for  GalleryItemID =  $_GET['gi']     gallery.php?gi=123
  Contents of this page is one of:
  
  GIContentTypeID	Page Display for gallery.php
  --------------------------------------------------------------
  	9999			Special case for Home Page (by defn:  GalID=1, GalItemID=1)
  	1				SubGallery - This Gallery Item points to a SubGallery which contains multiple (homogeneous) items (could be leaf nodes, or more sub galleries)
						thus this page will display the items WITHIN THAT GALLERY (while loop)
   LeafNodes:		(just displaying one item)
	11,12,13		Types of text or HTML content  (get pointer from URL field)
	2,3,4,5			Image or audio files
	6,7				Video via Youtube
	
#END GALLERY DISPLAY TABLE 
*/





require("db/lib-db.php");     			 // includes db_sfq   (single field query)
require("db/lib-JHDB-definitions.php");
require("db/lib-utils.php");
require("db/lib-gallery-utils.php");

$DisplayThisGalleryItemID = 1; // default for Home Page   --  which GalleryItem to display on THIS page
if ($_REQUEST['gi'] AND  $_REQUEST['gi']>0)  $DisplayThisGalleryItemID = intval($_REQUEST['gi']);


list($HTMLPageTitle, $ThisParentGalleryID, $ThisGIContentTypeID, $ThisContentURL, $ThisMenuTitle )	= 
	db_sfq("SELECT PageTitle, ParentGalleryID, GIContentTypeID, URL, PageTitle 
				FROM tblGalleryItems 
				WHERE ID='{$DisplayThisGalleryItemID}'");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <!-- Page Title -->
    <title><?=$HTMLPageTitle?> - Jazz History Database</title>

    <!-- Meta Headers -->
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

    <!-- Meta Information -->
   
   

    <!-- Favicon -->
    <link rel="shortcut icon" href="/images/sitewide/favicon.ico" type="image/x-icon" />

    <!-- ZOOMSTOP -->

    <!-- Cascading Stylesheets -->
    <link href="/css/sitewide-index.css" rel="stylesheet" type="text/css" media="screen" />

    <!-- Javascript Libraries/Frameworks -->

<!-- needed only for picture galleries -->    
<script type="text/javascript" src="http://www.jazzhistorydatabase.com/scripts/shadowbox/src/adapter/shadowbox-base.js"></script>
<script type="text/javascript" src="http://www.jazzhistorydatabase.com/scripts/shadowbox/src/shadowbox.js"></script>
<script type="text/javascript" src="http://www.jazzhistorydatabase.com/scripts/shadowbox/src/init.js"></script>
    
<? 
if ($_REQUEST['d'])   // display integrated dump (debug) mode info
{ ?>
<style>
table {
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
}
</style>
<?} ?>  
    
    
<!--script type="text/javascript" src="/scripts/search-highlight.js"></script-->
</head>
<body>
    <div id="main">
        <div id="header">
            <?php include("lib-masthead.php"); ?>
        </div>

        <!-- ZOOMRESTART -->

        <div id="body">
<!-- Colletion Master Nav Menu -->
    <div id="collection_header">
        <div id="collection_crumbs"> 
    <?




//  ----------------------------------------------- CRUMBS & SUB-MENU ---------------------------------------------
/* 
#BEGIN CRUMB BEHAVIOR SUMMARY TABLE
Page Type		GI ID		Top Crumb Menu              	Crumb SubMenu
-------------------------------------------------------------------------------
Home			1			none							none
DirOfAll Musns 2(,3,4,5)	none							none      Top-level MUSICIANS page, shows thumbs of all performers/bands/venues (all Entities)
MusnX Home Dir  (2nnnmmm)	Home>Musicians>ThisNameNoLink	SubGal1 - SubGal2 -etc.  ex: Bio - Photo Gal - etc.
Musn Leaf Node (2xnnnmmm)	Home>Musicians>ThisName>ThisPg	SiblngGI1 - SiblngGI2 -etc.  ex: Bio - Photo Gal - etc. Note these are siblings of this Child GI (same ParentGalleryID)
Musn Media Gal (2xnnnmmm)	Home>Musicians>ThisName>ThisPg	SiblngGI1 - SiblngGI2 -etc.  ex: Bio - Photo Gal - etc. Note these are siblings of this Child GI

#END CRUMB BEHAVIOR SUMMARY TABLE
*/
	$MasterCrumbMenu = $SubCrumbMenu = "";
	if       ($DisplayThisGalleryItemID <  10)      ; //no menus
	  elseif ($DisplayThisGalleryItemID >= 2000000)
	  { // assemble crumb menus
		$HomeLink			= "<a href='".SiteBaseURL."' >Home</a>";
		//$TopLevelGalleryID = find_top_level_galleryID($DisplayThisGalleryItemID); // Any gal item would have an upstream root gallery
		$CollectionLink		= "<a href='".GalleryBaseURL."?gi=2' >Musicians</a>"; // punt for hardcoded Musicians (ID=2)  for now- later enhance this to choose which hierarchy subtree we're in using TopLevelGalleryID (chg to galItemID??)
		$EntityFullName		= get_EntityFullName_from_GIID($DisplayThisGalleryItemID);
		$ThisEntityPrimaryGIID = get_EntityPrimaryGIID_from_GIID($DisplayThisGalleryItemID);
		$ThisEntityLink		= "<a href='".GalleryBaseURL."?gi={$ThisEntityPrimaryGIID}' > {$EntityFullName}</a>";
		$ThisEntityNameOnly	= $EntityFullName;
		$ThisPageMenuTitle	= $ThisMenuTitle;   // Link version is not needed (not ever used)
		
		$MasterCrumbMenu 	= "{$HomeLink} > {$CollectionLink} ";
		if ($DisplayThisGalleryItemID >=20000000)
			$MasterCrumbMenu .= "> {$ThisEntityLink} > {$ThisPageMenuTitle} ";
		  else  
			$MasterCrumbMenu .= "> {$ThisEntityNameOnly} ";   // FIX ME  http://jazzhistorymuseum.org/gallery.php?gi=2000001    NAME DOES NOT APPEAR
		
//echo " **** get_subgallery_crumb_menu($DisplayThisGalleryItemID); ";
		$SubCrumbMenu 		= get_subgallery_crumb_menu($DisplayThisGalleryItemID);  // always will be a subgallery above, thus look for siblings
		
		// menu text string vars are now ready to display
		
	  }// end elseif assemble crumb menus
	  
	  // Get title and header image for display below
	  $ThisGalleryHeaderImageURL = gallery_thumb_URL_from_itemID($DisplayThisGalleryItemID);  // uses Gal or GalItem Thumb
	  $ThisGalleryID		= db_sfq("SELECT SubGalleryID FROM tblGalleryItems WHERE ID ='{$DisplayThisGalleryItemID}'");
	  if ($ThisGalleryID)
	  	$ThisGalleryTitle =  db_sfq("SELECT Title FROM tblGalleries WHERE ID='{$ThisGalleryID}'");
	  //$ThisGalleryHeaderImageURL 	= expand_to_URL($ThisGalleryHeaderImageURL);  // from http://,  /webroot,  subdir/etc
		
	?>
    
	<?= $MasterCrumbMenu ?>
  </div> <!-- end collection_crumbs -->
  
  <div id="collection_title">
            <?
          if ($ThisGalleryHeaderImageURL)
            {
            ?>
          <img src="<?=$ThisGalleryHeaderImageURL?>" /><br /><br />

            <?
            }  // end if
            ?>
          <?=$ThisGalleryTitle?>
   </div> <!-- end collection title-->
          
   <div id="collection_navbar"> 
  	 <?= $SubCrumbMenu ?>
   </div> <!-- end sub nav bar of Gallery Items --> 
   <?
//  ----------------------------------------------- END CRUMBS & SUB-MENU ---------------------------------------------
			?>
            
  </div> <!-- end collection_header -->
<!-- Collection Content -->
  <div id="content">
<!-- Main Column -->
    <div id="content_main">
         
<?  





















// =========================================    SWITCH on  ThisGIContentTypeID    to display the corresponding formatting    ============================================
echo "\n<!-- Orig-GIID=$DisplayThisGalleryItemID;	 THIS(original-level)GIContentTypeID=$ThisGIContentTypeID; -->\n";	
	
	switch ($ThisGIContentTypeID)
	{
//=====================================   SUBGALLERY  --  display its directory of thumbs   ==================================
		case GIContentType_SubGallery:  // GI pointed to a subgallery which is a collection of next-level-down gallery items - display those thumbnails with links or media players
			$DisplayThisGalleryID = db_sfq("SELECT SubGalleryID FROM tblGalleryItems WHERE ID='{$DisplayThisGalleryItemID}'");
echo "\n<!-- 	display_SubGallery_child_GI_thumbs($DisplayThisGalleryID) -->\n";			
			display_SubGallery_child_GI_thumbs($DisplayThisGalleryID);  
		
		break;
		
		
		
//======================================  ALL LEAF NODES BELOW  ====================================
		case GIContentType_HomePage:
		
			echo " HOME PAGE GOES HERE <BR>\n ";  // could be taken care of as a CASE in   display_SubGallery_child_GI_thumbs(1)  (GI==1 is the home page)
		
		break;
		
//these are not implemented for leaf nodes  (must be subgalleries of one or more gallery items)	
		case GIContentType_ImageWithPlayer:
		break;
		case GIContentType_AudioWithPlayer:
		break;
		case GIContentType_VideoEmbedYouTube:
		break;


// ------------------   HTML/text CONTENT	 -------------------
		case GIContentType_JHDBContentFile:  // display file (specified in URL (misnomer in this case) field) with std JHDB template wrapper
echo "<!-- ContentFile = $ThisContentURL; GIID = $DisplayThisGalleryItemID -->\n";		
			if ($ThisContentURL != "") // display fixed (static) HTML content within the JHDB template shell
			{ // Display JHDB template shell with inner HTML content spliced in
				list($BioDescrPageFlag, $ThisParentGalleryID)
										=	db_sfq("SELECT BioDescrPage, ParentGalleryID FROM tblGalleryItems WHERE ID = '{$DisplayThisGalleryItemID}'");
				$ThisEntityID			=	db_sfq("SELECT EntityID FROM tblGalleries WHERE ID = '{$ThisParentGalleryID}'");
				$EntityImageURL			=	EntityContentBaseURL . db_sfq("SELECT ImageURL FROM tblEntities WHERE ID = '{$ThisEntityID}'");
				if ($BioDescrPageFlag)  $ImageToDisplay	= "<img src='{$EntityImageURL}' ><br />";
				else $ImageToDisplay	=	"";
				
				$ContentFile = PHPPathBase."/{$ThisContentURL}";
			
				if (file_exists($ContentFile))
				{
					$Temp		= explode(".",$ContentFile);
					$Extension	= strtolower(end($Temp)); // html or txt
					$TextHTMLWrapper 	= "div";  // inocuous default if HTML
					if ($Ext=='txt')	$TextHTMLWrapper 	= "pre";  // simple text - preserve linebreaks  <-- FIX ME  some other way (perhaps on the upload/input side so that plain text has <br> in the db record already.
					echo "<{$TextHTMLWrapper}>\n";
					echo $ImageToDisplay;
					include($ContentFile);
					echo "</{$TextHTMLWrapper}>\n";
				}
				else // file does not exist
					echo "<br><br><b><em>Please report this error:</em>  <br><br>Content URL file does not exist: $ContentFile </b> ";
			}// end isset
//  --------------------- END Display HTML/text Content ---------------------
		
		break;
		
		case GIContentType_RemoteURL:
		case GIContentType_JHDBURL:
		
		echo"<META http-equiv='refresh' content='0;URL={$URL}'>\n";    //    <--  FIX ME
		
		break;
		
		default:  // anything else is unimplemented!
		break;
	
	}// end switch



if (!$_REQUEST['d'])  
{ // not dump mode
?>
		  <div class="clearboth"></div>
          <div class="about-box">
              <div class="about-title"><br /><br />About Content for <em><?=$ThisGalleryTitle?></em></div>
              <p><?php permsBlurb(); ?></p>
          </div> <!-- end about-box -->
<? } ?>
          
          </div>
          
<!-- ---------------------------end content_main------------------------------ -->
          
          <!-- -----------------------Sidebar Column------------------ -->
          <div id="content_sidebar">
          <?php if (!$_REQUEST['d']) include('sidebar.php'); ?>
          </div>
      	</div> <!-- end content -->
      	<div class="clearboth"></div>
      </div> <!-- end main -->

      <!-- ZOOMSTOP -->

      <div id="footer">
        <?php if (!$_REQUEST['d']) include('lib-footer.php'); ?>
      </div>
    </div> <!-- end body -->

    <!--ZOOMRESTART-->

  </body>
</html>