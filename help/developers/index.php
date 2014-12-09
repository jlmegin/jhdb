<?
require("../../db/lib-db.php");
require("../../db/lib-utils.php");  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JHDB Developer Help File information</title>
<style type="text/css">
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
}
</style>
</head>

<body>
<p><strong>Jazz History Database Developer/Admin Reference Materials</strong></p>
<p>A <strong>Gallery</strong> is a Collection of (a Grouping mechanism for) <strong>Gallery Items</strong>. All Galleries except the HomePage (GalleryItemID==1) are SubGalleries (i.e., a GalleryItem points to them). All Galleries have at least one GalleryItem.</p>
<p><img src="JHDB example of gallery hierarchy IDs.png" width="1600" height="3071" />
  <img src="JHDB-page-layout-overview.png" width="1200" height="636" /></p>
<p><img src="JHDB-HomePage-layout-overview.png" width="1094" height="564" /></p>
<p><img src="JHDB-(Sub)Gallery-layout-overview.png" width="1094" height="600" /></p>
<p>GALLERY.PHP - DISPLAYS A GIVEN GalleryItemID (?gi=nnnn)</p>
<p><img src="GI display request possibilities.png" width="1125" height="1125" /></p>
<p><img src="GalleryItemTypes.png" width="1651" height="1050" /></p>
<p>&nbsp;</p>
<p><img src="JHDB adding galleries and uploading1.png" width="1275" height="1651" /></p>
<p><img src="JHDB adding galleries and uploading2.png" width="1275" height="1651" /></p>
<p><img src="JHDB database schema with relationships (prelim).png" width="1320" height="1019" /></p>
<table width="886" cellpadding="0" cellspacing="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td width="153">&nbsp;</td>
    <td></td>
    
  </tr>
  <tr>
    <td height="24" colspan="2" bgcolor="#FFFF00"><strong>tblGalleries</strong></td>
    <td bgcolor="#FFFF00"></td>
    <td width="705" bgcolor="#FFFF00">(Collections)</td>
  </tr><tr>
    <td width="10" height="73">&nbsp;</td>
    <td width="153">ID</td>
    <td></td>
    <td width="705">GalleryID is pointed to from    tblGalleryItems.SubGalleryID (to show that that Gallery Item belongs to this    Gallery)<br />
      See ID numbering range conventions near bottom of this page.     <br />
      ALL These ranges are for human    parsing/identification/categorization/debugging</td>
  </tr><tr>
    <td height="33">&nbsp;</td>
    <td>Title</td>
    <td></td>
    <td><p>Text of Title of the Gallery, displayed in caps at the top of marquis thumbnails; <br />
    e.g.,    MUSICIANS &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  RICH ARDIZZONE COLLECTION</p></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Summary</td>
    <td></td>
    <td>(unused?)</td>
  </tr><tr>
    <td bgcolor="#FFFFFF">&nbsp;</td>
    <td bgcolor="#FFFFFF">HeaderImageURL</td>
    <td bgcolor="#FFFFFF"></td>
    <td bgcolor="#FFFFFF">Double Width header image --  done in tblSiteParams</td>
  </tr><tr>
    <td bgcolor="#CCCCCC">&nbsp;</td>
    <td bgcolor="#CCCCCC">HeaderImageLinkURL</td>
    <td bgcolor="#CCCCCC"></td>
    <td bgcolor="#CCCCCC"></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>EntityID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>GalleryUsageTypeID</td>
    <td></td>
    <td>Collection Directory(containing SubGalleries); &nbsp;photo, recording, album, discography, article, interview,    visual arts, URL links, poetry</td>
  </tr>
  <tr>
    <td height="39">&nbsp;</td>
    <td>GIContentTypeID</td>
    <td></td>
    <td>What is the content for the Gallery Items directly IN this gallery (will be consistent for subgalleries; mixed for higher level galleries)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ThumbURL</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>CaptionText</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>TemplateURL</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ItemDisplayLimit</td>
    <td></td>
    <td>Max # Items shown in this view    (GET param can override) Null= unlimited  (not yet implemented in gallery.php)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>URL</td>
    <td></td>
    <td>or TemplateID?</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>EmbedShowPlayer</td>
    <td></td>
    <td>Audio player; embed youtube</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ContributorID</td>
    <td></td>
    <td>null if admin or &quot;original&quot;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Sort</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="22" colspan="3">&nbsp;</td>
    <td></td>
    
  </tr>
  <tr>
    <td height="25" colspan="3" bgcolor="#FFFF00"><strong>tblGalleryItems</strong></td>
    <td bgcolor="#FFFF00">Records are Instances (items) within a Gallery (Collection)</td>

  </tr><tr>
    <td height="49">&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td>This field is inconsequential (not used anywhere except as a table record    number)<br />
    However, it IS used to categorize (steer) the GI for the Crumb Menus</td>
  </tr><tr>
    <td height="49">&nbsp;</td>
    <td>ParentGalleryID</td>
    <td></td>
    <td>which gallery (collection) does this item belong in. if more than one,    dupl record in this table with other galleryID</td>
  </tr><tr>
    <td height="55">&nbsp;</td>
    <td>SubGalleryID</td>
    <td></td>
    <td>THIS GalleryItem itself points to the SubGalleryID Gallery.    Galleries can be displayed in many(more than one) parent Gallery, but    a separate (another) GalleryItem is needed to insulate. <br />
    Note if this GalleryItem is a leaf node, SubGalleryID=NULL </td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>GIContentTypeID</td>
    <td></td>
    <td>another hierarchical (sub)  gallery, OR it is a leaf node (image/audio/video/HTML+Image)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>PageTitle</td>
    <td></td>
    <td>Used for Title over thumbnail, and possible Menu Title(below)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Summary</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ThumbURL</td>
    <td></td>
    <td>if NULL, use auto-generated thumb</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ThumbAltText</td>
    <td></td>
    <td>AltTxt for tooltip hover display in browser (&amp; SEO)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>CaptionText</td>
    <td></td>
    <td>Text displayed below &quot;thumb&quot;</td>
  </tr>
  <tr>
    <td height="44">&nbsp;</td>
    <td>HeaderImageURL</td>
    <td></td>
    <td>Specially-sized image displayed at the tops of Galleries; <br />
    the 4 top-level MAIN COLLECTIONS use a larger width image</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>CSSClearBothAfter</td>
    <td></td>
    <td>for replicating the table-layout of original HTML JHDB, unclear if will be used: if =1, this starts the second column</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>MenuTitle</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>URL</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>HomepageFeaturedSort</td>
    <td></td>
    <td>&gt;0 means display this on JHDB home page, and use this (integer) for the sort order [not implemented]</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <col width="33" />
  <col width="172" />
  <col width="26" />
  <col width="882" />
  <tr>
    <td colspan="3"><strong>tblContributors</strong></td>
    <td width="705">this is the same as the existing User table</td>
  </tr>
  <tr>
    <td width="10"></td>
    <td width="153">ID</td>
    <td width="16">&nbsp;</td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>FName</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Lname</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Email</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>PW</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ScopeID</td>
    <td></td>
    <td>Scope of Editing</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ParentID</td>
    <td></td>
    <td>for inheriting permissions, and access heritage/lineage</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>AdminLevel</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>CreationDate</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ExpirationDate</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="39" colspan="3"><strong>tblEntities </strong></td>
    <td>people(composers, performers, artists, interviewers, writers, etc),    bands, events, venues   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
      See ID numbering range
    Conventions near bottom of this page</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>EntityTypeID</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Fname</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>LName</td>
    <td></td>
    <td>or Band/group name</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Birth</td>
    <td></td>
    <td>if a person; birth year;   if    group, formation year</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Death</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>DirectoryPath</td>
    <td></td>
    <td>PHP (filesystem) path starting AFTER base (web root) &nbsp;&nbsp;http://jazz...org/content/(append DirectoryPath here)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>PrimaryGalleryID</td>
    <td></td>
    <td>Top-level (main) Gallery (collection) for this person/group</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Bio URL</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ImageURL</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ImageCaption</td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>PrimaryAccessionsID</td>
    <td></td>
    <td>Headshot, Band picture  &nbsp;&nbsp;&nbsp;Unused - use instead ImageURL field</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>TemplateID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblAccessions</strong></td>
    <td>Media Holdings:  a record here for each image, audio, video,    etc.</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>EntityID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>MediumTypeID</td>
    <td></td>
    <td>image, audio, video  (future: 3D, 360panorama, hologram,    virtualRoom)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ContentTypeID</td>
    <td></td>
    <td>photo, recording, album, discography, article, interview, visual arts,    URL links, poetry</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>CaptionText</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>URL</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>SourceID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Credits</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ContributorID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td height="84">&nbsp;</td>
    <td>StyleID</td>
    <td></td>
    <td>Genre: enumeration of: <br />
      See enumerations near bottom of this page.<br />
