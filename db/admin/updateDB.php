<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5){
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)){
			$silent=true;
		}

		// set up tables
		setupTable('tblContributors', "create table if not exists `tblContributors` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `FName` VARCHAR(40) , `LName` VARCHAR(40) , `Email` VARCHAR(80) , `PW` VARCHAR(40) , `Organization` VARCHAR(40) , `ScopeID` INT unsigned , `ParentID` INT unsigned , `AdminLevel` TINYINT , `CreationDate` DATE , `ExpirationDate` DATE , `LastAccessDate` DATE , `Online` TINYINT ) CHARSET latin1", $silent);
		setupIndexes('tblContributors', array('ParentID'));
		setupTable('tblEntities', "create table if not exists `tblEntities` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `FName` VARCHAR(100) , `LName` VARCHAR(100) , `Birth` DATE , `Death` DATE , `DirectoryPath` VARCHAR(40) , `PrimaryGalleryID` INT unsigned , `BioURL` VARCHAR(100) , `ImageURL` VARCHAR(40) , `ImageCaption` VARCHAR(255) , `PrimaryAccessionID` INT unsigned , `TemplateID` INT unsigned , `Online` TINYINT , `CreationDate` DATE ) CHARSET latin1", $silent, array( "ALTER TABLE tblEntities ADD `field14` VARCHAR(40)","ALTER TABLE `tblEntities` CHANGE `field14` `CreationDate` VARCHAR(40) "," ALTER TABLE `tblEntities` CHANGE `CreationDate` `CreationDate` DATE "));
		setupTable('tblGalleries', "create table if not exists `tblGalleries` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Title` VARCHAR(255) , `Summary` VARCHAR(255) , `HeaderImageURL` VARCHAR(255) , `HeaderImageLinkURL` VARCHAR(255) , `EntityID` INT unsigned , `GIContentTypeID` INT unsigned , `ContributorID` VARCHAR(40) , `ThumbURL` VARCHAR(255) , `CaptionText` TEXT , `TemplateURL` VARCHAR(255) , `ItemDisplayLimit` TINYINT unsigned , `URL` VARCHAR(255) , `EmbedShowPlayer` TINYINT , `Online` TINYINT , `Sort` SMALLINT unsigned ) CHARSET latin1", $silent, array( "ALTER TABLE `tblGalleries` CHANGE `ContentTypeID` `GIContentTypeID` INT unsigned "));
		setupIndexes('tblGalleries', array('EntityID'));
		setupTable('tblAccessions', "create table if not exists `tblAccessions` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `EntityID` INT unsigned , `MediumTypeID` INT unsigned , `ContentTypeID` INT unsigned , `CaptionText` VARCHAR(255) , `URL` VARCHAR(100) , `SourceID` INT unsigned , `Credits` VARCHAR(255) , `ContributorID` INT unsigned , `StyleID` INT unsigned , `CountryID` INT unsigned , `CreationDate` DATETIME , `AccessionEntryDate` DATETIME , `ApprovalID` INT unsigned , `Comments` VARCHAR(40) , `Online` TINYINT ) CHARSET latin1", $silent);
		setupIndexes('tblAccessions', array('EntityID','MediumTypeID','ContentTypeID','ContributorID'));
		setupTable('tblGalleryItems', "create table if not exists `tblGalleryItems` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `ParentGalleryID` INT unsigned , `SubGalleryID` INT unsigned , `GIContentTypeID` INT unsigned , `PageTitle` VARCHAR(255) , `ThumbURL` VARCHAR(100) , `ThumbAltText` VARCHAR(100) , `CSSClearBothAfter` TINYINT default '-1' , `MenuTitle` VARCHAR(100) , `CaptionText` VARCHAR(255) , `URL` VARCHAR(100) , `Online` TINYINT , `HomepageFeaturedSort` TINYINT unsigned , `Sort` SMALLINT unsigned , `BioDescrPage` TINYINT ) CHARSET latin1", $silent, array( "ALTER TABLE tblGalleryItems ADD `field15` VARCHAR(40)","ALTER TABLE `tblGalleryItems` CHANGE `field15` `BioDescrPage` VARCHAR(40) "," ALTER TABLE `tblGalleryItems` CHANGE `BioDescrPage` `BioDescrPage` TINYINT "));
		setupIndexes('tblGalleryItems', array('ParentGalleryID','GIContentTypeID'));
		setupTable('tblContentTypes', "create table if not exists `tblContentTypes` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Name` VARCHAR(40) ) CHARSET latin1", $silent);
		setupTable('tblSubGalleryTypes', "create table if not exists `tblSubGalleryTypes` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Name` VARCHAR(40) ) CHARSET latin1", $silent);
		setupTable('tblSources', "create table if not exists `tblSources` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Name` VARCHAR(40) , `Comment` VARCHAR(255) ) CHARSET latin1", $silent);
		setupTable('tblTemplates', "create table if not exists `tblTemplates` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Name` VARCHAR(40) , `URL` VARCHAR(100) ) CHARSET latin1", $silent);
		setupTable('tblNavMenu', "create table if not exists `tblNavMenu` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Label` VARCHAR(40) , `HierarchyLevel` TINYINT unsigned , `URL` VARCHAR(100) , `Online` TINYINT , `Sort` SMALLINT unsigned ) CHARSET latin1", $silent);
		setupTable('tblUploadLogs', "create table if not exists `tblUploadLogs` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `ContributorID` INT unsigned , `Log` VARCHAR(255) , `UploadDate` DATETIME , `IP` VARCHAR(40) ) CHARSET latin1", $silent);
		setupIndexes('tblUploadLogs', array('ContributorID'));
		setupTable('tblSidebarEntries', "create table if not exists `tblSidebarEntries` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Title` VARCHAR(40) , `Caption` VARCHAR(255) , `AccessionID` INT unsigned , `LinkURL` VARCHAR(255) , `ThumbnailID` INT unsigned , `NewDateExpiration` DATE , `Group` TINYINT unsigned , `SortWithinGroup` TINYINT unsigned ) CHARSET latin1", $silent);
		setupIndexes('tblSidebarEntries', array('AccessionID'));
		setupTable('tblKeywords', "create table if not exists `tblKeywords` (   `ID` INT unsigned not null auto_increment , primary key (`ID`), `Keyword` VARCHAR(40) , `ReferralTypeID` INT unsigned , `ReferralID` INT unsigned ) CHARSET latin1", $silent);


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')){
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields){
		if(!is_array($arrFields)){
			return false;
		}

		foreach($arrFields as $fieldName){
			if(!$res=@db_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")){
				continue;
			}
			if(!$row=@db_fetch_assoc($res)){
				continue;
			}
			if($row['Key']==''){
				@db_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter=''){
		global $Translation;
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)){
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)){
				$oldTableName=$matches[1];
			}
		}

		if($res=@db_query("select count(1) from `$tableName`")){ // table already exists
			if($row = @db_fetch_array($res)){
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)){
					echo '<br />';
					foreach($arrAlter as $alter){
						if($alter!=''){
							echo "$alter ... ";
							if(!@db_query($alter)){
								echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
								echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
							}else{
								echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
							}
						}
					}
				}else{
					echo $Translation["table uptodate"];
				}
			}else{
				echo str_replace("<TableName>", $tableName, $Translation["couldnt count"]);
			}
		}else{ // given tableName doesn't exist

			if($oldTableName!=''){ // if we have a table rename query
				if($ro=@db_query("select count(1) from `$oldTableName`")){ // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@db_query($renameQuery)){
						echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
						echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
					}else{
						echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				}else{ // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			}else{ // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@db_query($createSQL)){
					echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
					echo '<div class="text-danger">' . $Translation['mysql said'] . db_error(db_link()) . '</div>';
				}else{
					echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
				}
			}
		}

		echo "</div>";

		$out=ob_get_contents();
		ob_end_clean();
		if(!$silent){
			echo $out;
		}
	}
?>