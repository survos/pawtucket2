<?php
/* ----------------------------------------------------------------------
 * themes/default/views/bundles/ca_objects_default_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013-2015 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
	$t_object = 			$this->getVar("item");
	$va_comments = 			$this->getVar("comments");
	$va_tags = 				$this->getVar("tags_array");
	$vn_comments_enabled = 	$this->getVar("commentsEnabled");
	$vn_share_enabled = 	$this->getVar("shareEnabled");

?>
<div class="container">
<div class="row">
	<div class='col-xs-12 navTop'><!--- only shown at small screen size -->
		{{{previousLink}}}{{{resultsLink}}}{{{nextLink}}}
	</div><!-- end detailTop -->
	<div class='navLeftRight col-xs-1 col-sm-1 col-md-1 col-lg-1'>
		<div class="detailNavBgLeft">
			{{{previousLink}}}{{{resultsLink}}}
		</div><!-- end detailNavBgLeft -->
	</div><!-- end col -->
	<div class='col-xs-12 col-sm-10 col-md-10 col-lg-10'>
		<div class="container"><div class="row">
			<div class='col-sm-6 col-md-6 col-lg-5 col-lg-offset-1'>
				{{{representationViewer}}}
				
				
				<div id="detailAnnotations"></div>
				
				<?php print caObjectRepresentationThumbnails($this->request, $this->getVar("representation_id"), $t_object, array("returnAs" => "bsCols", "linkTo" => "carousel", "bsColClasses" => "smallpadding col-sm-3 col-md-3 col-xs-4")); ?>
				
<?php
				# Comment and Share Tools
				if ($vn_comments_enabled | $vn_share_enabled) {
						
					print '<div id="detailTools">';
					if ($vn_comments_enabled) {
?>				
						<div class="detailTool"><a href='#' onclick='jQuery("#detailComments").slideToggle(); return false;'><span class="glyphicon glyphicon-comment"></span>Comments and Tags (<?php print sizeof($va_comments) + sizeof($va_tags); ?>)</a></div><!-- end detailTool -->
						<div id='detailComments'><?php print $this->getVar("itemComments");?></div><!-- end itemComments -->
<?php				
					}
					#if ($vn_share_enabled) {
					#	print '<div class="detailTool"><span class="glyphicon glyphicon-share-alt"></span>'.$this->getVar("shareLink").'</div><!-- end detailTool -->';
					#}
					print '</div><!-- end detailTools -->';
				}				
?>
				<div class="sharethis-inline-share-buttons"></div>
			</div><!-- end col -->
			
			<div class='col-sm-6 col-md-6 col-lg-5'>
				<H4>{{{ca_objects.preferred_labels.name}}}</H4>
				<H6>{{{<unit>^ca_objects.type_id</unit>}}}</H6>
				<HR>
<?php
				if ($va_alt_title = $t_object->get('ca_objects.nonpreferred_labels', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Alternate Titles</H6>".$va_alt_title."</div>";
				}
				if ($va_alt_id = $t_object->getWithTemplate('<ifcount min="1" code="ca_objects.other_identifiers"><unit delimiter=", "><ifdef code="ca_objects.other_identifiers.legacy_identifier">^ca_objects.other_identifiers.legacy_identifier (^ca_objects.other_identifiers.other_identifier_type)</ifdef></unit></ifcount>')) { 
					print "<div class='unit'><h6>Other Identifiers</H6>".$va_alt_id."</div>";
				}
				if ($va_date = $t_object->getWithTemplate('<ifcount min="1" code="ca_objects.date.date_value"><unit delimiter="<br/>"><ifdef code="ca_objects.date.date_value">^ca_objects.date.date_value (^ca_objects.date.date_types)</ifdef></unit></ifcount>')) {
					print "<div class='unit'><h6>Date</H6>".$va_date."</div>";
				}
				if ($va_description = $t_object->get('ca_objects.public_description', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Description</H6>".$va_description."</div>";
				}	
				if ($va_credit_line = $t_object->get('ca_objects.credit_line', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Credit Line</H6>".$va_credit_line."</div>";
				}
				if ($va_exhibition_label = $t_object->get('ca_objects.exhibition_label', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Exhibition Label</H6>".$va_exhibition_label."</div>";
				}
				if ($va_dimensions = $t_object->getWithTemplate('<ifcount code="ca_objects.dimensions" min="1"><unit delimiter="<br/>"><ifdef code="ca_objects.dimensions.dimensions_height">^ca_objects.dimensions.dimensions_height H</ifdef><ifdef code="ca_objects.dimensions.dimensions_height,ca_objects.dimensions.dimensions_width"> X </ifdef><ifdef code="ca_objects.dimensions.dimensions_width">^ca_objects.dimensions.dimensions_width W</ifdef><ifdef code="ca_objects.dimensions.dimensions_depth,ca_objects.dimensions.dimensions_width"> X </ifdef><ifdef code="ca_objects.dimensions.dimensions_depth">^ca_objects.dimensions.dimensions_depth D</ifdef><ifdef code="ca_objects.dimensions.dimensions_depth,ca_objects.dimensions.dimensions_length"> X </ifdef><ifdef code="ca_objects.dimensions.dimensions_length">^ca_objects.dimensions.dimensions_length L</ifdef><ifdef code="ca_objects.dimensions.dimensions_weight">, ^ca_objects.dimensions.dimensions_weight Weight</ifdef><ifdef code="ca_objects.dimensions.dimensions_diameter">, ^ca_objects.dimensions.dimensions_diameter Diameter</ifdef><ifdef code="ca_objects.dimensions.dimensions_circumference">, ^ca_objects.dimensions.dimensions_circumference Circumference</ifdef><ifdef code="ca_objects.dimensions.measurement_notes"><br/>Measurement Notes: ^ca_objects.dimensions.measurement_notes</ifdef><ifdef code="ca_objects.dimensions.measurement_type"><br/>Measurement Types: ^ca_objects.dimensions.measurement_type</ifdef></unit></ifcount>')) {
					print "<div class='unit'><h6>Dimensions</H6>".$va_dimensions."</div>"; 
				}
				if ($va_accessory = $t_object->get('ca_objects.accessory', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Accessory</H6>".$va_accessory."</div>";
				}
				if ($va_material = $t_object->get('ca_objects.material', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Material</H6>".$va_material."</div>";
				}	
				if ($va_technique = $t_object->get('ca_objects.technique', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Technique</H6>".$va_technique."</div>";
				}
				if ($va_medium = $t_object->get('ca_objects.medium', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Medium</H6>".$va_medium."</div>";
				}
				if ($va_format = $t_object->get('ca_objects.format', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Format</H6>".$va_format."</div>";
				}	
				if ($va_inscription = $t_object->get('ca_objects.inscription', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Inscription</H6>".$va_inscription."</div>";
				}	
				if ($va_signature = $t_object->getWithTemplate('<unit delimiter="<br/>"><ifdef code="ca_objects.signature.signedname"><b>name:</b> ^ca_objects.signature.signedname </ifdef><ifdef code="ca_objects.signature.signloc"><b>location:</b> ^ca_objects.signature.signloc</ifdef></unit>')) {
					print "<div class='unit'><h6>Signature</H6>".$va_signature."</div>";  
				}
				if ($va_geo_notes = $t_object->get('ca_objects.geo_notes', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Geographic Notes</H6>".$va_geo_notes."</div>";
				}
				if ($va_culture = $t_object->get('ca_objects.culture', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Culture</H6>".$va_culture."</div>";
				}	
				if ($va_school = $t_object->get('ca_objects.school', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>School</H6>".$va_school."</div>";
				}
				if ($va_style = $t_object->get('ca_objects.style', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Style</H6>".$va_style."</div>";
				}	
				if ($va_rights = $t_object->getWithTemplate('<unit delimiter="<br/>"><ifdef code="ca_objects.rights.rightsText"><b>Rights:</b> ^ca_objects.rights.rightsText</ifdef><ifdef code="ca_objects.rights.rightsHolder"><br/><b>Rights Holder:</b> ^ca_objects.rights.rightsHolder</ifdef><ifdef code="ca_objects.rights.copyrightStatement"><br/><b>Copyright Statement:</b> ^ca_objects.rights.copyrightStatement</ifdef><ifdef code="ca_objects.rights.rightsNotes"><br/><b>Rights Notes:</b> ^ca_objects.rights.rightsNotes</ifdef></unit>')) {
					print "<div class='unit'><h6>Rights</H6>".$va_rights."</div>";  
				}
				if ($va_publication_notes = $t_object->get('ca_objects.publication_notes', array('delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Publication Notes</H6>".$va_publication_notes."</div>";
				}																																																				
?>
				<hr></hr>
					<div class="row">
						<div class="col-sm-6">	
							{{{<ifcount code="ca_collections" min="1" max="1"><H6>Related collection</H6></ifcount>}}}
							{{{<ifcount code="ca_collections" min="2"><H6>Related collections</H6></ifcount>}}}
							{{{<unit relativeTo="ca_collections" delimiter="<br/>"><l>^ca_collections.preferred_labels</l> (^relationship_typename)</unit>}}}
							
														
							{{{<ifcount code="ca_entities" min="1" max="1"><H6>Related person</H6></ifcount>}}}
							{{{<ifcount code="ca_entities" min="2"><H6>Related people</H6></ifcount>}}}
							{{{<unit relativeTo="ca_entities" delimiter="<br/>"><l>^ca_entities.preferred_labels.displayname</l> (^relationship_typename)</unit>}}}
							
							
							{{{<ifcount code="ca_places" min="1" max="1"><H6>Related place</H6></ifcount>}}}
							{{{<ifcount code="ca_places" min="2"><H6>Related places</H6></ifcount>}}}
							{{{<unit relativeTo="ca_places" delimiter="<br/>"><l>^ca_places.preferred_labels.name</l> (^relationship_typename)</unit>}}}
							
							{{{<ifcount code="ca_list_items" min="1" max="1"><H6>Related Term</H6></ifcount>}}}
							{{{<ifcount code="ca_list_items" min="2"><H6>Related Terms</H6></ifcount>}}}
							{{{<unit relativeTo="ca_list_items" delimiter="<br/>">^ca_list_items.preferred_labels.name_plural</unit>}}}
							
<?php
							if ($va_aat = $t_object->get('ca_objects.aat', array('delimiter' => '<br/>'))) {
								print "<div class='unit'><h6>Getty Art and Architecture Thesaurus</H6>".$va_aat."</div>";
							}
							if ($va_ulan = $t_object->get('ca_objects.ulan', array('delimiter' => '<br/>'))) {
								print "<div class='unit'><h6>Getty Union List of Artist Names</H6>".$va_ulan."</div>";
							}
							if ($va_tgn = $t_object->get('ca_objects.tgn', array('delimiter' => '<br/>'))) {
								print "<div class='unit'><h6>Getty Thesaurus of Geographic Names</H6>".$va_tgn."</div>"; 
							}
							if ($va_lcsh = $t_object->get('ca_objects.lcsh_terms', array('delimiter' => '<br/>'))) {
								print "<div class='unit'><h6>Library of Congress Subject Headings</H6>".$va_lcsh."</div>"; 
							}	
							if ($va_lc_names = $t_object->get('ca_objects.lc_names', array('delimiter' => '<br/>'))) {
								print "<div class='unit'><h6>Library of Congress Name Authority File</H6>".$va_lc_names."</div>"; 
							}
							if ($va_tgm = $t_object->get('ca_objects.tgm', array('delimiter' => '<br/>'))) {
								print "<div class='unit'><h6>Library of Congress Thesaurus of Graphic Materials</H6>".$va_tgm."</div>"; 
							}																											
?>  
						</div><!-- end col -->				
						<div class="col-sm-6 colBorderLeft">
							{{{map}}}
						</div>
					</div><!-- end row -->
			</div><!-- end col -->
		</div><!-- end row --></div><!-- end container -->
	</div><!-- end col -->
	<div class='navLeftRight col-xs-1 col-sm-1 col-md-1 col-lg-1'>
		<div class="detailNavBgRight">
			{{{nextLink}}}
		</div><!-- end detailNavBgLeft -->
	</div><!-- end col -->
</div><!-- end row -->
</div><!-- end container -->
<script type='text/javascript'>
	jQuery(document).ready(function() {
		$('.trimText').readmore({
		  speed: 75,
		  maxHeight: 120
		});
	});
</script>