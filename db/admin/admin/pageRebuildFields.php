<?php
	$currDir=dirname(__FILE__);
	require("$currDir/incCommon.php");
	include("$currDir/incHeader.php");

	/* application schema as created in AppGini */
	$schema = array(   
		'tblContributors' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'FName' => array('appgini' => 'VARCHAR(40) '),
			'LName' => array('appgini' => 'VARCHAR(40) '),
			'Email' => array('appgini' => 'VARCHAR(80) '),
			'PW' => array('appgini' => 'VARCHAR(40) '),
			'Organization' => array('appgini' => 'VARCHAR(40) '),
			'ScopeID' => array('appgini' => 'INT unsigned '),
			'ParentID' => array('appgini' => 'INT unsigned '),
			'AdminLevel' => array('appgini' => 'TINYINT '),
			'CreationDate' => array('appgini' => 'DATE '),
			'ExpirationDate' => array('appgini' => 'DATE '),
			'LastAccessDate' => array('appgini' => 'DATE '),
			'Online' => array('appgini' => 'TINYINT ')
		),
		'tblEntities' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'FName' => array('appgini' => 'VARCHAR(100) '),
			'LName' => array('appgini' => 'VARCHAR(100) '),
			'Birth' => array('appgini' => 'DATE '),
			'Death' => array('appgini' => 'DATE '),
			'DirectoryPath' => array('appgini' => 'VARCHAR(40) '),
			'PrimaryGalleryID' => array('appgini' => 'INT unsigned '),
			'BioURL' => array('appgini' => 'VARCHAR(100) '),
			'ImageURL' => array('appgini' => 'VARCHAR(40) '),
			'ImageCaption' => array('appgini' => 'VARCHAR(255) '),
			'PrimaryAccessionID' => array('appgini' => 'INT unsigned '),
			'TemplateID' => array('appgini' => 'INT unsigned '),
			'Online' => array('appgini' => 'TINYINT '),
			'CreationDate' => array('appgini' => 'DATE ')
		),
		'tblGalleries' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Title' => array('appgini' => 'VARCHAR(255) '),
			'Summary' => array('appgini' => 'VARCHAR(255) '),
			'HeaderImageURL' => array('appgini' => 'VARCHAR(255) '),
			'HeaderImageLinkURL' => array('appgini' => 'VARCHAR(255) '),
			'EntityID' => array('appgini' => 'INT unsigned '),
			'GIContentTypeID' => array('appgini' => 'INT unsigned '),
			'ContributorID' => array('appgini' => 'VARCHAR(40) '),
			'ThumbURL' => array('appgini' => 'VARCHAR(255) '),
			'CaptionText' => array('appgini' => 'TEXT '),
			'TemplateURL' => array('appgini' => 'VARCHAR(255) '),
			'ItemDisplayLimit' => array('appgini' => 'TINYINT unsigned '),
			'URL' => array('appgini' => 'VARCHAR(255) '),
			'EmbedShowPlayer' => array('appgini' => 'TINYINT '),
			'Online' => array('appgini' => 'TINYINT '),
			'Sort' => array('appgini' => 'SMALLINT unsigned ')
		),
		'tblAccessions' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'EntityID' => array('appgini' => 'INT unsigned '),
			'MediumTypeID' => array('appgini' => 'INT unsigned '),
			'ContentTypeID' => array('appgini' => 'INT unsigned '),
			'CaptionText' => array('appgini' => 'VARCHAR(255) '),
			'URL' => array('appgini' => 'VARCHAR(100) '),
			'SourceID' => array('appgini' => 'INT unsigned '),
			'Credits' => array('appgini' => 'VARCHAR(255) '),
			'ContributorID' => array('appgini' => 'INT unsigned '),
			'StyleID' => array('appgini' => 'INT unsigned '),
			'CountryID' => array('appgini' => 'INT unsigned '),
			'CreationDate' => array('appgini' => 'DATETIME '),
			'AccessionEntryDate' => array('appgini' => 'DATETIME '),
			'ApprovalID' => array('appgini' => 'INT unsigned '),
			'Comments' => array('appgini' => 'VARCHAR(40) '),
			'Online' => array('appgini' => 'TINYINT ')
		),
		'tblGalleryItems' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'ParentGalleryID' => array('appgini' => 'INT unsigned '),
			'SubGalleryID' => array('appgini' => 'INT unsigned '),
			'GIContentTypeID' => array('appgini' => 'INT unsigned '),
			'PageTitle' => array('appgini' => 'VARCHAR(255) '),
			'ThumbURL' => array('appgini' => 'VARCHAR(100) '),
			'ThumbAltText' => array('appgini' => 'VARCHAR(100) '),
			'CSSClearBothAfter' => array('appgini' => 'TINYINT default \'-1\' '),
			'MenuTitle' => array('appgini' => 'VARCHAR(100) '),
			'CaptionText' => array('appgini' => 'VARCHAR(255) '),
			'URL' => array('appgini' => 'VARCHAR(100) '),
			'Online' => array('appgini' => 'TINYINT '),
			'HomepageFeaturedSort' => array('appgini' => 'TINYINT unsigned '),
			'Sort' => array('appgini' => 'SMALLINT unsigned '),
			'BioDescrPage' => array('appgini' => 'TINYINT ')
		),
		'tblContentTypes' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Name' => array('appgini' => 'VARCHAR(40) ')
		),
		'tblSubGalleryTypes' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Name' => array('appgini' => 'VARCHAR(40) ')
		),
		'tblSources' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Name' => array('appgini' => 'VARCHAR(40) '),
			'Comment' => array('appgini' => 'VARCHAR(255) ')
		),
		'tblTemplates' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Name' => array('appgini' => 'VARCHAR(40) '),
			'URL' => array('appgini' => 'VARCHAR(100) ')
		),
		'tblNavMenu' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Label' => array('appgini' => 'VARCHAR(40) '),
			'HierarchyLevel' => array('appgini' => 'TINYINT unsigned '),
			'URL' => array('appgini' => 'VARCHAR(100) '),
			'Online' => array('appgini' => 'TINYINT '),
			'Sort' => array('appgini' => 'SMALLINT unsigned ')
		),
		'tblUploadLogs' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'ContributorID' => array('appgini' => 'INT unsigned '),
			'Log' => array('appgini' => 'VARCHAR(255) '),
			'UploadDate' => array('appgini' => 'DATETIME '),
			'IP' => array('appgini' => 'VARCHAR(40) ')
		),
		'tblSidebarEntries' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Title' => array('appgini' => 'VARCHAR(40) '),
			'Caption' => array('appgini' => 'VARCHAR(255) '),
			'AccessionID' => array('appgini' => 'INT unsigned '),
			'LinkURL' => array('appgini' => 'VARCHAR(255) '),
			'ThumbnailID' => array('appgini' => 'INT unsigned '),
			'NewDateExpiration' => array('appgini' => 'DATE '),
			'Group' => array('appgini' => 'TINYINT unsigned '),
			'SortWithinGroup' => array('appgini' => 'TINYINT unsigned ')
		),
		'tblKeywords' => array(   
			'ID' => array('appgini' => 'INT unsigned not null primary key auto_increment '),
			'Keyword' => array('appgini' => 'VARCHAR(40) '),
			'ReferralTypeID' => array('appgini' => 'INT unsigned '),
			'ReferralID' => array('appgini' => 'INT unsigned ')
		)
	);

	$table_captions = getTableList();

	/* function for preparing field definition for comparison */
	function prepare_def($def){
		$def = trim($def);
		$def = strtolower($def);

		/* ignore length for int data types */
		$def = preg_replace('/int\w*\([0-9]+\)/', 'int', $def);

		/* make sure there is always a space before mysql words */
		$def = preg_replace('/(\S)(unsigned|not null|binary|zerofill|auto_increment|default)/', '$1 $2', $def);

		/* treat 0.000.. same as 0 */
		$def = preg_replace('/([0-9])*\.0+/', '$1', $def);

		return $def;
	}

	/* process requested fixes */
	$fix_table = (isset($_GET['t']) ? $_GET['t'] : false);
	$fix_field = (isset($_GET['f']) ? $_GET['f'] : false);

	if($fix_table && $fix_field && isset($schema[$fix_table][$fix_field])){
		$field_added = $field_updated = false;

		// field exists?
		$res = sql("show columns from `{$fix_table}` like '{$fix_field}'", $eo);
		if($row = db_fetch_assoc($res)){
			// modify field
			$qry = "alter table `{$fix_table}` modify `{$fix_field}` {$schema[$fix_table][$fix_field]['appgini']}";
			sql($qry, $eo);
			$field_updated = true;
		}else{
			// create field
			$qry = "alter table `{$fix_table}` add column `{$fix_field}` {$schema[$fix_table][$fix_field]['appgini']}";
			sql($qry, $eo);
			$field_added = true;
		}
	}

	foreach($table_captions as $tn => $tc){
		$eo['silentErrors'] = true;
		$res = sql("show columns from `{$tn}`", $eo);
		if($res){
			while($row = db_fetch_assoc($res)){
				if(!isset($schema[$tn][$row['Field']]['appgini'])) continue;
				$field_description = strtoupper(str_replace(' ', '', $row['Type']));
				$field_description = str_ireplace('unsigned', ' unsigned', $field_description);
				$field_description = str_ireplace('zerofill', ' zerofill', $field_description);
				$field_description = str_ireplace('binary', ' binary', $field_description);
				$field_description .= ($row['Null'] == 'NO' ? ' not null' : '');
				$field_description .= ($row['Key'] == 'PRI' ? ' primary key' : '');
				$field_description .= ($row['Key'] == 'UNI' ? ' unique' : '');
				$field_description .= ($row['Default'] != '' ? " default '" . makeSafe($row['Default']) . "'" : '');
				$field_description .= ($row['Extra'] == 'auto_increment' ? ' auto_increment' : '');

				$schema[$tn][$row['Field']]['db'] = '';
				if(isset($schema[$tn][$row['Field']])){
					$schema[$tn][$row['Field']]['db'] = $field_description;
				}
			}
		}
	}