standards,    ragtime, third stream, dixieland,     Afro-Cuban jazz, West Coast jazz, ska jazz, cool jazz, Indo jazz,    avant-garde jazz, soul jazz, modal jazz, chamber jazz, free jazz, Latin jazz,    smooth jazz, jazz fusion and jazz rock, jazz funk, loft jazz, punk jazz, acid    jazz, ethno jazz, jazz rap, cyber jazz, M-Base and nu jazz.</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>CountryID/EthnicityID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>CreationDate</td>
    <td></td>
    <td>Date of picture taken, audio recorded, composition written, etc</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>AccessionEntryDate</td>
    <td></td>
    <td>DateTime when uploaded/entered</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ApprovalID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Comments</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>MenuTitle</td>
    <td></td>
    <td>label used for nav menu within each page; if null  use Page Title</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>CaptionText</td>
    <td></td>
    <td>if NULL, use caption from media table</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>URL</td>
    <td></td>
    <td>if not a SubGallery, then link to page content</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>HomepageFeaturedSort</td>
    <td></td>
    <td>Display sequence for the home page marquis summary samples</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Sort</td>
    <td></td>
    <td>overall sorting for other display contexts</td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblContentTypes</strong></td>
    <td>see enumerations at the bottom of this page - live dump of db</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td>photo, recording, album, discography, article, interview, visual arts,    URL links, poetry</td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblMediumTypes</strong></td>
    <td>&nbsp;</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td>image, audio, video</td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblSources</strong></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td height="37">&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td>web, museum, photographer, album, library, youtube (where did you get    accession from?) (NOT who contributed/submitted it)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Comment</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblTemplates</strong></td>
    <td>web display format</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>URL</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblNavMenu</strong></td>
    <td>not sure if need to go this far for site programmability?</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Label</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>HierarchyLevel</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>URL</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Online</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Sort</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblUploadLog</strong></td>
    <td>capture upload activity</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>IP</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ContributorID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Log</td>
    <td></td>
    <td>concatenate upload info into long string, on Log Entry for each    SUBMIT(could be multiple files, etc)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>UploadDate</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblSidebarEntries</strong></td>
    <td>Not yet implemented  (currently hardcoded HTML)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Title</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Caption</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>AccessionID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>LinkURL</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ThumbnailID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>NewDateExpiration</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Group</td>
    <td></td>
    <td>top or bottom chunk of entries in column</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>SortWithinGroup</td>
    <td></td>
    <td>sort with top (overridden with random display from PHP)</td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblKeywords</strong></td>
    <td>Aliases for search</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Keyword</td>
    <td></td>
    <td>New keyword to be catalogued as an alias</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ReferralTypeID</td>
    <td></td>
    <td>Gallery, Entity, MediaHolding</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ReferralID</td>
    <td></td>
    <td>ID within table (target of alias referral)</td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblInstrumentations</strong></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblSiteParams</strong></td>
    <td>(see enumerations near bottom of this page)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td>(inconsequential, never referenced; table is indexed by Name)</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td>Name of parameter</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Value1 .. Value3</td>
    <td></td>
    <td>up to three associated values</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Comment</td>
    <td></td>
    <td>Explanation/instructions/reminders - unused in code, just an aid in the db table admin interface</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblStyleTypes</strong> (<strong>Genres</strong>)</td>
    <td>(see enumerations near bottom of this page)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Name</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblApprovals</strong></td>
    <td>Admin/editors who approve Galleries/Accessions --  one entry in this table can be used many    times (e.g., for items in a given collection)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ApproverID</td>
    <td></td>
    <td>(Admin or Contributor with privs)</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ApprovalDateTime</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Comments</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3"><strong>tblGalleryOwners</strong></td>
    <td>permissions for editing, etc</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>ContributorID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>GalleryID</td>
    <td></td>
    <td></td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>Level</td>
    <td></td>
    <td>permission type</td>
  </tr><tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<p>&nbsp;</p>
