<?php 
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// No direct access to this file
defined('_JEXEC') or die;

// Explicitly declare the type of content
header("Content-type: text/css; charset=UTF-8");
?>

/* Contains font styles, borders, colors and images */

#te_<?php echo $suffix; ?> .person {		
	<?php if ($fontcolor != '') : ?>
		color: <?php echo $fontcolor; ?>;
	<?php endif; ?>
	
	<?php if ($bgcolor1 != '') : ?>
		background: <?php echo $bgcolor1; ?>;
	<?php elseif ($bgcolor2 != '') : ?>
		background: <?php echo $bgcolor2; ?>;
	<?php else : ?>
		background-color: #F5F5F5;
	<?php endif; ?>
		
	<?php if ($bgcolor1 != $bgcolor2 && $bgcolor1 != '' && $bgcolor2 != '') : ?>
		background: -moz-linear-gradient(top, <?php echo $bgcolor1; ?> 0%, <?php echo $bgcolor2; ?> 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $bgcolor1; ?>), color-stop(100%,<?php echo $bgcolor2; ?>)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* Opera11.10+ */
		background: -ms-linear-gradient(top, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $bgcolor1; ?>', endColorstr='<?php echo $bgcolor2; ?>',GradientType=0 ); /* IE6-9 (IE9 cannot use SVG because the colors are dynamic) */
		background: linear-gradient(top, <?php echo $bgcolor1; ?> 0%,<?php echo $bgcolor2; ?> 100%); /* W3C */
	<?php endif; ?>	
	
	<?php if ($bgimage != '') : ?>
		background-image: url(../../<?php echo $bgimage; ?>);
		background-position: top left;
		background-repeat: repeat;
	<?php endif; ?>
}

#te_<?php echo $suffix; ?> .odd {
	border-bottom: none; /* to override k2 style */
	padding: 0; /* to override k2 style */
}

#te_<?php echo $suffix; ?> .even {
	border-bottom: none; /* to override k2 style */
	padding: 0; /* to override k2 style */
}

#te_<?php echo $suffix; ?> .innerperson {
	border: 1px solid #FFFFFF;
	margin: 2px; 
	padding: 2px;
}

#te_<?php echo $suffix; ?> .picture_left .personpicture {
	margin-right: 5px;
}

#te_<?php echo $suffix; ?> .picture_right .personpicture {
	margin-left: 5px;
}

#te_<?php echo $suffix; ?> .picture {	
	margin: 25px auto 10px auto;
	<?php if ($border_width > 0) : ?>
		padding: <?php echo $border_width; ?>px;
	<?php endif; ?>
	background-color: <?php echo $pic_bgcolor; ?>;
	<?php if ($picture_shadow) : ?>
		-moz-box-shadow: 0px 0px 8px #000000;
		-webkit-box-shadow: 0px 0px 8px #000000;
		box-shadow: 0px 0px 8px #000000;
		/* IE 7 AND 8 DO NOT SUPPORT BLUR PROPERTY OF SHADOWS */
	<?php endif; ?>
}

#te_<?php echo $suffix; ?> .picture_left .picture,
#te_<?php echo $suffix; ?> .picture_right .picture {	
	margin: <?php echo (35 + $hover_extra_margin) ?>px <?php echo (10 + $hover_extra_margin) ?>px <?php echo (10 + $hover_extra_margin) ?>px <?php echo (10 + $hover_extra_margin) ?>px;
}

#te_<?php echo $suffix; ?> .picture_top .picture {	
	margin: <?php echo (35 + $hover_extra_margin) ?>px auto <?php echo (10 + $hover_extra_margin) ?>px auto;
}
			
#te_<?php echo $suffix; ?> .picture_left .personinfo {	
	<?php if ($overflow) : ?>
		padding: 25px 5px 5px 15px;
	<?php else : ?>
		padding: 25px 5px 5px 0;
	<?php endif; ?>
}

#te_<?php echo $suffix; ?> .picture_right .personinfo {	
	<?php if ($overflow) : ?>
		padding: 25px 15px 5px 5px;
	<?php else : ?>
		padding: 25px 0 5px 5px;
	<?php endif; ?>
}

