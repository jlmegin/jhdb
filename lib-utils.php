<?php

 
function convert_field_to_sql_date($Date)
{
	$Date = trim($Date);
	$Date = str_replace(" ","",$Date);
	if (strlen($Date)==0) return "0000-00-00";  // was null
	if (strlen($Date)==4) return "{$Date}-00-00";
	if (strlen($Date)!=10) echo"Warning '$Date' needs to be in the format dd/mm/yyyy<br>";
	$Date = date_format('Y-m-d', strtotime(str_replace('-', '/', $Date)));
	return $Date;
}// end convert_field_to_sql_date


function MoveToCurrentEntityDirectory($SubDir, $PostName)
{
	
	if($_FILES[$PostName]['error'] != 0) die("Error in uploading file. <a href='http://php.net/manual/en/features.file-upload.errors.php'>{$_FILES[$PostName]['error']}</a>");
	if (!$_SESSION['CurrentEntityID']) die("<strong>Internal Error: Entity (musician) ID is not set, cannot proceed with File upload operation</strong>");
	$EntityDirectoryPath 	= db_sfq("SELECT DirectoryPath FROM tblEntities WHERE ID ={$_SESSION['CurrentEntityID']} ");
	$TargetDirPath			= EntityContentPHPBasePath."musicians/{$EntityDirectoryPath}/{$SubDir}/";
	if (!file_exists($TargetDirPath)) {mkdir($TargetDirPath, 0777); chmod($TargetDirPath, 0777); /*chown($TargetDirPath, ServerAccountUsername);*/}  // directory is there
	
	$Filename				= strtolower($_FILES[$PostName]['name']);
	$TempArray 				= explode(".", $Filename);
	$FileExtension 			= end($TempArray);
	if (substr_count($Filename,".")!=1) echo "Warning: image filename {$Filename} does not contain exactly one period.<br />\n";
	$FileNameNoExt			= $TempArray[0];   //  ASSUMPTION: no extra periods in filename
	$TargetPathAndName		= $TargetDirPath.$Filename;
	$Count = 0; 
	while (file_exists($TargetPathAndName)) 
	{
		$Count++;   
		$TargetPathAndName 	= $TargetDirPath.($FileName=$FileNameNoExt."_".$Count.".".$FileExtension);
	}// end while
	$TargetURL				= "{$EntityContentBaseURL}musicians/{$EntityDirectoryPath}/{$SubDir}/{$Filename}";
	$SourceTempFile			= $_FILES[$PostName]['tmp_name'];
echo " 	$EntityDirectoryPath; $TargetPathAndName; $TargetURL;	$SourceTempFile;";
	$Result = rename($SourceTempFile, $TargetPathAndName);
	if (!$Result) die ("<strong>Internal Error</strong> for uploaded file rename from temp directory to musician's directory: <br /><strong>rename</strong>({$SourceTempFile}, {$TargetPath}{$Filename}) <br />\n");
	 chmod($TargetPathAndName, 0777);
	 //chown($TargetPathAndName, ServerAccountUsername);
	return $TargetURL;
} // end MoveToCurrentEntityDirectory