<p><strong>IMAGE SIZE INVENTORY</strong></p>
<table width="800" border="0" cellpadding="10">
  <tr>
    <td width="177"><strong>Image</strong></td>
    <td width="72"><strong>Width</strong></td>
    <td width="73"><strong>Heighth</strong></td>
    <td width="335"><strong>Displayed</strong></td>
  </tr>
  <tr>
    <td>HeaderImage (Musicians)</td>
    <td>375-400</td>
    <td>~100</td>
    <td>tops of Gallery Directories</td>
  </tr>
  <tr>
    <td>HeaderImage <br />
    (4 top-level Collections)</td>
    <td>660</td>
    <td>300</td>
    <td>top of top-level Collections<br />
      (fills width of main column)</td>
  </tr>
  <tr>
    <td>Marquis thumbnails</td>
    <td>300</td>
    <td>110</td>
    <td>Gallery Directory Contents Thumbnails for each SubGallery</td>
  </tr>
  <tr>
    <td>SubGallery Content Thumbs</td>
    <td>300</td>
    <td>110</td>
    <td>Contents Thumbnails for each GalleryItem</td>
  </tr>
</table>
<!-- p>&nbsp;</p>
<p><em>excerpt (may be out of date) from lib-JHDB-definitions.php</em>:<br />
  <strong>ID RANGE CONVENTIONS for tblGallery
