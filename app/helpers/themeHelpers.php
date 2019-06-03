<?php
/** ---------------------------------------------------------------------
 * app/helpers/themeHelpers.php : utility functions for setting database-stored configuration values
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2019 Whirl-i-Gig
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
 * @package CollectiveAccess
 * @subpackage utils
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */

	/**
	*
	*/

   	# ---------------------------------------
	/**
	 * Generate HTML <img> tag for graphic in current theme; if graphic is not available the graphic in the default theme will be returned.
	 *
	 * @param string $ps_file_path
	 * @param array $pa_attributes
	 * @param array $pa_options
	 * @return string
	 */
	function caGetThemeGraphic($ps_file_path, $pa_attributes=null, $pa_options=null) {
		global $g_request;
		$vs_base_url_path = $g_request->getThemeUrlPath();
		$vs_base_path = $g_request->getThemeDirectoryPath();
		$vs_file_path = "/assets/pawtucket/graphics/{$ps_file_path}";

        if (file_exists($vs_base_path.$vs_file_path)) {
            // Graphic is present in currently configured theme
			return caHTMLImage($vs_base_url_path.$vs_file_path, $pa_attributes, $pa_options);
		}

        $o_config = Configuration::load();		
		if ($o_config->get('allowThemeInheritance')) {
            $i=0;
            
            while($vs_inherit_from_theme = trim(trim($o_config->get(['inheritFrom', 'inherit_from'])), "/")) {
                $i++;
                if (file_exists(__CA_THEMES_DIR__."/{$vs_inherit_from_theme}/{$vs_file_path}")) {
                    return caHTMLImage(__CA_THEMES_URL__."/{$vs_inherit_from_theme}/{$vs_file_path}", $pa_attributes, $pa_options);
                }
                
                if(!file_exists(__CA_THEMES_DIR__."/{$vs_inherit_from_theme}/conf/app.conf")) { break; }
                $o_config = Configuration::load(__CA_THEMES_DIR__."/{$vs_inherit_from_theme}/conf/app.conf", false, false, true);
                if ($i > 10) {break;} // max 10 levels
            }
        }

        // Fall back to default theme
		return caHTMLImage($g_request->getDefaultThemeUrlPath().$vs_file_path, $pa_attributes, $pa_options);
	}
	# ---------------------------------------
	/**
	 * Generate URL tag for graphic in current theme; if graphic is not available the graphic in the default theme will be returned.
	 *
	 * @param string $ps_file_path
	 * @param array $pa_options
	 * @return string
	 */
	function caGetThemeGraphicURL($ps_file_path, $pa_options=null) {
		global $g_request;
		$vs_base_path = $g_request->getThemeUrlPath();
		$vs_file_path = '/assets/pawtucket/graphics/'.$ps_file_path;

		if (!file_exists($g_request->getThemeDirectoryPath().$vs_file_path)) {
			$vs_base_path = $g_request->getDefaultThemeUrlPath();
		}
		return $vs_base_path.$vs_file_path;
	}
	# ---------------------------------------
	/**
	 * Set CSS classes to add the "pageArea" page content <div>, overwriting any previous setting.
	 * Use to set classes specific to each page type and context.
	 *
	 * @param mixed $pa_page_classes A class (string) or list of classes (array) to set
	 * @return bool Always returns true
	 */
	$g_theme_page_css_classes = array();
	function caSetPageCSSClasses($pa_page_classes) {
		global $g_theme_page_css_classes;
		if (!is_array($pa_page_classes) && $pa_page_classes) { $pa_page_classes = array($pa_page_classes); }
		if (!is_array($pa_page_classes)) { $pa_page_classes = array(); }

		$g_theme_page_css_classes = $pa_page_classes;

		return true;
	}
	# ---------------------------------------
	/**
	 * Adds CSS classes to the "pageArea" page content <div>. Use to set classes specific to each
	 * page type and context.
	 *
	 * @param mixed $pa_page_classes A class (string) or list of classes (array) to add
	 * @return bool Returns true if classes were added, false if class list is empty
	 */
	function caAddPageCSSClasses($pa_page_classes) {
		global $g_theme_page_css_classes;
		if (!is_array($pa_page_classes) && $pa_page_classes) { $pa_page_classes = array($pa_page_classes); }

		if(!is_array($va_classes = $g_theme_page_css_classes)) {
			return false;
		}

		$g_theme_page_css_classes = array_unique(array_merge($pa_page_classes, $va_classes));

		return true;
	}
	# ---------------------------------------
	/**
	 * Get CSS class attribute ready for including in a <div> tag. Used to add classes to the "pageArea" page content <div>
	 *
	 * @return string The "class" attribute with set classes or an empty string if no classes are set
	 */
	function caGetPageCSSClasses() {
		global $g_theme_page_css_classes;
		return (is_array($g_theme_page_css_classes) && sizeof($g_theme_page_css_classes)) ? "class='".join(' ', $g_theme_page_css_classes)."'" : '';
	}
	# ---------------------------------------
	/**
	 * Converts, and by default prints, a root-relative static view path to a DefaultController URL to load the appropriate view
	 * Eg. $ps_path of "/About/this/site" becomes "/index.php/About/this/site"
	 *
	 * @param string $ps_path
	 * @param array $pa_options Options include:
	 *		dontPrint = Don't print URL to output. Default is false.
	 *
	 * @return string the URL
	 */
	function caStaticPageUrl($ps_path, $pa_options=null) {
		global $g_request;

		$vs_url = $g_request->getBaseUrlPath().'/'.$g_request->getScriptName().$ps_path;

		if (!caGetOption('dontPrint', $pa_options, false)) {
			print $vs_url;
		}
		return $vs_url;
	}
	# ---------------------------------------
	/**
	 * Get theme-specific detail configuration
	 *
	 * @return Configuration
	 */
	function caGetDetailConfig() {
		return Configuration::load(__CA_THEME_DIR__.'/conf/detail.conf');
	}
	# ---------------------------------------
	/**
	 *
	 *
	 * @param string $ps_detail_type
	 * @return array
	 */
	function caGetDetailTypeConfig($ps_detail_type) {
		$o_config = Configuration::load(__CA_THEME_DIR__.'/conf/detail.conf');
		$va_config_values = $o_config->get('detailTypes');
		if (isset($va_config_values[$ps_detail_type])) {
			return $va_config_values[$ps_detail_type];
		}
		return null;
	}
	# ---------------------------------------
	/**
	 * Get theme-specific gallery section configuration
	 *
	 * @return Configuration
	 */
	function caGetGalleryConfig() {
		return Configuration::load(__CA_THEME_DIR__.'/conf/gallery.conf');
	}
	# ---------------------------------------
	/**
	 * Get theme-specific contact configuration
	 *
	 * @return Configuration
	 */
	function caGetContactConfig() {
		return Configuration::load(__CA_THEME_DIR__.'/conf/contact.conf');
	}
	# ---------------------------------------
	/**
	 * Get theme-specific front page configuration
	 *
	 * @return Configuration
	 */
	function caGetFrontConfig() {
		return Configuration::load(__CA_THEME_DIR__.'/conf/front.conf');
	}
	# ---------------------------------------
	/**
	 * Get theme-specific finding-aid section configuration
	 *
	 * @return Configuration
	 */
	function caGetCollectionsConfig() {
		return Configuration::load(__CA_THEME_DIR__.'/conf/collections.conf');
	}
	# ---------------------------------------
	/**
	 * Get theme-specific icon configuration
	 *
	 * @return Configuration
	 */
	function caGetIconsConfig() {
		if(file_exists(__CA_THEME_DIR__.'/conf/icons.conf')){
			return Configuration::load(__CA_THEME_DIR__.'/conf/icons.conf');
		}else{
			return Configuration::load(__CA_THEMES_DIR__.'/default/conf/icons.conf');
		}
	}
	# ---------------------------------------
	/**
	 * Get theme-specific sets/lightbox configuration
	 *
	 * @return Configuration
	 */
	function caGetLightboxConfig() {
		return Configuration::load(__CA_THEME_DIR__.'/conf/lightbox.conf');
	}
	# ---------------------------------------
	/**
	 * Returns associative array, keyed by primary key value with values being
	 * the preferred label of the row from a suitable locale, ready for display
	 *
	 * @param array $pa_ids indexed array of primary key values to fetch labels for
	 * @param array $pa_options
	 * @return array List of media
	 */
	function caGetPrimaryRepresentationsForIDs($pa_ids, $pa_options=null) {
		if (!is_array($pa_ids) && (is_numeric($pa_ids)) && ($pa_ids > 0)) { $pa_ids = array($pa_ids); }
		if (!is_array($pa_ids) || !sizeof($pa_ids)) { return array(); }

		$pa_access_values = caGetOption("checkAccess", $pa_options, array());
		$pa_versions = caGetOption("versions", $pa_options, array(), array('castTo' => 'array'));
		$ps_table = caGetOption("table", $pa_options, 'ca_objects');
		$pa_return = caGetOption("return", $pa_options, array(), array('castTo' => 'array'));

		$vs_access_where = '';
		if (isset($pa_access_values) && is_array($pa_access_values) && sizeof($pa_access_values)) {
			$vs_access_where = ' AND orep.access IN ('.join(',', $pa_access_values).')';
		}
		$o_db = new Db();
		if (!($vs_linking_table = RepresentableBaseModel::getRepresentationRelationshipTableName($ps_table))) { return null; }
		$vs_pk = Datamodel::primaryKey($ps_table);

		$qr_res = $o_db->query("
			SELECT oxor.{$vs_pk}, orep.media, orep.representation_id
			FROM ca_object_representations orep
			INNER JOIN {$vs_linking_table} AS oxor ON oxor.representation_id = orep.representation_id
			WHERE
				(oxor.{$vs_pk} IN (".join(',', $pa_ids).")) AND oxor.is_primary = 1 AND orep.deleted = 0 {$vs_access_where}
		");

		$vb_return_tags = (sizeof($pa_return) == 0) || in_array('tags', $pa_return);
		$vb_return_info = (sizeof($pa_return) == 0) || in_array('info', $pa_return);
		$vb_return_urls = (sizeof($pa_return) == 0) || in_array('urls', $pa_return);

		$va_media = array();
		while($qr_res->nextRow()) {
			$va_media_tags = array();
			if ($pa_versions && is_array($pa_versions) && sizeof($pa_versions)) { $va_versions = $pa_versions; } else { $va_versions = $qr_res->getMediaVersions('media'); }

			$vb_media_set = false;
			foreach($va_versions as $vs_version) {
				if (!$vb_media_set && $qr_res->getMediaPath('ca_object_representations.media', $vs_version)) { $vb_media_set = true; }

				if ($vb_return_tags) {
					if (sizeof($va_versions) == 1) {
						$va_media_tags['tags'] = $qr_res->getMediaTag('ca_object_representations.media', $vs_version);
					} else {
						$va_media_tags['tags'][$vs_version] = $qr_res->getMediaTag('ca_object_representations.media', $vs_version);
					}
				}
				if ($vb_return_info) {
					if (sizeof($va_versions) == 1) {
						$va_media_tags['info'] = $qr_res->getMediaInfo('ca_object_representations.media', $vs_version);
					} else {
						$va_media_tags['info'][$vs_version] = $qr_res->getMediaInfo('ca_object_representations.media', $vs_version);
					}
				}
				if ($vb_return_urls) {
					if (sizeof($va_versions) == 1) {
						$va_media_tags['urls'] = $qr_res->getMediaUrl('ca_object_representations.media', $vs_version);
					} else {
						$va_media_tags['urls'][$vs_version] = $qr_res->getMediaUrl('ca_object_representations.media', $vs_version);
					}
				}
			}

			$va_media_tags['representation_id'] = $qr_res->get('ca_object_representations.representation_id');

			if (!$vb_media_set)  { continue; }

			if (sizeof($pa_return) == 1) {
				$va_media_tags = $va_media_tags[$pa_return[0]];
			}
			$va_media[$qr_res->get($vs_pk)] = $va_media_tags;
		}

		// Return empty array when there's no media
		if(!sizeof($va_media)) { return array(); }

		// Preserve order of input ids
		$va_media_sorted = array();
		foreach($pa_ids as $vn_id) {
			if(!isset($va_media[$vn_id]) || !$va_media[$vn_id]) { continue; }
			$va_media_sorted[$vn_id] = $va_media[$vn_id];
		}

		return $va_media_sorted;
	}
	# ---------------------------------------
	/**
	 * Returns associative array, keyed by primary key value with values being
	 * the preferred label of the row from a suitable locale, ready for display
	 *
	 * @param array $pa_ids indexed array of primary key values to fetch labels for
	 * @param array $pa_options
	 * @return array List of media
	 */
	function caGetPrimaryRepresentationTagsForIDs($pa_ids, $pa_options=null) {
		$pa_options['return'] = array('tags');

		return caGetPrimaryRepresentationsForIDs($pa_ids, $pa_options);
	}
	# ---------------------------------------
	/**
	 * Returns associative array, keyed by primary key value with values being
	 * the preferred label of the row from a suitable locale, ready for display
	 *
	 * @param array $pa_ids indexed array of primary key values to fetch labels for
	 * @param array $pa_options
	 * @return array List of media
	 */
	function caGetPrimaryRepresentationInfoForIDs($pa_ids, $pa_options=null) {
		$pa_options['return'] = array('info');

		return caGetPrimaryRepresentationsForIDs($pa_ids, $pa_options);
	}
	# ---------------------------------------
	/**
	 * Returns associative array, keyed by primary key value with values being
	 * the preferred label of the row from a suitable locale, ready for display
	 *
	 * @param array $pa_ids indexed array of primary key values to fetch labels for
	 * @param array $pa_options
	 * @return array List of media
	 */
	function caGetPrimaryRepresentationUrlsForIDs($pa_ids, $pa_options=null) {
		$pa_options['return'] = array('urls');

		return caGetPrimaryRepresentationsForIDs($pa_ids, $pa_options);
	}
	# ---------------------------------------
	/*
	 *
	 * @param int $pn_representation_id
	 * @param ca_objects $pt_object
	 * @param array $pa_options Options include:
	 *		version = media version for thumbnail [Default = icon]
	 *		linkTo = viewer, detail, carousel. Carousel slides the media rep carousel on the default object detail page to the selected rep. [Default = carousel] 
	 *		returnAs = list, bsCols, array	[Default = list]
	 *		bsColClasses = pass the classes to assign to bs col [Default = col-sm-4 col-md-3 col-lg-3]
	 *		dontShowCurrentRep = true, false [Default = false]
	 *		currentRepClass = set to class name added to li and a tag for current rep [Default = active]
	 *      primaryOnly = Only show primary representations. [Default is false]
	 * @return string HTML output
	 */
	function caObjectRepresentationThumbnails($pn_representation_id, $pt_object, $pa_options){
		global $g_request;
		
		if(!$pt_object || !$pt_object->getPrimaryKey()){
			return false;
		}
		if(!is_array($pa_options)){
			$pa_options = array();
		}
		# --- set defaults
 		$pb_primary_only 					= caGetOption('primaryOnly', $pa_options, false);
		$ps_version                         = caGetOption('version', $pa_options, 'icon');
		$ps_link_to                         = caGetOption('linkTo', $pa_options, 'carousel');
		$ps_return_as                       = caGetOption('returnAs', $pa_options, 'list');
		$ps_bs_col_classes                  = caGetOption('bsColClasses', $pa_options, 'col-sm-4 col-md-3 col-lg-3');
		$ps_current_rep_class               = caGetOption('currentRepClass', $pa_options, 'active');
		
		if(!$pa_options["currentRepClass"]){ $pa_options["currentRepClass"] = "active"; }
		
		# --- get reps as thumbnails
		$va_reps = $pt_object->getRepresentations(array($ps_version), null, array("checkAccess" => caGetUserAccessValues(), 'primaryOnly' => $pb_primary_only));
		if(sizeof($va_reps) < 2){
			return;
		}
		$va_links = array();
		$vn_primary_id = "";
		foreach($va_reps as $vn_rep_id => $va_rep){
			$vs_class = "";
			if($va_rep["is_primary"]){
				$vn_primary_id = $vn_rep_id;
			}
			if($vn_rep_id == $pn_representation_id){
				if($pa_options["dontShowCurrentRep"]){
					continue;
				}
				$vs_class = $ps_current_rep_class;
			}
			$vs_thumb = $va_rep["tags"][$ps_version];
			switch($ps_link_to){
				# -------------------------------
				case "viewer":
					$va_links[$vn_rep_id] = "<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl('', 'Detail', 'GetMediaOverlay', array($pt_object->primaryKey() => $pt_object->getPrimaryKey(), 'representation_id' => $vn_rep_id, 'overlay' => 1, 'context' => $g_request->getAction()))."\"); return false;' ".(($vs_class) ? "class='".$vs_class."'" : "").">".$vs_thumb."</a>\n";
					break;
				# -------------------------------
				case "carousel":
					$va_links[$vn_rep_id] = "<a href='#' onclick='$(\".{$ps_current_rep_class}\").removeClass(\"{$ps_current_rep_class}\"); $(this).parent().addClass(\"{$ps_current_rep_class}\"); $(this).addClass(\"{$ps_current_rep_class}\"); $(\".jcarousel\").jcarousel(\"scroll\", $(\"#slide".$vn_rep_id."\"), false); return false;' ".(($vs_class) ? "class='".$vs_class."'" : "").">".$vs_thumb."</a>\n";
					break;
				# -------------------------------
				default:
				case "detail":
					$va_links[$vn_rep_id] = caDetailLink($vs_thumb, $vs_class, $pt_object->tableName(), $pt_object->getPrimaryKey(), array("representation_id" => $vn_rep_id));
					break;
				# -------------------------------
			}
		}
		
		# --- make sure the primary rep shows up first
		//$va_primary_link = array($vn_primary_id => $va_links[$vn_primary_id]);
		//unset($va_links[$vn_primary_id]);
		//$va_links = $va_primary_link + $va_links;
		
		# --- formatting
		$vs_formatted_thumbs = "";
		switch($ps_return_as){
			# ---------------------------------
			case "list":
				$vs_formatted_thumbs = "<ul id='detailRepresentationThumbnails'>";
				foreach($va_links as $vn_rep_id => $vs_link){
					if($vs_link){ $vs_formatted_thumbs .= "<li id='detailRepresentationThumbnail{$vn_rep_id}'".(($vn_rep_id == $pn_representation_id) ? " class='{$ps_current_rep_class}'" : "").">{$vs_link}</li>\n"; }
				}
				$vs_formatted_thumbs .= "</ul>";
				return $vs_formatted_thumbs;
				break;
			# ---------------------------------
			case "bsCols":
				$vs_formatted_thumbs = "<div class='container'><div class='row' id='detailRepresentationThumbnails'>";
				foreach($va_links as $vn_rep_id => $vs_link){
					if($vs_link){ $vs_formatted_thumbs .= "<div id='detailRepresentationThumbnail{$vn_rep_id}' class='{$ps_bs_col_classes}".(($vn_rep_id == $pn_representation_id) ? " {$ps_current_rep_class}" : "")."'>{$vs_link}</div>\n"; }
				}
				$vs_formatted_thumbs .= "</div></div>\n";
				return $vs_formatted_thumbs;
				break;
			# ---------------------------------
			case "array":
				return $va_links;
				break;
			# ---------------------------------
		}
	}
	# ---------------------------------------
	/*
	 * list of comments and
	 * comment form for all detail pages
	 *
	 */
	function caDetailItemComments($pn_item_id, $t_item, $va_comments, $va_tags){
		global $g_request;
		
		$vs_tmp = "";
		if(is_array($va_comments) && (sizeof($va_comments) > 0)){
			foreach($va_comments as $va_comment){
				$vs_tmp .= "<blockquote>";
				if($va_comment["media1"]){
					$vs_tmp .= '<div class="pull-right" id="commentMedia'.$va_comment["comment_id"].'">';
					$vs_tmp .= $va_comment["media1"]["tiny"]["TAG"];
					$vs_tmp .= "</div><!-- end pullright commentMedia -->\n";
					TooltipManager::add(
						"#commentMedia".$va_comment["comment_id"], $va_comment["media1"]["large_preview"]["TAG"]
					);
				}
				if($va_comment["comment"]){
					$vs_tmp .= $va_comment["comment"];
				}
				$vs_tmp .= "<small>".$va_comment["author"].", ".$va_comment["date"]."</small></blockquote>";
			}
		}
		if(is_array($va_tags) && sizeof($va_tags) > 0){
			$va_tag_links = array();
			foreach($va_tags as $vs_tag){
				$va_tag_links[] = caNavLink($g_request, $vs_tag, '', '', 'MultiSearch', 'Index', array('search' => $vs_tag));
			}
			$vs_tmp .= "<h2>"._t("Tags")."</h2>\n
				<div id='tags'>".implode($va_tag_links, ", ")."</div>";
		}
		if($g_request->isLoggedIn()){
			$vs_tmp .= "<button type='button' class='btn btn-default' onclick='caMediaPanel.showPanel(\"".caNavUrl('', 'Detail', 'CommentForm', array("tablename" => $t_item->tableName(), "item_id" => $t_item->getPrimaryKey()))."\"); return false;' >"._t("Add your tags and comment")."</button>";
		}else{
			$vs_tmp .= "<button type='button' class='btn btn-default' onclick='caMediaPanel.showPanel(\"".caNavUrl('', 'LoginReg', 'LoginForm', array())."\"); return false;' >"._t("Login/register to comment on this object")."</button>";
		}
		return $vs_tmp;
	}
	# ---------------------------------------
	/*
	 * Returns the info for each set
	 *
	 * options: "write_access" = false
	 *
	 */
	function caLightboxSetListItem($t_set, $va_check_access = array(), $pa_options = array()) {
		global $g_request;
		
		if(!($vn_set_id = $t_set->get("set_id"))) {
			return false;
		}
		$vb_write_access = false;
		if($pa_options["write_access"]){
			$vb_write_access = true;
		}
		$va_set_items = caExtractValuesByUserLocale($t_set->getItems(array("user_id" => $g_request->user->get("user_id"), "thumbnailVersions" => array("iconlarge", "icon"), "checkAccess" => $va_check_access, "limit" => 5)));
		
		$vs_set_display = "<div class='lbSetContainer' id='lbSetContainer{$vn_set_id}'><div class='lbSet ".(($vb_write_access) ? "" : "readSet" )."'><div class='lbSetContent'>\n";
		if(!$vb_write_access){
			$vs_set_display .= "<div class='pull-right caption'>Read Only</div>";
		}
		$vs_set_display .= "<H5>".caNavLink($g_request, $t_set->getLabelForDisplay(), "", "", "Lightbox", "setDetail", array("set_id" => $vn_set_id), array('id' => "lbSetName{$vn_set_id}"))."</H5>";

        $va_lightboxDisplayName = caGetLightboxDisplayName();
		$vs_lightbox_displayname = $va_lightboxDisplayName["singular"];
		$vs_lightbox_displayname_plural = $va_lightboxDisplayName["plural"];

        if(sizeof($va_set_items)){
			$vs_primary_image_block = $vs_secondary_image_block = "";
			$vn_i = 1;
			$t_list_items = new ca_list_items();
			foreach($va_set_items as $va_set_item){
				$t_list_items->load($va_set_item["type_id"]);
				$vs_placeholder = caGetPlaceholder($t_list_items->get("idno"), "placeholder_media_icon");
				if($vn_i == 1){
					# --- is the iconlarge version available?
					$vs_large_icon = "icon";
					if($va_set_item["representation_url_iconlarge"]){
						$vs_large_icon = "iconlarge";
					}
					if($va_set_item["representation_tag_".$vs_large_icon]){
						$vs_primary_image_block .= "<div class='col-sm-6'><div class='lbSetImg'>".caNavLink($g_request, $va_set_item["representation_tag_".$vs_large_icon], "", "", "Lightbox", "setDetail", array("set_id" => $vn_set_id))."</div><!-- end lbSetImg --></div>\n";
					}else{
						$vs_primary_image_block .= "<div class='col-sm-6'><div class='lbSetImg'>".caNavLink($g_request, "<div class='lbSetImgPlaceholder'>".$vs_placeholder."</div><!-- end lbSetImgPlaceholder -->", "", "", "Lightbox", "setDetail", array("set_id" => $vn_set_id))."</div><!-- end lbSetImg --></div>\n";
					}
				}else{
					if($va_set_item["representation_tag_icon"]){
						$vs_secondary_image_block .= "<div class='col-xs-3 col-sm-6 lbSetThumbCols'><div class='lbSetThumb'>".caNavLink($g_request, $va_set_item["representation_tag_icon"], "", "", "Lightbox", "setDetail", array("set_id" => $vn_set_id))."</div><!-- end lbSetThumb --></div>\n";
					}else{
						$vs_secondary_image_block .= "<div class='col-xs-3 col-sm-6 lbSetThumbCols'>".caNavLink($g_request, "<div class='lbSetThumbPlaceholder'>".caGetThemeGraphic('spacer.png').$vs_placeholder."</div><!-- end lbSetThumbPlaceholder -->", "", "", "Lightbox", "setDetail", array("set_id" => $vn_set_id))."</div>\n";
					}
				}
				$vn_i++;
			}
			while($vn_i < 6){
				$vs_secondary_image_block .= "<div class='col-xs-3 col-sm-6 lbSetThumbCols'>".caNavLink($g_request, "<div class='lbSetThumbPlaceholder'>".caGetThemeGraphic('spacer.png')."</div><!-- end lbSetThumbPlaceholder -->", "", "", "Lightbox", "setDetail", array("set_id" => $vn_set_id))."</div>";
				$vn_i++;
			}
		}else{
			$vs_primary_image_block .= "<div class='col-sm-6'><div class='lbSetImg'><div class='lbSetImgPlaceholder'>"._t("this %1 contains no items", $vs_lightbox_displayname)."</div><!-- end lbSetImgPlaceholder --></div><!-- end lbSetImg --></div>\n";
			$i = 1;
			while($vn_i < 4){
				$vs_secondary_image_block .= "<div class='col-xs-3 col-sm-6 lbSetThumbCols'><div class='lbSetThumbPlaceholder'>".caGetThemeGraphic('spacer.png')."</div><!-- end lbSetThumbPlaceholder --></div>";
				$vn_i++;
			}
		}
		$vs_set_display .= "<div class='row'>".$vs_primary_image_block."<div class='col-sm-6'><div id='comment{$vn_set_id}' class='lbSetComment'><!-- load comments here --></div>\n<div class='lbSetThumbRowContainer'><div class='row lbSetThumbRow' id='lbSetThumbRow{$vn_set_id}'>".$vs_secondary_image_block."</div><!-- end row --></div><!-- end lbSetThumbRowContainer --></div><!-- end col --></div><!-- end row -->";
		$vs_set_display .= "</div><!-- end lbSetContent -->\n";
		$vs_set_display .= "<div class='lbSetExpandedInfo' id='lbExpandedInfo{$vn_set_id}'>\n<hr><div>created by: ".trim($t_set->get("ca_users.fname")." ".$t_set->get("ca_users.lname"))."</div>\n";
		$vs_set_display .= "<div>"._t("Items: %1", $t_set->getItemCount(array("user_id" => $g_request->user->get("user_id"), "checkAccess" => $va_check_access)))."</div>\n";
		if($vb_write_access){
			$vs_set_display .= "<div class='pull-right'><a href='#' data-set_id=\"".(int)$t_set->get('set_id')."\" data-set_name=\"".addslashes($t_set->get('ca_sets.preferred_labels.name'))."\" data-toggle='modal' data-target='#confirm-delete'><span class='glyphicon glyphicon-trash'></span></a></div>\n";
		}
		$vs_set_display .= "<div><a href='#' onclick='jQuery(\"#comment{$vn_set_id}\").load(\"".caNavUrl('', 'Lightbox', 'AjaxListComments', array('type' => 'ca_sets', 'set_id' => $vn_set_id))."\", function(){jQuery(\"#lbSetThumbRow{$vn_set_id}\").hide(); jQuery(\"#comment{$vn_set_id}\").show();}); return false;' title='"._t("Comments")."'><span class='glyphicon glyphicon-comment'></span> <small>".$t_set->getNumComments()."</small></a>";
		if($vb_write_access){
			$vs_set_display .= "&nbsp;&nbsp;&nbsp;<a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl('', 'Lightbox', 'setForm', array("set_id" => $vn_set_id))."\"); return false;' title='"._t("Edit Name/Description")."'><span class='glyphicon glyphicon-edit'></span></a>";
		}
		$vs_set_display .= "</div>\n";
		$vs_set_display .= "</div><!-- end lbSetExpandedInfo --></div><!-- end lbSet --></div><!-- end lbSetContainer -->\n";

        return $vs_set_display;
	}
	# ---------------------------------------
	/**
	 *
	 *
	 */
	$g_theme_detail_for_type_cache = array();
	function caGetDetailForType($pm_table, $pm_type=null, $pa_options=null) {
		global $g_request, $g_theme_detail_for_type_cache;
		
		$vs_current_action = $g_request->getAction();
		if (isset($g_theme_detail_for_type_cache[$pm_table.'/'.$pm_type])) { return $g_theme_detail_for_type_cache[$pm_table.'/'.$pm_type.'/'.$vs_current_action]; }
		$o_config = caGetDetailConfig();
		$vs_preferred_detail = caGetOption('preferredDetail', $pa_options, null);

		if (!($vs_table = Datamodel::getTableName($pm_table))) { return null; }

		if ($pm_type) {
			$t_instance = Datamodel::getInstanceByTableName($vs_table, true);
			$vs_type = is_numeric($pm_type) ? $t_instance->getTypeCode($pm_type) : $pm_type;
		} else {
			$vs_type = null;
		}

		$va_detail_types = $o_config->getAssoc('detailTypes');

		$vs_detail_type = null;
		foreach($va_detail_types as $vs_code => $va_info) {
			if ($va_info['table'] == $vs_table) {
				$va_detail_aliases = caGetOption('aliases', $va_info, array(), array('castTo' => 'array'));

				if (is_null($pm_type) || !is_array($va_info['restrictToTypes']) || (sizeof($va_info['restrictToTypes']) == 0) || in_array($vs_type, $va_info['restrictToTypes'])) {
					// If the code matches the current url action use that in preference to anything else

					// does it have an alias?
					if ($vs_preferred_detail && ($vs_code == $vs_preferred_detail)) { return $vs_preferred_detail; }
					if ($vs_preferred_detail && in_array($vs_preferred_detail, $va_detail_aliases)) { return $vs_preferred_detail; }
					if ($vs_current_action && ($vs_code == $vs_current_action)) { return $vs_code; }
					if ($vs_current_action && in_array($vs_current_action, $va_detail_aliases)) { return $vs_current_action; }

					$vs_detail_type = $g_theme_detail_for_type_cache[$pm_table.'/'.$pm_type.'/'.$vs_current_action] = $g_theme_detail_for_type_cache[$vs_table.'/'.$vs_type.'/'.$vs_current_action] = $vs_code;
				}
			}
		}

		if (!$vs_detail_type) $g_theme_detail_for_type_cache[$pm_table.'/'.$pm_type] = $g_theme_detail_for_type_cache[$vs_table.'/'.$vs_type.'/'.$vs_current_action] = null;
		return $vs_detail_type;
	}
	# ---------------------------------------
	/**
	 *
	 *
	 */
	function caGetDisplayImagesForAuthorityItems($pm_table, $pa_ids, $pa_options=null) {
		if (!($t_instance = Datamodel::getInstanceByTableName($pm_table, true))) { return null; }
		if (method_exists($t_instance, "isRelationship") && $t_instance->isRelationship()) { return array(); }
		
		$ps_return = caGetOption("return", $pa_options, 'tags');
		
		if ((!caGetOption("useRelatedObjectRepresentations", $pa_options, array())) && method_exists($t_instance, "getPrimaryMediaForIDs")) {
			// Use directly related media if defined
			$va_media = $t_instance->getPrimaryMediaForIDs($pa_ids, array($vs_version = caGetOption('version', $pa_options, 'icon')), $pa_options);
			$va_media_by_id = array();
			foreach($va_media as $vn_id => $va_media_info) {
				if(!is_array($va_media_info)) { continue; }
				$va_media_by_id[$vn_id] = $va_media_info['tags'][$vs_version];
			}
			if(sizeof($va_media_by_id)){
				return $va_media_by_id;
			}
		}

		if(!is_array($pa_options)){
			$pa_options = array();
		}
		$pa_access_values = caGetOption("checkAccess", $pa_options, array());
		$vs_access_wheres = '';
		if($pa_options['checkAccess']){
			$vs_access_wheres = " AND ca_objects.access IN (".join(",", $pa_access_values).") AND ca_object_representations.access IN (".join(",", $pa_access_values).")";
		}
		$va_path = array_keys(Datamodel::getPath($vs_table = $t_instance->tableName(), "ca_objects"));
		$vs_pk = $t_instance->primaryKey();

		$va_params = array();

		$vs_linking_table = $va_path[1];


		$vs_rel_type_where = '';
		if (is_array($va_rel_types = caGetOption('relationshipTypes', $pa_options, null)) && sizeof($va_rel_types)) {
			$va_rel_types = caMakeRelationshipTypeIDList($vs_linking_table, $va_rel_types);
			if (is_array($va_rel_types) && sizeof($va_rel_types)) {
				$vs_rel_type_where = " AND ({$vs_linking_table}.type_id IN (?))";
				$va_params[] = $va_rel_types;
			}
		}
		
		$vs_type_where = '';
		if (is_array($va_object_types = caGetOption('objectTypes', $pa_options, null)) && sizeof($va_object_types)) {
			$va_object_types = caMakeTypeIDList('ca_objects', $va_object_types);
			if (is_array($va_object_types) && sizeof($va_object_types)) {
				$vs_type_where = " AND (ca_objects.type_id IN (?))";
				$va_params[] = $va_object_types;
			}
		}

		if(is_array($pa_ids) && sizeof($pa_ids)) {
			$vs_id_sql = "AND {$vs_table}.{$vs_pk} IN (?)";
			$va_params[] = $pa_ids;
		}

		$vs_sql = "SELECT DISTINCT ca_object_representations.media, {$vs_table}.{$vs_pk}
			FROM {$vs_table}
			INNER JOIN {$vs_linking_table} ON {$vs_linking_table}.{$vs_pk} = {$vs_table}.{$vs_pk}
			INNER JOIN ca_objects ON ca_objects.object_id = {$vs_linking_table}.object_id
			INNER JOIN ca_objects_x_object_representations ON ca_objects_x_object_representations.object_id = ca_objects.object_id
			INNER JOIN ca_object_representations ON ca_object_representations.representation_id = ca_objects_x_object_representations.representation_id
			WHERE
				ca_objects_x_object_representations.is_primary = 1 {$vs_access_wheres} {$vs_rel_type_where} {$vs_type_where} {$vs_id_sql}
		";

		$o_db = $t_instance->getDb();

		$qr_res = $o_db->query($vs_sql, $va_params);
		$va_res = array();
		while($qr_res->nextRow()) {
		    switch($ps_return) {
		        case 'urls':
			        $va_res[$qr_res->get($vs_pk)] = $qr_res->getMediaUrl("media", caGetOption('version', $pa_options, 'icon'));
			        break;
		        case 'paths':
			        $va_res[$qr_res->get($vs_pk)] = $qr_res->getMediaPath("media", caGetOption('version', $pa_options, 'icon'));
			        break;
		        case 'tags':
		        default:
			        $va_res[$qr_res->get($vs_pk)] = $qr_res->getMediaTag("media", caGetOption('version', $pa_options, 'icon'));
			        break;
			}
		}
		return $va_res;
	}
	# ---------------------------------------
	/**
	 * class -> class name of <ul>
	 * options -> limit -> limit number of sets returned, role -> role in <ul> tag
	 */
	function caGetGallerySetsAsList($vs_class, $pa_options=null){
		global $g_request;
		
		$o_config = caGetGalleryConfig();
		$va_access_values = caGetUserAccessValues();
 		$t_list = new ca_lists();
 		$vn_gallery_set_type_id = $t_list->getItemIDFromList('set_types', $o_config->get('gallery_set_type'));
 		$vs_set_list = "";
		if($vn_gallery_set_type_id){
			$t_set = new ca_sets();
			$va_sets = caExtractValuesByUserLocale($t_set->getSets(array('table' => 'ca_objects', 'checkAccess' => $va_access_values, 'setType' => $vn_gallery_set_type_id)));

			$vn_limit = caGetOption('limit', $pa_options, 100);
			$vs_role = caGetOption('role', $pa_options, null);
			if(sizeof($va_sets)){
				$vs_set_list = "<ul".(($vs_class) ? " class='".$vs_class."'" : "").(($vs_role) ? " role='".$vs_role."'" : "").">\n";

				$vn_c = 0;
				foreach($va_sets as $vn_set_id => $va_set){
					$vs_set_list .= "<li>".caNavLink($g_request, $va_set["name"], "", "", "Gallery", $vn_set_id)."</li>\n";
					$vn_c++;

					if ($vn_c >= $vn_limit) { break; }
				}
				$vs_set_list .= "</ul>\n";
			}
		}
		return $vs_set_list;
	}
	# ---------------------------------------
	/**
	 *
	 */
	function caGetPlaceholder($vs_type_code, $vs_placeholder_type = "placeholder_media_icon"){
		$o_config = caGetIconsConfig();
		$va_placeholders_by_type = $o_config->getAssoc("placeholders");
		$vs_placeholder = $o_config->get($vs_placeholder_type);
		if(is_array($va_placeholders_by_type[$vs_type_code])){
			$vs_placeholder = $va_placeholders_by_type[$vs_type_code][$vs_placeholder_type];
		}
		if(!$vs_placeholder){
			if($vs_placeholder_type == "placeholder_media_icon"){
				$vs_placeholder = "<i class='fa fa-picture-o fa-2x'></i>";
			}else{
				$vs_placeholder = "<i class='fa fa-picture-o fa-5x'></i>";
			}
		}
		return $vs_placeholder;
	}
	# ---------------------------------------
	/**
	 *
	 */
	function caGetLightboxDisplayName($o_lightbox_config = null){
		if(!$o_lightbox_config){ $o_lightbox_config = caGetLightboxConfig(); }
		$vs_lightbox_displayname = $o_lightbox_config->get("lightboxDisplayName");
		if(!$vs_lightbox_displayname){
			$vs_lightbox_displayname = _t("lightbox");
		}
		$vs_lightbox_displayname_plural = $o_lightbox_config->get("lightboxDisplayNamePlural");
		if(!$vs_lightbox_displayname_plural){
			$vs_lightbox_displayname_plural = _t("lightboxes");
		}
		$vs_lightbox_section_heading = $o_lightbox_config->get("lightboxSectionHeading");
		if(!$vs_lightbox_section_heading){
			$vs_lightbox_section_heading = _t("lightboxes");
		}
		return array("singular" => $vs_lightbox_displayname, "plural" => $vs_lightbox_displayname_plural, "section_heading" => $vs_lightbox_section_heading);
	}
	# ---------------------------------------
	function caDisplayLightbox(){
		global $g_request;
		
		if($g_request->isLoggedIn() && !$g_request->config->get("disable_lightbox")){
			return true;
		}else{
			return false;
		}
	}
	# ---------------------------------------
	function caGetAddToSetInfo(){
		global $g_request;
		
		$va_link_info = array();
		if(!$g_request->isLoggedIn() && !$g_request->config->get("disable_lightbox")){
			$o_lightbox_config = caGetLightboxConfig();
			$va_link_info["controller"] = "Lightbox";
			$va_link_info["icon"] = $o_lightbox_config->get("addToLightboxIcon");
			if(!$va_link_info["icon"]){
				$va_link_info["icon"] = "<i class='fa fa-suitcase'></i>";
			}
			$va_lightboxDisplayName = caGetLightboxDisplayName($o_lightbox_config);
			$va_link_info["name_singular"] = $va_lightboxDisplayName["singular"];
			$va_link_info["name_plural"] = $va_lightboxDisplayName["plural"];
			$va_link_info["section_heading"] = $va_lightboxDisplayName["section_heading"];
			
			$va_link_info["link_text"] = _t("Login to add to %1", $va_link_info["name_singular"]);
			return $va_link_info;
		}
		if(caDisplayLightbox($g_request)){
			$o_lightbox_config = caGetLightboxConfig();
			$va_link_info["controller"] = "Lightbox";
			$va_link_info["icon"] = $o_lightbox_config->get("addToLightboxIcon");
			if(!$va_link_info["icon"]){
				$va_link_info["icon"] = "<i class='fa fa-suitcase'></i>";
			}
			$va_lightboxDisplayName = caGetLightboxDisplayName($o_lightbox_config);
			$va_link_info["name_singular"] = $va_lightboxDisplayName["singular"];
			$va_link_info["name_plural"] = $va_lightboxDisplayName["plural"];
			$va_link_info["section_heading"] = $va_lightboxDisplayName["section_heading"];
			$va_link_info["link_text"] = _t("Add to %1", $va_link_info["name_singular"]);
			return $va_link_info;
		}
		return false;
	}
	
	# ---------------------------------------
	/**
	 *
	 */
	function caSetAdvancedSearchFormInView($po_view, $ps_function, $ps_view, $pa_options=null) {
		global $g_request;
		
		require_once(__CA_MODELS_DIR__."/ca_metadata_elements.php");
		
		if (!($va_search_info = caGetInfoForAdvancedSearchType($ps_function))) { return null; }
		
 		if (!($pt_subject = Datamodel::getInstanceByTableName($va_search_info['table'], true))) { return null; }
 		
 		$va_globals = $pt_subject->getAppConfig()->getAssoc('global_template_values');
 		
		$ps_controller = caGetOption('controller', $pa_options, null);
		$ps_form_name = caGetOption('formName', $pa_options, 'caAdvancedSearch');
		
		$vs_script = null;
		
		$pa_tags = $po_view->getTagList($ps_view);
		if (!is_array($pa_tags) || !sizeof($pa_tags)) { return null; }
		
		$va_form_elements = array();
		
		$vb_submit_or_reset_set = false;
		foreach($pa_tags as $vs_tag) {
		    if(isset($va_globals[$vs_tag])) { continue; }
		    
			$va_parse = caParseTagOptions($vs_tag);
			$vs_tag_proc = $va_parse['tag'];
			$va_opts = $va_parse['options'];
			$va_opts['checkAccess'] = $g_request ? caGetUserAccessValues() : null;
			
			if (($vs_default_value = caGetOption('default', $va_opts, null)) || ($vs_default_value = caGetOption($vs_tag_proc, $va_default_form_values, null))) { 
				$va_default_form_values[$vs_tag_proc] = $vs_default_value;
				unset($va_opts['default']);
			} 
		
			$vs_tag_val = null;
			switch(strtolower($vs_tag_proc)) {
				case 'submit':
					$po_view->setVar($vs_tag, "<a href='#' class='caAdvancedSearchFormSubmit'>".((isset($va_opts['label']) && $va_opts['label']) ? $va_opts['label'] : _t('Submit'))."</a>");
					$vb_submit_or_reset_set = true;
					break;
				case 'submittag':
					$po_view->setVar($vs_tag, "<a href='#' class='caAdvancedSearchFormSubmit'>");
					$vb_submit_or_reset_set = true;
					break;
				case 'reset':
					$po_view->setVar($vs_tag, "<a href='#' class='caAdvancedSearchFormReset'>".((isset($va_opts['label']) && $va_opts['label']) ? $va_opts['label'] : _t('Reset'))."</a>");
					$vb_submit_or_reset_set = true;
					break;
				case 'resettag':
					$po_view->setVar($vs_tag, "<a href='#' class='caAdvancedSearchFormReset'>");
					$vb_submit_or_reset_set = true;
					break;
				case '/resettag':
				case '/submittag':
					$po_view->setVar($vs_tag, "</a>");
					break;
				default:
					if (preg_match("!^(.*):label$!", $vs_tag_proc, $va_matches)) {
						$po_view->setVar($vs_tag, $vs_tag_val = $t_subject->getDisplayLabel($va_matches[1]));
					} elseif (preg_match("!^(.*):boolean$!", $vs_tag_proc, $va_matches)) {
						$po_view->setVar($vs_tag, caHTMLSelect($vs_tag_proc.'[]', array(_t('AND') => 'AND', _t('OR') => 'OR', 'AND NOT' => 'AND NOT'), array('class' => 'caAdvancedSearchBoolean')));
					} elseif (preg_match("!^(.*):relationshipTypes$!", $vs_tag_proc, $va_matches)) {
						$va_tmp = explode(".", $va_matches[1]);
						
						$vs_select = '';
						if ($t_rel = $pt_subject->getRelationshipInstance($va_tmp[0])) {
							$vs_select = $t_rel->getRelationshipTypesAsHTMLSelect($va_tmp[0], null, null, array_merge(array('class' => 'caAdvancedSearchRelationshipTypes'), $va_opts, array('name' => $vs_tag_proc.'[]')), $va_opts);
						}
						$po_view->setVar($vs_tag, $vs_select);
					} else {
						$va_opts['asArrayElement'] = true;
						if (isset($va_opts['restrictToTypes']) && $va_opts['restrictToTypes'] && !is_array($va_opts['restrictToTypes'])) { 
							$va_opts['restrictToTypes'] = preg_split("![,;]+!", $va_opts['restrictToTypes']);
						}
						
						// Relationship type restrictions
						if (isset($va_opts['restrictToRelationshipTypes']) && $va_opts['restrictToRelationshipTypes'] && !is_array($va_opts['restrictToRelationshipTypes'])) { 
							$va_opts['restrictToRelationshipTypes'] = preg_split("![,;]+!", $va_opts['restrictToRelationshipTypes']);
						}
						
						// Exclude values
						if (isset($va_opts['exclude']) && $va_opts['exclude'] && !is_array($va_opts['exclude'])) { 
							$va_opts['exclude'] = preg_split("![,;]+!", $va_opts['exclude']);
						}
						
						if ($vs_rel_types = join(";", caGetOption('restrictToRelationshipTypes', $va_opts, array()))) { $vs_rel_types = "/{$vs_rel_types}"; }
			
						if ($vs_tag_val = $pt_subject->htmlFormElementForSearch($g_request, $vs_tag_proc, $va_opts)) {
							switch(strtolower($vs_tag_proc)) {
								case '_fulltext':		// Set default label for _fulltext if needed
									if(!isset($va_opts['label'])) { $va_opts['label'] = _t('Keywords'); }
									break;
							}
							$vs_tag_val .= caHTMLHiddenInput("{$vs_tag_proc}{$vs_rel_types}_label", array('value' => isset($va_opts['label']) ? str_replace("_", " ", urldecode($va_opts['label'])) : $pt_subject->getDisplayLabel($vs_tag_proc)));	// set display labels for search criteria
							$po_view->setVar($vs_tag, $vs_tag_val);
						}
						
						$va_tmp = explode('.', $vs_tag_proc);
						if((($t_element = ca_metadata_elements::getInstance($va_tmp[1])) && ($t_element->get('datatype') == 0))) {
							if (is_array($va_elements = $t_element->getElementsInSet())) {
								foreach($va_elements as $va_element) {
									if ($va_element['datatype'] > 0) {
										$va_form_elements[] = $va_tmp[0].'.'.$va_tmp[1].'.'.$va_element['element_code'].$vs_rel_types;	// add relationship types to field name
									}
								}
							}
							break;
						}
					}
					if ($vs_tag_val) { $va_form_elements[] = $vs_tag_proc.$vs_rel_types; }		// add relationship types to field name
					break;
			}
		}
		
		if($vb_submit_or_reset_set) {
			$vs_script = "<script type='text/javascript'>
			jQuery('.caAdvancedSearchFormSubmit').on('click', function() {
				jQuery('#caAdvancedSearch').submit();
				return false;
			});
			jQuery('.caAdvancedSearchFormReset').on('click', function() {
				jQuery('#caAdvancedSearch').find('input[type!=\"hidden\"],textarea').val('');
				jQuery('#caAdvancedSearch').find('input.lookupBg').val('');
				jQuery('#caAdvancedSearch').find('select.caAdvancedSearchBoolean').val('AND');
				jQuery('#caAdvancedSearch').find('select').prop('selectedIndex', 0);
				return false;
			});
			jQuery(document).ready(function() {
				var f, defaultValues = ".json_encode($va_default_form_values).", defaultBooleans = ".json_encode($va_default_form_booleans).";
				for (f in defaultValues) {
					var f_proc = f + '[]';
					jQuery('input[name=\"' + f_proc+ '\"], textarea[name=\"' + f_proc+ '\"], select[name=\"' + f_proc+ '\"]').each(function(k, v) {
						if (defaultValues[f][k]) { jQuery(v).val(defaultValues[f][k]); } 
					});
				}
				for (f in defaultBooleans) {
					var f_proc = f + '[]';
					jQuery('select[name=\"' + f_proc+ '\"].caAdvancedSearchBoolean').each(function(k, v) {
						if (defaultBooleans[f][k]) { jQuery(v).val(defaultBooleans[f][k]); }
					});
				}
			});
			</script>\n";
		}
		
		$po_view->setVar("form", caFormTag("{$ps_function}", $ps_form_name, $ps_controller, 'post', 'multipart/form-data', '_top', array('noCSRFToken' => true, 'disableUnsavedChangesWarning' => true, 'submitOnReturn' => true)));
 		$po_view->setVar("/form", $vs_script.caHTMLHiddenInput("_advancedFormName", array("value" => $ps_function)).caHTMLHiddenInput("_formElements", array("value" => join('|', $va_form_elements))).caHTMLHiddenInput("_advanced", array("value" => 1))."</form>");
 			
		return $va_form_elements;
	}
	# ---------------------------------------
	/**
	 *
	 */
	function caGetAdvancedSearchFormAutocompleteJS($ps_field, $pt_instance, $pa_options=null) {
		$vs_field_proc = preg_replace("![\.]+!", "_", $ps_field);
		if ($vs_rel_types = join("_", caGetOption(['restrictToRelationshipTypes', 'relationshipType'], $pa_options, []))) { $vs_rel_types_proc = "_{$vs_rel_types}"; $vs_rel_types = "/{$vs_rel_types}";  }

		
		if (!is_array($pa_options)) { $pa_options = array(); }
        if (!isset($pa_options['width'])) { $pa_options['width'] = 30; }
        if (!isset($pa_options['values'])) { $pa_options['values'] = array(); }
        if (!isset($pa_options['values'][$ps_field])) { $pa_options['values'][$ps_field] = ''; }
        $index = caGetOption('index', $pa_options, null);
        
        $array_suffix = caGetOption('asArrayElement', $pa_options, false) ? "[]" : "";
        
        $vs_buf = caHTMLTextInput("{$vs_field_proc}_autocomplete{$index}", array('value' => (isset($pa_options['value']) ? $pa_options['value'] : $pa_options['values'][$ps_field]), 'size' => $pa_options['width'], 'class' => $pa_options['class'], 'id' => "{$vs_field_proc}_autocomplete{$index}"));
		
		$vs_buf .= "<input type=\"hidden\" name=\"{$ps_field}{$array_suffix}\" id=\"{$vs_field_proc}{$index}\" value=\"".(isset($pa_options['id_value']) ? (int)$pa_options['id_value'] : '')."\" class=\"lookupBg\"/>";
									
		if (!is_array($va_json_lookup_info = caJSONLookupServiceUrl($pt_instance->tableName()))) { return null; }
		$vs_buf .= "<script type=\"text/javascript\">
    jQuery(document).ready(function() {
        jQuery('#{$vs_field_proc}_autocomplete{$index}').autocomplete({ minLength: 3, delay: 800, html: true,
                source: function( request, response ) {
                    $.ajax({
                        url: '{$va_json_lookup_info['search']}',
                        dataType: \"json\",
                        data: { term: '{$ps_field}:' + request.term },
                        success: function( data ) {
                            response(data);
                        }
                    });
                },
                response: function ( event, ui ) {
                    if (ui && ui.content && ui.content.length == 1 && (ui.content[0].id == -1)) {
                        jQuery('#{$vs_field_proc}{$index}').val(jQuery('#{$vs_field_proc}_autocomplete{$index}').val());
                    }
                },
                select: function( event, ui ) {
                    if(!parseInt(ui.item.id) || (ui.item.id <= 0)) {
                        jQuery('#{$vs_field_proc}_autocomplete{$index}').val('');  // no matches so clear text input
                        jQuery('#{$vs_field_proc}{$index}').val('xx');
                        event.preventDefault();
                        return;
                    }
                    jQuery('#{$vs_field_proc}_autocomplete{$index}').val(jQuery.trim(ui.item.label.replace(/<\/?[^>]+>/gi, '')));
                    jQuery('#{$vs_field_proc}{$index}').val(ui.item.id);
                    event.preventDefault();
                }
        }).autocomplete('instance')._renderItem = function(ul, item) {
                return $('<li>').append(item.label).appendTo(ul);
        };
    });								
</script>";

		return $vs_buf;
	}
	# ---------------------------------------
	/**
	 *
	 */
	function caCheckLightboxView($pa_options = null){
		global $g_request;
		
		if (!($ps_view = caGetOption('view', $pa_options, null)) && $g_request) { $ps_view = $g_request->getParameter('view', pString); }
		if(!in_array($ps_view, array('thumbnail', 'map', 'timeline', 'timelineData', 'pdf', 'list', 'xlsx', 'pptx'))) {
			$ps_view = caGetOption('default', $pa_options, 'thumbnail');
		}
		return $ps_view;
	}
	# ---------------------------------------
	/**
	 * Generate link to change current locale.
	 *
	 * @param string $ps_locale ISO locale code (Ex. en_US) to change to.
	 * @param string $ps_classname CSS class name(s) to include in <a> tag.
	 * @param array $pa_attributes Optional attributes to include in <a> tag. [Default is null]
	 * @param array $pa_options Options to be passed to caNavLink(). [Default is null]
	 * @return string 
	 *
	 * @seealso caNavLink()
	 */
	function caChangeLocaleLink($ps_locale, $ps_content, $ps_classname, $pa_attributes=null, $pa_options=null) {
		global $g_request;
		
		$va_params = $g_request->getParameters(['GET', 'REQUEST', 'PATH']);
		$va_params['lang'] = $ps_locale;
		return caNavLink($ps_content, $ps_classname, '*', '*', '*', $va_params, $pa_attributes, $pa_options);
	}
	# ---------------------------------------
	/** 
	 * Returns number of global values defined in the current theme
	 *
	 * @return int
	 */
	function caGetGlobalValuesCount() {
		$o_config = Configuration::load();
		$va_template_values = $o_config->getAssoc('global_template_values');
		return is_array($va_template_values) ? sizeof($va_template_values) : 0;
	}
	# ---------------------------------------
	/**
	 * 
	 *
	 * 
	 */
	function caGetComparisonList($ps_table, $pa_options=null) {
		global $g_request;
		
		if (!is_array($va_comparison_list = Session::getVar("{$ps_table}_comparison_list"))) { $va_comparison_list = []; }
		
		// Get title template from config
		$va_compare_config = $g_request->config->get('compare_images');
		if (!is_array($va_compare_config = $va_compare_config[$ps_table])) { $va_compare_config = []; }
		$va_display_list = caProcessTemplateForIDs(caGetOption('title_template', $va_compare_config, "^{$ps_table}.preferred_labels"), $ps_table, $va_comparison_list, ['returnAsArray' => true]);
		
		$va_list = [];
		foreach($va_comparison_list as $vn_i => $vn_id) {
			$va_list[$vn_id] = $va_display_list[$vn_i];
		}
		
		return $va_list;
	}
	# ---------------------------------------
	/**
	 * Used to export collection hierarchy as PDF finding aid
	 * recursive loop to display all collection children and objects
	 * 
	 */	
	function caGetCollectionLevelSummary($va_collection_ids, $vn_level) {
		global $g_request;
		
		$va_access_values = caGetUserAccessValues();
		# --- get collections configuration
		$o_collections_config = caGetCollectionsConfig();
		if($o_collections_config->get("export_max_levels") && ($vn_level > $o_collections_config->get("export_max_levels"))){
			return;
		}
		$t_list = new ca_lists();
		$va_exclude_collection_type_ids = array();
		if($va_exclude_collection_type_idnos = $o_collections_config->get("export_exclude_collection_types")){
			# --- convert to type_ids
			$va_exclude_collection_type_ids = $t_list->getItemIDsFromList("collection_types", $va_exclude_collection_type_idnos, array("dontIncludeSubItems" => true));
		}
		$vs_output = "";
		$qr_collections = caMakeSearchResult("ca_collections", $va_collection_ids);
		
		$vs_sub_collection_label_template = $o_collections_config->get("export_sub_collection_label_template");
		$vs_sub_collection_desc_template = $o_collections_config->get("export_sub_collection_description_template");
		$vs_sub_collection_sort = $o_collections_config->get("export_sub_collection_sort");
		if(!$vs_sub_collection_sort){
			$vs_sub_collection_sort = "ca_collections.idno_sort";
		}
		$vb_dont_show_top_level_description = false;
		if($o_collections_config->get("dont_show_top_level_description") && ($vn_level == 1)){
			$vb_dont_show_top_level_description = true;
		}
		$vs_object_template = $o_collections_config->get("export_object_label_template");
		$va_collection_type_icons = array();
		$va_collection_type_icons_by_idnos = $o_collections_config->get("export_collection_type_icons");
		if(is_array($va_collection_type_icons_by_idnos) && sizeof($va_collection_type_icons_by_idnos)){
			foreach($va_collection_type_icons_by_idnos as $vs_idno => $vs_icon){
				$va_collection_type_icons[$t_list->getItemId("collection_types", $vs_idno)] = $vs_icon;
			}
		}
		if($qr_collections->numHits()){
			while($qr_collections->nextHit()) {
				if($va_exclude_collection_type_ids && is_array($va_exclude_collection_type_ids) && (in_array($qr_collections->get("ca_collections.type_id"), $va_exclude_collection_type_ids))){
					continue;
				}
		
				$vs_icon = "";
				if(is_array($va_collection_type_icons) && $va_collection_type_icons[$qr_collections->get("ca_collections.type_id")]){
					$vs_icon = $va_collection_type_icons[$qr_collections->get("ca_collections.type_id")];
				}			
				# --- related objects?
				$va_object_ids = $qr_collections->get("ca_objects.object_id", array("returnAsArray" => true, 'checkAccess' => $va_access_values));
				$vn_rel_object_count = sizeof($va_object_ids);
				$va_child_ids = $qr_collections->get("ca_collections.children.collection_id", array("returnAsArray" => true, "checkAccess" => $va_access_values, "sort" => $vs_sub_collection_sort));
				$vs_output .= "<div class='unit' style='margin-left:".(40*($vn_level - 1))."px;'>";
				if($vs_icon){
					$vs_output .= $vs_icon." ";
				}
				$vs_output .= "<b>";
				if($vs_sub_collection_label_template){
					$vs_output .= $qr_collections->getWithTemplate($vs_sub_collection_label_template);
				}else{
					$vs_output .= $qr_collections->get("ca_collections.preferred_labels");
				}
				$vs_output .= "</b>";
			
				if($vn_rel_object_count){
					$vs_output .= " <span class='small'>(".$vn_rel_object_count." record".(($vn_rel_object_count == 1) ? "" : "s").")</span>";
				}
				$vs_output .= "<br/>";
				if(!$vb_dont_show_top_level_description){
					$vs_desc = "";
					if($vs_sub_collection_desc_template && ($vs_desc = $qr_collections->getWithTemplate($vs_sub_collection_desc_template))){
						$vs_output .= "<p>".$vs_desc."</p>";
					}
				}
				# --- objects
				if(sizeof($va_object_ids)){
					$qr_objects = caMakeSearchResult("ca_objects", $va_object_ids);
					while($qr_objects->nextHit()){
						$vs_output .= "<div style='margin-left:20px;'>";
						if($vs_object_template){
							$vs_output .= $qr_objects->getWithTemplate($vs_object_template);
						}else{
							$vs_output .= $qr_objects->get("ca_objects.preferred_labels.name");
						}
						$vs_output .= "</div>";
					}
				}
				$vs_output .= "</div>";
				if(sizeof($va_child_ids)) {
					$vs_output .=  caGetCollectionLevelSummary($va_child_ids, $vn_level + 1);
				}
			}
		}
		return $vs_output;
	}
	# ---------------------------------------
	# NEW HELPERS: React rewrite, May 2019
	# ---------------------------------------
	/**
	 * Return menu bar list suitable for inclusion in a Bootstrap 4.1 layout.
	 * Will set "active" style on a menu item if the current request routes to that item.
	 * 
	 * @param array $items A dictionary of menu items. Keys are menu titles, values are dictionarys with route elements: module, controller and action
	 * @param array $options No options are currently supported
	 *
	 * @return array
	 */	
	function caGetNavItemsForBootstrap($items, $options=null) {
		global $g_request;

		$current_module_path = $g_request->getModulePath();
		$current_controller = $g_request->getController();
		$current_action = $g_request->getAction();
		$buf = [];
		foreach($items as $name => $info) {
			$action_bits = explode('/', $info['action']);
			$active = (($action_bits[0] === $current_action) && ($info['controller'] === $current_controller) && ($info['module'] == $current_module_path)) ? "active" : "";
			$buf[] = "<li class=\"nav-item {$active}\">".caNavLink($name, "nav-link", $info['module'], $info['controller'], $info['action'])."</li>";
		}
		return $buf;
	}
	# ---------------------------------------
	/**
	 * Return "user" menu for nav bar, suitable for display in a Bootstrap 4.1 layout.
	 *
	 * @param string $title
	 * @param array $options No options are currently supported
	 *
	 * @return array
	 */	
	function caGetNavUserItemsForBootstrap($title, $options=null) {
		global $g_request;
		
		$lightbox_display_name = caGetLightboxDisplayName();
		$lightbox_section_heading = caUcFirstUTF8Safe($lightbox_display_name["section_heading"]);
	
		# Collect the user links: they are output twice, once for toggle menu and once for nav
		$user_links = [];
		if($g_request->isLoggedIn()){
			$user_links[] = '<a role="presentation" class="dropdown-item">'.trim($g_request->user->get("fname")." ".$g_request->user->get("lname")).', '.$g_request->user->get("email").'</a>';
			$user_links[] = '<a class="divider nav-divider"></a>';
			if(caDisplayLightbox($g_request)){
				$user_links[] = caNavLink($g_request, $lightbox_section_heading, '', '', 'Lightbox', 'Index', array());
			}
			$user_links[] = caNavLink($g_request, _t('User Profile'), 'dropdown-item', '', 'LoginReg', 'profileForm', array());
		
			if ($g_request->config->get('use_submission_interface')) {
				$user_links[] = caNavLink($g_request, _t('Submit content'), 'dropdown-item', '', 'Contribute', 'List', array());
			}
			$user_links[] = caNavLink($g_request, _t('Logout'), '', '', 'LoginReg', 'Logout', array());
		} else {	
			if (!$g_request->config->get(['dontAllowRegistrationAndLogin', 'dont_allow_registration_and_login']) || $g_request->config->get('pawtucket_requires_login')) { $user_links[] = "<a href='#' class=\"dropdown-item\" onclick='caMediaPanel.showPanel(\"".caNavUrl('', 'LoginReg', 'LoginForm', array())."\"); return false;' >"._t("Login")."</a>"; }
			if (!$g_request->config->get(['dontAllowRegistrationAndLogin', 'dont_allow_registration_and_login']) && !$g_request->config->get('dontAllowRegistration')) { $user_links[] = "<a href='#' class=\"dropdown-item\" onclick='caMediaPanel.showPanel(\"".caNavUrl('', 'LoginReg', 'RegisterForm', array())."\"); return false;' >"._t("Register")."</a>"; }
		}
		if (sizeof($user_links)) { 
			array_unshift($user_links, "<li class=\"nav-item dropdown\"><a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"userDropdown\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">{$title}</a><div class=\"dropdown-menu\" aria-labelledby=\"userDropdown\">");
			array_push($user_links, "</div></li>");    
		}
		//print_r($user_links);
		return $user_links;
	}
	# ---------------------------------------