function create_main_musician_GI_and_gallery($CurrentEntityID) // creates new Gallery Record and sets SESSION variables to "select gallery"
{
	// This fn Depends on SESSION vars to be correct (for this new Entity), rather than looking up in tblEntities
	list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('MainCollectionGalleryItem');	
	$NextGalleryItemID				= find_first_free_ID( "tblGalleryItems", $RangeMin, $RangeMax);
	list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('MusicianBandMainGallery');	
	$NextGalleryID					= find_first_free_ID( "tblGalleries", $RangeMin, $RangeMax);
	$EntityName	= (strlen($_SESSION['CurrentEntityFName'])>0? "{$_SESSION['CurrentEntityFName']} ":"").  /* FName may be Null */
						"{$_SESSION['CurrentEntityLName']}";
			
	db_sql("INSERT INTO tblGalleries SET 
				ID				= '{$NextGalleryID}',
				GIContentTypeID = ".SubGalleryType_SubGallery.",
				EntityID		= '{$CurrentEntityID}', 
				Title			= '{$EntityName} Collection',
				ContributorID	= '{$_SESSION['CurrentContributorID']}',
				Online			= 1
			");
	$ThisGalleryID = mysql_insert_id();	
		
	db_sql("INSERT INTO tblGalleryItems SET
				ID 				= '{$NextGalleryItemID}',
				ParentGalleryID	= '2', /*Top-level MUSICIANS COLLECTION*/
				SubGalleryID	= '{$ThisGalleryID}',
				GIContentTypeID	= ".SubGalleryType_SubGallery.",
				PageTitle		= UPPER('{$EntityName} COLLECTION'),
				MenuTitle		= '{$EntityName} Collection',
				Online			= 1					
			  ");
	$ThisGalleryItemID = mysql_insert_id();
	echo "<br>Created '{$EntityName}' top-level collection Gallery[{$ThisGalleryID}] (within the top-level JHDB MUSICIANS Collection) with corresponding GalleryItemID[{$ThisGalleryItemID}]<br>";
	
	
	
	// SET SESSION VARIABLES
	//   none???
	
	return $ThisGalleryID;  // new GalleryID created
	
} // end create_main_musician_GI_and_gallery
				
				// --------------------  If Musician, 
				
function create_default_musician_GI_and_subgalleries($MusicianMainGalleryID)  //Create  standard subgalleries underneath "main" MusicianMainGalleryID
{
	// Create GalleryItems within MusicianMainGalleryID  for: Bio, Images, Audio, Video
	//   and then SubGalleries for Images and Audio and Video
	//  Deal with server directories (existence, creation, permissions) at upload time (back in main code)
	
	// This fn Depends on SESSION vars to be correct (for this new Entity), rather than looking up in tblEntities


echo "<br> create_default_musician_GI_and_subgalleries($MusicianMainGalleryID) <br>";


//--------------------------------- GALLERY ITEMS FIRST    then   SUBGALLERIES  (for each) --------------------------------------  
  	list($RangeMin, $RangeMax) 		= get_ID_range_for_GalleryType('GalleryItem');	
	$NextGalleryItemID				= find_first_free_ID( "tblGalleryItems", $RangeMin, $RangeMax);
  	list($SGRangeMin, $SGRangeMax) 	= get_ID_range_for_GalleryType('SubGallery');	
	$NextGalleryID					= find_first_free_ID( "tblGalleries", $SGRangeMin, $SGRangeMax);
	
	$EntityName	= (strlen($_SESSION['CurrentEntityFName'])>0? "{$_SESSION['CurrentEntityFName']} ":"").  /* FName may be Null */
						"{$_SESSION['CurrentEntityLName']}";
	
      if (isset($_POST['CreateBioItem']))   // Bio is only a GalleryItem (no SubGallery)
	  {
		  db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					GIContentTypeID	= ".SubGalleryType_ContentURL.",
					PageTitle		= 'Bio for {$EntityName}',
					MenuTitle		= 'Biography',
					Online			= 1,
					Sort			= 100					
				  ");
			$ThisGalleryItemID = mysql_insert_id();
			echo "<br>Created Biography GalleryItem[{$ThisGalleryItemID}] (within the top-level Musician Gallery) - please fill in details in subsequent steps<br>";
			$NextGalleryItemID++;  // incr for next Gallery Item so we don't have to re calculate (not that it's huge CPU usage)
			// No subgallery for Bio
	  } // end	 CreateBioItem  
	  
	  if (isset($_POST['CreateDescrItem']))   // only GalleryItem
	  {
		if (isset($_POST['CreateBioItem'])) echo "Warning: CreateDescrItem <strong>Description</strong> ignored, since you also requested <strong>Biography</strong>. Contact JHDB admins to create additional Gallery Items (for Descriptions, etc.)<br>";
		else
		{ // continue CreateDescrItem
		  db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					GIContentTypeID	= ".SubGalleryType_ContentURL.",
					PageTitle		= 'Description of {$EntityName}',
					MenuTitle		= 'Description',
					Online			= 1,
					Sort			= 200					
				  ");	
			$ThisGalleryItemID = mysql_insert_id();
			echo "<br>Created Description GalleryItem[{$ThisGalleryItemID}] (within the top-level Musician/Group Gallery) - please fill in details in subsequent steps<br>";		
			$NextGalleryItemID++;  // incr for next Gallery Item
			// No subgallery for Descr
		} // end CreateDescrItem
	  } // end	 CreateBioItem    
	  
      if (isset($_POST['CreateImageGallery']))  // GalleryItem AND (empty) SubGallery
	  {
		   			// Create corresponding Image SubGallery                    
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".SubGalleryType_ContentURL.",
					ContentTypeID	= '',  /* what does this subgallery contain/point to ?  */
					Title			= 'Photo Gallery for {$EntityName}',
					Summary			= 'Photo Gallery for {$EntityName}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 300					
				  ");
			$ThisSubGalleryID = mysql_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".SubGalleryType_ImageWithPlayer.",
					PageTitle		= 'Photo Gallery for {$EntityName}',
					MenuTitle		= 'Photo Gallery',
					Online			= 1,
					Sort			= 300					
				  ");
			$ThisGalleryItemID = mysql_insert_id();
			echo "<br>Created Photo GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			

			echo "<br>Created Image SubGallery[{$ThisSubGalleryID}]  (image collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
			
	  } // end	CreateImageGallery     
      if (isset($_POST['CreateAudioGallery']))
	  {
		// Create corresponding Audio SubGallery                     
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".SubGalleryType_AudioWithPlayer.",
					ContentTypeID	= '',  /* what  this subgallery contains/points to   */
					Title			= 'Audio Gallery for {$EntityName}',
					Summary			= 'Audio Gallery for {$EntityName}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 400					
				  ");
			$ThisSubGalleryID = mysql_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".SubGalleryType_AudioWithPlayer.",
					PageTitle		= 'Audio Gallery for {$EntityName}',
					MenuTitle		= 'Audio Gallery',
					Online			= 1,
					Sort			= 400					
				  ");
			$ThisGalleryItemID = mysql_insert_id();
			echo "<br>Created Audio GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			

			echo "<br>Created Audio SubGallery[{$ThisSubGalleryID}]  (MP3 collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
			
		  
		  
	  } // end	CreateAudioGallery     
      if (isset($_POST['CreateVideoGallery']))
	  {
		  // Create corresponding Video SubGallery                     
			db_sql("INSERT INTO tblGalleries SET
		 			ID 				= '{$NextGalleryID}',
					GIContentTypeID	= ".SubGalleryType_VideoEmbedYouTube.",
					ContentTypeID	= '',  /* what does this subgallery contain/point to ?  */
					Title			= 'Video Gallery for {$EntityName}',
					Summary			= 'Video Gallery for {$EntityName}',
					Online			= 1,
					ContributorID	= '{$_SESSION['CurrentContributorID']}',
					EntityID		= '{$_SESSION['CurrentEntityID']}',
					Sort			= 400					
				  ");
			$ThisSubGalleryID = mysql_insert_id();
			
			db_sql("INSERT INTO tblGalleryItems SET
		 			ID 				= '{$NextGalleryItemID}',
		  			ParentGalleryID	= '{$MusicianMainGalleryID}',
					SubGalleryID	= '{$ThisSubGalleryID}',
					GIContentTypeID	= ".SubGalleryType_VideoEmbedYouTube.",
					PageTitle		= 'Video Gallery for {$EntityName}',
					MenuTitle		= 'Video Gallery',
					Online			= 1,
					Sort			= 400					
				  ");
			$ThisGalleryItemID = mysql_insert_id();
			echo "<br>Created Video GalleryItem[{$ThisGalleryItemID}]<br>";		
			$NextGalleryItemID++; 
			

			echo "<br>Created Video SubGallery[{$ThisSubGalleryID}]  (Youtube collection within the top-level Musician Gallery) - please fill in details/uploads in subsequent steps<br>";
			$NextGalleryID++; 
	  } // end CreateVideoGallery
      
	
} // end create_default_musician_GI_and_subgalleries


function get_ID_range_for_GalleryType($GalleryTypeID)
{
	$RangeMin	= RangeMinGalleryItemsID; // defaults
	$RangeMax 	= RangeMaxGalleryItemsID;
  
	switch ($GalleryTypeID)
	{
		case 'BaseGallery': // 
			$RangeMin = RangeMinCollectionGalleryID; // base (top-level) collections created by JHDB admins only (MUSICIANS, MEDIA, EVENTS, etc.)
			$RangeMax = RangeMaxCollectionGalleryID;
		break; // end case BaseGallery
		case 'MusicianBandMainGallery': // 
			$RangeMin = RangeMinEntityGalleryID; 	 // musician/band collections IDs for tblGalleries that are associated with ENTITIES
			$RangeMax = RangeMaxEntityGalleryID;
		break; // end case MusicianBandMainGallery
		case 'SubGallery':	// 
			$RangeMin = RangeMinSubGalleryID; 		 // supporting sub galleries 
			$RangeMax = RangeMaxSubGalleryID;
		break; // end case SubGallery
		case 'GalleryItem':	// 
			$RangeMin = RangeMinGalleryItemsID; 	 // GalleryItem Table
			$RangeMax = RangeMaxGalleryItemsID;
		break; // end case GalleryItem
		case 'MainCollectionGalleryItem':	// 
			$RangeMin = RangeMinEntityGalleryItemsID; 	 // Musician (main collection) GalleryItem Table
			$RangeMax = RangeMaxEntityGalleryItemsID;
		break; // end case MainColelctionGalleryItem
		case 'Entity':	// 
			$RangeMin = RangeMinEntityID; 	 	     // Entity Table
			$RangeMax = RangeMaxEntityID;
		break; // end case Entity
		
	}// end switch GalleryType
	
	return array($RangeMin, $RangeMax );
} // end get_ID_range_for_GalleryType


//===============================================  DATABASE HELPERS  =========================================

function find_top_level_galleryID($CurrentID)
{
	$ID = $CurrentID;
	while ($ID>=Max_ID_TopLevel_Galleries)
	{
		$TempID = db_sfq("SELECT ParentGalleryID FROM tblGalleryItems WHERE SubGalleryID=$ID LIMIT 1"); // LIMIT just in case
		if ($TempID=="" OR $TempID==$$ID) {echo "Error: recursion for find_top_level_galleryID( {$CurrentID} ) ID={$ID}<br>\n"; return $ID; }
//	echo " <br>$CurrentID--TempID=$TempID;-- ";
		if ($TempID==1) exitloop; // ID==1 is Home
		if ($TempID) $ID=$TempID;
	}
	
	return $ID;
} // end find_top_level_galleryID

function gallery_thumb_URL_from_itemID($ID, $IncludeHTMLTag=0 )   ///////  FIXME    NOT finished
{
	global $ImageURLBase;
	
	list($ThumbURL, $URL)  = db_sfq("SELECT ThumbURL, URL FROM tblGalleryItems WHERE ID = {$ID}");
	if (!$ThumbURL) return $URL;
	
	return $ThumbURL; 
	
} // end gallery_thumb_URL_from_itemID


function gallery_link_from_GalleryItemID($ID, $IncludeHTMLTag=true )
{
	list( $GIContentTypeID, $SubGalleryID, $GalleryItemURL, $PageTitle, $MenuTitle) = db_sfq("SELECT GIContentTypeID, SubGalleryID, URL, PageTitle, MenuTitle FROM tblGalleryItems WHERE ID = {$ID}");
	
	$ResultURL = GalleryBaseURL;   $Target="";
	if ($GIContentTypeID==SubGalleryType_SubGallery)		$ResultURL .=	"?g={$SubGalleryID}";	
	if ($GIContentTypeID==SubGalleryType_RemoteURL)			{$ResultURL	=	$GalleryItemURL; $Target="target='_blank'";}
	if ($GIContentTypeID==SubGalleryType_JHDBURL)			{$ResultURL	=	$GalleryItemURL; $Target="target='_blank'";}
	if ($GIContentTypeID==SubGalleryType_ContentURL)		$ResultURL .=	"?gs={$ID}&u={$GalleryItemURL}";	

	if ($IncludeHTMLTag)
	{
		$ResultTitle = $PageTitle;  // serves as default Menu Title
		if ($MenuTitle) $ResultTitle = $MenuTitle;  // override if provided in GalleryItem record
		$ResultHTMLTag = "<a href='{$ResultURL}' {$Target} >{$ResultTitle}</a>";
		return $ResultHTMLTag;
	} else
	{ // no HTML tag format, just link
		return $ResultURL;
	}
}// end gallery_link_from_GalleryItemID


function find_first_free_ID($TableName, $RangeMin, $RangeMax)
{	// no error checking for any anomalies such as the Range is already used up; assumes range min < max
	
	$ExistingCount	= db_sfq("SELECT ID FROM {$TableName} 
						WHERE ID>{$RangeMin} AND ID<{$RangeMax} "); 
	$MaxUsedID 		= db_sfq("SELECT ID FROM {$TableName} 
						WHERE ID>{$RangeMin} AND ID<{$RangeMax}
						ORDER BY ID DESC LIMIT 1");
	
	echo "<br>find_first_free_ID($TableName, $RangeMin, $RangeMax);  MaxUsedID =$MaxUsedID  <br>";
	
	if ($ExistingCount==0) return $RangeMin;  // none previously existed
	
	if ($MaxUsedID=="")   // could be NULL because range is already "full"
		return $RangeMax;    // guess/punt
		else return $MaxUsedID+1;  // next one to be used
	
	
}  //  end find_first_free_ID


function entity_file_path_from_EntityID($EntityID)
{  // no error chking, assume EntityID is valid

	
	$EntityLName = db_sfq("SELECT LName FROM tblEntities WHERE ID='{$EntityID}'");
	return EntityContentPHPBasePath."/{$EntityLName}";
}// end entity_file_path_from_EntityID

function UploadToEntityDirectory($SourceFilePath, $EntityID, $SubDir='images')
{  // given tmp file from $_POST, copy to Entity's  image/  directory and return URL to it
	if (!file_exists($SourceFilePath)) die (" Image Upload failed to server temp directory {$FilePath}; (EntityID={$EntityID}). Please use BROWSER BACK to try again or contact JHDB administrator");
	if (!$EntityID>0) die (" During Image Upload {$SourceFilePath}:  Bad (EntityID={$EntityID})   Please use BROWSER BACK to try again or contact JHDB administrator");
	$EntityFilePath = entity_file_path_from_EntityID($EntityID);
	$FileName = var_dump(basename($SourceFilePath)); // filename.ext
	$EntityFile = $EntityFilePath.$FileName;
	if (file_exists($EntityFile))
	{
		$NewFileName = date_format("Y-m-d")."-".$EntityFile;
		echo "Information: Filename {$EntityFile} already exists;  naming new image upload to {$NewFileName} <br>";	
		$EntityFile = $NewFileName;
	}
	if (copy ($SourceFilePath, $EntityFile))  
	{
		echo "<br> Successful upload copy to {$EntityFile}<br>";
		return $EntityFile; 
	}
	  else  die("Contact JHDB administrator: File Copy failed copy($SourceFilePath, $EntityFile)");
	
} // end UploadToEntityImageDirectory


function resize_image_max($image,$max_width,$max_height) 
{
    $w = imagesx($image); //current width
    $h = imagesy($image); //current height
    if ((!$w) || (!$h)) { $GLOBALS['errors'][] = 'Image couldn\'t be resized because it wasn\'t a valid image.'; return false; }

    if (($w <= $max_width) && ($h <= $max_height)) { return $image; } //no resizing needed
    
    //try max width first...
    $ratio = $max_width / $w;
    $new_w = $max_width;
    $new_h = $h * $ratio;
    
    //if that didn't work
    if ($new_h > $max_height) {
        $ratio = $max_height / $h;
        $new_h = $max_height;
        $new_w = $w * $ratio;
    }
    
    $new_image = imagecreatetruecolor ($new_w, $new_h);
    imagecopyresampled($new_image,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
    return $new_image;
} // end resize_image_max

function resize_image($method, $image_loc, $new_loc, $width, $height) 
{
    if (!is_array(@$GLOBALS['errors'])) { $GLOBALS['errors'] = array(); }
    
    if (!in_array($method,array('force','max','crop'))) { $GLOBALS['errors'][] = 'Invalid method selected.'; }
    
    if (!$image_loc) { $GLOBALS['errors'][] = 'No source image location specified.'; }
    else {
        if ((substr(strtolower($image_loc),0,7) == 'http://') || (substr(strtolower($image_loc),0,7) == 'https://')) { /*don't check to see if file exists since it's not local*/ }
        elseif (!file_exists($image_loc)) { $GLOBALS['errors'][] = 'Image source file does not exist.'; }
        $extension = strtolower(substr($image_loc,strrpos($image_loc,'.')));
        if (!in_array($extension,array('.jpg','.jpeg','.png','.gif','.bmp'))) { $GLOBALS['errors'][] = 'Invalid source file extension!'; }
    }
    
    if (!$new_loc) { $GLOBALS['errors'][] = 'No destination image location specified.'; }
    else {
        $new_extension = strtolower(substr($new_loc,strrpos($new_loc,'.')));
        if (!in_array($new_extension,array('.jpg','.jpeg','.png','.gif','.bmp'))) { $GLOBALS['errors'][] = 'Invalid destination file extension!'; }
    }

    $width = abs(intval($width));
    if (!$width) { $GLOBALS['errors'][] = 'No width specified!'; }
    
    $height = abs(intval($height));
    if (!$height) { $GLOBALS['errors'][] = 'No height specified!'; }
    
    if (count($GLOBALS['errors']) > 0) { image_echo_errors(); return false; }
    
    if (in_array($extension,array('.jpg','.jpeg'))) { $image = @imagecreatefromjpeg($image_loc); }
    elseif ($extension == '.png') { $image = @imagecreatefrompng($image_loc); }
    elseif ($extension == '.gif') { $image = @imagecreatefromgif($image_loc); }
    elseif ($extension == '.bmp') { $image = @imagecreatefromwbmp($image_loc); }
    
    if (!$image) { $GLOBALS['errors'][] = 'Image could not be generated!'; }
    else {
        $current_width = imagesx($image);
        $current_height = imagesy($image);
        if ((!$current_width) || (!$current_height)) { $GLOBALS['errors'][] = 'Generated image has invalid dimensions!'; }
    }
    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); image_echo_errors(); return false; }

    if ($method == 'force') { $new_image = resize_image_force($image,$width,$height); }
    elseif ($method == 'max') { $new_image = resize_image_max($image,$width,$height); }
    elseif ($method == 'crop') { $new_image = resize_image_crop($image,$width,$height); }
    
    if ((!$new_image) && (count($GLOBALS['errors'] == 0))) { $GLOBALS['errors'][] = 'New image could not be generated!'; }
    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); image_echo_errors(); return false; }
    
    $save_error = false;
    if (in_array($extension,array('.jpg','.jpeg'))) { imagejpeg($new_image,$new_loc) or ($save_error = true); }
    elseif ($extension == '.png') { imagepng($new_image,$new_loc) or ($save_error = true); }
    elseif ($extension == '.gif') { imagegif($new_image,$new_loc) or ($save_error = true); }
    elseif ($extension == '.bmp') { imagewbmp($new_image,$new_loc) or ($save_error = true); }
    if ($save_error) { $GLOBALS['errors'][] = 'New image could not be saved!'; }
    if (count($GLOBALS['errors']) > 0) { @imagedestroy($image); @imagedestroy($new_image); image_echo_errors(); return false; }

    imagedestroy($image);
    imagedestroy($new_image);
    
    return true;
} // end resize_image

function image_echo_errors() 
{
    if (!is_array(@$GLOBALS['errors'])) { $GLOBALS['errors'] = array('Unknown error!'); }
    foreach ($GLOBALS['errors'] as $error) { echo '<p style="color:red;font-weight:bold;">Error: '.$error.'</p>'; }
} // end image_echo_errors


function post_debug()
{
echo "<br>POST_DEBUG: <strong>SERVER</strong> VARS<br>"; print_r ($_SERVER); echo "<br><strong>POST</strong><br>"; print_r ($_POST); echo "<br><strong>SESSION</strong><br>"; print_r($_SESSION); echo "<br><strong>GET</strong><br>";  print_r($_GET);echo "</xmp><br>";
};




// permsBlurb: Consumes an optional name, and returns the permissions blurb using that name.
function permsBlurb($names=NULL) 
{
	if ($names != NULL) {
		echo "All materials are presented here with the express permission of their respective copyright owners and " . $names . ".  Materials may have been edited to remove content for which explicit permission has not been granted."; 
	} else if ($names == NULL) { // If no name is given, special case format.
		echo "All materials are presented here with the express permission of their respective copyright owners.  Materials may have been edited to remove content for which explicit permission has not been granted."; 
	}
} //end permsBlurb

// newStamp: Shorthand for a stamp in the new-stamp class, for marking new collections.
function newStamp() 
{
	echo "<div class=\"new-stamp\"><img src=\"/images/index-splashes/new.png\" /></div>" ;
} // end newStamp

// audioPlayer: Generates the audio player.  Requires the appropriate scripts to be included in the <head> of the file.
function audioPlayer() 
{
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
}// end audioPlayer




?> 