and tblGalleryItems </strong></p>
<p> // Conventions for HUMANS to help visually parse/comprehend what a (Gallery) ID record is being used for.&lt;br /&gt;<br />
//		These are not technical constraints, but just conventions to help sort things out when looking at the raw numbers<br />
//		function get_ID_range_for_GalleryType(gallerytype)  uses these along with find_first_free_ID  to find a new ID when creating a new record entry<br />
<br />
define( Range<strong>Min</strong>CollectionGalleryID		,    &nbsp;&nbsp;10000 ); // base collections to be added that are not the Main &quot;built-in&quot; MUSICIANS/EVENTS/MEDIA/COLLECTIONS very top-level (corresponds to nav menu)<br />
define( RangeMaxCollectionGalleryID		,    999999 ); // arbitrary maximums<br />
<br />
define( RangeMinEntityGalleryItemsID, 	2000000 ); // Main Collection (musician top-level) GalleryItems <br />
define( RangeMaxEntityGalleryItemsID,   2999999 ); // no real max here<br />
define( Range<strong>Min</strong>EntityGalleryID			,  1000000 ); // collections about musicians<br />
define( Range<strong>Max</strong>EntityGalleryID			,  1999999 );<br />
define( Range<strong>Min</strong>SubGalleryID			, 10000000 ); // supporting SubGalleries <br />
define( Range<strong>Max</strong>SubGalleryID			, 19999999 ); //<br />
define( Range<strong>Min</strong>GalleryItemsID			, 20000000 ); // supporting GalleryItems <br />
define( Range<strong>Max</strong>GalleryItemsID			, 99999999 ); // no real max here<br />
// Following is tblEntity-related, not tblGalleries<br />
define( Range<strong>Min</strong>EntityID				,     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1000 ); // ID for Entity table (musicians, venues, bands, etc)<br />
define( Range<strong>Max</strong>EntityID				,   999999 ); // arbitrary max (a large value)</p>
<p>&nbsp;</p-->




<p><strong>ID RANGE & IMAGE SIZE DEFINITIONS:</strong></p>
<pre><? 
	display_code_comments("../../db/lib-JHDB-definitions.php", "MinMaxRanges");
	?>
</pre>

<p><strong>GI Content Types:</strong></p>
<pre><? 
	display_code_comments("../../db/lib-JHDB-definitions.php", "GIContentTypeTable");
	?>
</pre>

<p><strong>GALLERY DISPLAY FORMATS</strong></p>
<pre><? 
	display_code_comments("../../gallery.php", "GALLERY DISPLAY TABLE");
	?>
</pre>


<p><strong>GALLERY CRUMB MENU DISPLAY FORMAT SUMMARY TABLE</strong></p>
<pre><? 
	display_code_comments("../../gallery.php", "CRUMB BEHAVIOR SUMMARY TABLE");
	?>
</pre>


<p>&nbsp;</p>
<p><em><strong>ENUMERATION TABLES:</strong></em></p><p>&nbsp;</p>
<?
function enumerate_dump($Table, $Name='Name', $N1='', $N2='', $N3='', $N4='')
{
	$Results = db_sql("SELECT * FROM {$Table}");
	
	?> <strong><?=$Table?></strong><br /><table> <?
	while ($Row=mysqli_fetch_assoc($Results))
	{
		?>
        <tr><td><?=$Row['ID']?></td><td><?=$Row[$Name]?></td>
        <? 
		if ($N1) echo "<td>{$Row[$N1]}</td> \n";
		if ($N2) echo "<td>{$Row[$N2]}</td> \n";
		if ($N3) echo "<td>{$Row[$N3]}</td> \n";
		if ($N4) echo "<td>{$Row[$N4]}</td> \n";
		?>
  </tr>
        <?
	}// end while
	?> </table> <br /><?
	
}// end enumerate
enumerate_dump('tblGalleryUsageTypes');

enumerate_dump('tblEntityTypes', 'Category');

enumerate_dump('tblMediaTypes');

enumerate_dump('tblGIContentTypes');

enumerate_dump('tblStyleTypes');

enumerate_dump('tblSiteParams', 'Name', 'Value1','Value2','Value3','Comment' );



?>


</body>
</html>