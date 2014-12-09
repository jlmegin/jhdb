<?
#BEGIN GIContentTypeTable
	// GI ContentTypes Enumerations - used by both tables: Galleries, GalleryItems  (somewhat redundant but needed as a quick convenience)
	$SubGalleryTypes = array();  // decoded enumerations for dump/debugging
												// Impl?	Display(1)	GI is Leaf		Comments
	define(	GIContentType_SubGallery,		1); //	 Y		thumbs(1)	N	
	define(	GIContentType_Image,			2); //	 No		--
	define(	GIContentType_ImageWithPlayer,	3); //	 Y		enlarger(2)	n/a
	define(	GIContentType_Audio,			4); //	 No		--
	define(	GIContentType_AudioWithPlayer,	5); //	 Y		player(2)	n/a
	define(	GIContentType_Video,			6); //	 No		--
	define(	GIContentType_VideoEmbedYouTube,7); //	 Y		thumbs(1)	Y
	define(	GIContentType_ContentText,		8);	//	 No		--							Plain Text   that is stored in a db field(not file) that needs JHDB shell for display
	define( GIContentType_ContentHTML,		9); //   No                                 HTML needs JHDB shell
	                                            //                                      Not impl: upload text or HTML into a file
	define(	GIContentType_RemoteURL,		11);//	 Y		thumbs(1)	Y				URL is NOT within JHDB
	define(	GIContentType_JHDBURL,			12);//	 Y		thumbs(1)	Y				standalone (no wrapper) URL but within this site
	define(	GIContentType_JHDBContentFile,	13);//	 Y		thumbs(1)	Y				inner content needs shell/template/masthead to surround it (ex: Bio)
	define(	GIContentType_HomePage,		  9999);//	 Soon	special		N				default top-level home page (used in gallery.php)
// Notes:
// (1) Clicking on thumb displays new child page
// (2) Clicking on thumb plays audio or enlarges image
// (3) 

#END GIContentTypeTable
	
	define(Max_ID_TopLevel_Galleries,		10); //top-level main Gallery (Collections) have IDs 1-10 (1,2,3,4 used initially) - this used for recursing upward to find top level collection ID
	
	define(ServerAccountUsername,	"jazzhist");  //owner
	define(SiteBaseURL,				"http://JazzHistoryMuseum.org/");
	define(GalleryBaseURL,			SiteBaseURL."gallery.php");
	define(PHPPathBase,				"/home/".ServerAccountUsername."/public_html/");
	
	define(EntityContentPHPBasePath,PHPPathBase."content/");
	define(EntityContentBaseURL,	SiteBaseURL."content/");
	define(EntityContentBaseRoot,	"/content/");
	
	define(NoImageAvailableURL,		SiteBaseURL."images/no-image-available.jpg");
	define(PathToBioComingSoonContent, "/content/placeholder-bio-coming-soon.html");
	
	
	
#BEGIN MinMaxRanges	
	// Conventions for HUMANS to help visually parse/comprehend what a (Gallery) ID record is being used for.<br />
	//		These are not technical constraints, but just conventions to help sort things out when looking at the raw numbers
	//		function get_ID_range_for_GalleryType(gallerytype)  uses these along with find_first_free_ID  to find a new ID when creating a new record entry
	
	define( RangeMinCollectionGalleryID		,    10000 ); // base collections to be added that are not the Main "built-in" MUSICIANS/EVENTS/MEDIA/COLLECTIONS very top-level (corresponds to nav menu)
	define( RangeMaxCollectionGalleryID		,   999999 ); // arbitrary maximums
	
	define( RangeMinEntityGalleryItemsID	,  2000000 ); // Main Collection (musician top-level) GalleryItems 
	define( RangeMaxEntityGalleryItemsID	,  2999999 ); // no real max here
//	define( RangeMinEntityGalleryID			,  1000000 ); // collections about musicians
	define( RangeMaxEntityGalleryID			,  1999999 );
//	define( RangeMinSubGalleryID			, 10000000 ); // supporting SubGalleries 
	define( RangeMaxSubGalleryID			, 19999999 ); //
	// Note: GalleryIDs & GalleryItemIDs 1(home page),2(musicians),3(events),4(media),5(collections)   are by convention hardcoded (same IDs)
//	define( RangeMinGalleryItemsID			, 20000000 ); // supporting GalleryItems 
	define( RangeMaxGalleryItemsID			, 99999999 ); // no real max here
// Following are tblEntity-related, not tblGalleries
//	define( RangeMinEntityID				,     1000 ); // ID for Entity table (musicians, venues, bands, etc)
	define( RangeMaxEntityID				,   999999 ); // arbitrary max (a large value)

// File extensions permitted for uploads
	$AllowedImageUploadExts = array("gif", "jpeg", "jpg", "png");
	$AllowedAudioUploadExts = array("mp3");
	
	
// Image sizes  (upload resizes down to fit-within these specs)
	define(MaxImageSizeHGallery,			600);  // gallery click to enlarge
	define(MaxImageSizeWGallery,			600);
	define(MaxThumbSizeH,					300);	// photo gallery & bio thumbs
	define(MaxThumbSizeW,					300);
	define(MaxMarquisSizeH,					110);	// "thumbs" (sorta) for matrix of (sub) galleries
	define(MaxMarquisSizeW,					300);
#END MinMaxRanges	

// for easier debugging in progress
function redefine($Name, $NewValue)
{
//	if (!defined($Name)) return;
//	runkit_constant_remove($Name);
	define ($Name, $NewValue);
}// end
	
	redefine( 'RangeMinGalleryItemsID'			, 20000050 );   // to help with debugging  NOT CRITICAL and does not affect anything.
	redefine( 'RangeMinEntityID'				,     1020 );
	redefine( 'RangeMinEntityGalleryID'			,  1000020 ); // collections about musicians
	redefine( 'RangeMinSubGalleryID'			, 10000020 ); // supporting SubGalleries 
	
?>