#te_<?php echo $suffix; ?> .picture_top .personinfo {
	padding: 5px;
}

#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .personinfo {
	padding: 25px 5px 5px 5px;
}
			
#te_<?php echo $suffix; ?> .text_only .personinfo {
	padding: 25px 5px 5px 15px;
}
							
#te_<?php echo $suffix; ?> .fieldname span {
	font-size: 1.5em;
	font-weight: bold;
	line-height: 1.2em;
}

#te_<?php echo $suffix; ?> .picture_top .index1.fieldposition,
#te_<?php echo $suffix; ?> .ghost_picture_top .index1.fieldposition {
	display: inline-block;
    text-align: center;
    width: 100%;
}

#te_<?php echo $suffix; ?> .index1.fieldposition .fieldvalue {
	font-style: italic;
}

#te_<?php echo $suffix; ?> .personfield,
#te_<?php echo $suffix; ?> .personlinks {
	font-size: 0.9em;
}

#te_<?php echo $suffix; ?> .picture_top .personfield.index3,
#te_<?php echo $suffix; ?> .picture_top .personfield.index4,
#te_<?php echo $suffix; ?> .picture_top .personfield.index5,
#te_<?php echo $suffix; ?> .picture_top .personfield.index6,
#te_<?php echo $suffix; ?> .picture_top .personfield.index7,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .personfield.index3,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .personfield.index4,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .personfield.index5,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .personfield.index6,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .personfield.index7 {
	padding-left: 0;
}

#te_<?php echo $suffix; ?> .personfield.index3,
#te_<?php echo $suffix; ?> .personfield.index4,
#te_<?php echo $suffix; ?> .personfield.index5,
#te_<?php echo $suffix; ?> .personfield.index6,
#te_<?php echo $suffix; ?> .personfield.index7 {
	padding-left: 30px;
}

#te_<?php echo $suffix; ?> .featured.picture_left .picture_veil .feature,
#te_<?php echo $suffix; ?> .featured.picture_right .picture_veil .feature,
#te_<?php echo $suffix; ?> .featured.picture_top .picture_veil .feature {
	display: block;
	top: 5px;
	left: 5px;
	position: absolute;
}

#te_<?php echo $suffix; ?> .featured.text_only.ghost_picture_top .text_veil .feature {
	display: block;
	top: 8px;
	left: 10px;
	right: auto;
	position: absolute;
}
	
#te_<?php echo $suffix; ?> .featured.text_only .text_veil .feature {
	display: block;
	top: 30px;
	right: 10px;
	position: absolute;
}

#te_<?php echo $suffix; ?> .picture_left .iconlinks {
	top: 8px;
	right: 10px;
}

#te_<?php echo $suffix; ?> .picture_right .iconlinks,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_right .iconlinks {
	top: 8px;
	left: 10px;
	right: auto;
}

#te_<?php echo $suffix; ?> .picture_top .iconlinks,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .iconlinks {
	text-align: center;
    top: 8px;
    width: 100%;
    right: auto;
}

#te_<?php echo $suffix; ?> .text_only .iconlinks {
	top: 8px;
	right: 10px;
}

#te_<?php echo $suffix; ?> .picture_left .vcard,
#te_<?php echo $suffix; ?> .text_only .vcard {
	top: 8px;
	left: 10px;
}

#te_<?php echo $suffix; ?> .picture_right .vcard,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_right .vcard {
	top: 8px;
	right: 10px;
	left: auto;
}

#te_<?php echo $suffix; ?> .picture_top .vcard,
#te_<?php echo $suffix; ?> .text_only.ghost_picture_top .vcard {
    top: 8px;
	right: 10px;
	left: auto;
}

#te_<?php echo $suffix; ?> .vcard a span {
	display: none;
}
							
#te_<?php echo $suffix; ?> .personinfo .category,
#te_<?php echo $suffix; ?> .personinfo .tags {
	font-size: 0.8em;
}