?>

<?php if($field_added || $field_updated){ ?>
	<div class="alert alert-info alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<i class="glyphicon glyphicon-info-sign"></i>
		An attempt to <?php echo ($field_added ? 'create' : 'update'); ?> the field <i><?php echo $fix_field; ?></i> in <i><?php echo $fix_table; ?></i> table
		was made by executing this query:
		<pre><?php echo $qry; ?></pre>
		Results are shown below.
	</div>
<?php } ?>

<div class="page-header"><h1>
	View/Rebuild fields
	<button type="button" class="btn btn-default" id="show_deviations_only"><i class="glyphicon glyphicon-eye-close"></i> Show deviations only</button>
	<button type="button" class="btn btn-default hidden" id="show_all_fields"><i class="glyphicon glyphicon-eye-open"></i> Show all fields</button>
</h1></div>

<p class="lead">This page compares the tables and fields structure/schema as designed in AppGini to the actual database structure and allows you to fix any deviations.</p>

<div class="alert summary"></div>
<table class="table table-responsive table-hover table-striped">
	<thead><tr>
		<th></th>
		<th>Field</th>
		<th>AppGini definition</th>
		<th>Current definition in the database</th>
		<th></th>
	</tr></thead>

	<tbody>
	<?php foreach($schema as $tn => $fields){ ?>
		<tr class="text-info"><td colspan="5"><h4 data-placement="left" data-toggle="tooltip" title="<?php echo $tn; ?> table"><i class="glyphicon glyphicon-th-list"></i> <?php echo $table_captions[$tn]; ?></h4></td></tr>
		<?php foreach($fields as $fn => $fd){ ?>
			<?php $diff = ((prepare_def($fd['appgini']) == prepare_def($fd['db'])) ? false : true); ?>
			<?php $no_db = ($fd['db'] ? false : true); ?>
			<tr class="<?php echo ($diff ? 'highlight' : 'field_ok'); ?>">
				<td><i class="glyphicon glyphicon-<?php echo ($diff ? 'remove text-danger' : 'ok text-success'); ?>"></i></td>
				<td><?php echo $fn; ?></td>
				<td class="<?php echo ($diff ? 'bold text-success' : ''); ?>"><?php echo $fd['appgini']; ?></td>
				<td class="<?php echo ($diff ? 'bold text-danger' : ''); ?>"><?php echo thisOr($fd['db'], "Doesn't exist!"); ?></td>
				<td>
					<?php if($diff && $no_db){ ?>
						<a href="pageRebuildFields.php?t=<?php echo $tn; ?>&f=<?php echo $fn; ?>" class="btn btn-success btn-xs btn_create" data-toggle="tooltip" data-placement="top" title="Create the field by running an ADD COLUMN query."><i class="glyphicon glyphicon-plus"></i> Create it</a>
					<?php }elseif($diff){ ?>
						<a href="pageRebuildFields.php?t=<?php echo $tn; ?>&f=<?php echo $fn; ?>" class="btn btn-warning btn-xs btn_update" data-toggle="tooltip" title="Fix the field by running an ALTER COLUMN query so that its definition becomes the same as that in AppGini."><i class="glyphicon glyphicon-cog"></i> Fix it</a>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
	</tbody>
</table>
<div class="alert summary"></div>

<style>
	.bold{ font-weight: bold; }
	.highlight, .highlight td{ background-color: #FFFFE0 !important; }
	[data-toggle="tooltip"]{ display: block !important; }
</style>

<script>
	jQuery(function(){
		jQuery('[data-toggle="tooltip"]').tooltip();

		jQuery('#show_deviations_only').click(function(){
			jQuery(this).addClass('hidden');
			jQuery('#show_all_fields').removeClass('hidden');
			jQuery('.field_ok').hide();
		});

		jQuery('#show_all_fields').click(function(){
			jQuery(this).addClass('hidden');
			jQuery('#show_deviations_only').removeClass('hidden');
			jQuery('.field_ok').show();
		});

		jQuery('.btn_update').click(function(){
			return confirm("DANGER!! In some cases, this might lead to data loss, truncation, or corruption. It might be a better idea sometimes to update the field in AppGini to match that in the database. Would you still like to continue?");
		});

		var count_updates = jQuery('.btn_update').length;
		var count_creates = jQuery('.btn_create').length;
		if(!count_creates && !count_updates){
			jQuery('.summary').addClass('alert-success').html('No deviations found. All fields OK!');
		}else{
			jQuery('.summary')
				.addClass('alert-warning')
				.html(
					'Found ' + count_creates + ' non-existing fields that need to be created.<br>' +
					'Found ' + count_updates + ' deviating fields that might need to be updated.'
				);
		}
	});
</script>

<?php
	include("$currDir/incFooter.php");
?>
