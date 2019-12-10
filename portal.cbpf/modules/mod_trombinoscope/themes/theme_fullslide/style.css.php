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
		
#te_<?php echo $suffix; ?> .outerperson {	
	<?php if ($fontcolor != '') : ?>
		color: <?php echo $fontcolor; ?>;
	<?php endif; ?>
	
	<?php if ($bgcolor1 != '') : ?>
		background: <?php echo $bgcolor1; ?>;
	<?php elseif ($bgcolor2 != '') : ?>
		background: <?php echo $bgcolor2; ?>;
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
	
	margin: 6px;	
	
	<?php if ($picture_shadow) : ?>
		-moz-box-shadow: 0px 0px 4px #000000;
		-webkit-box-shadow: 0px 0px 4px #000000;
		box-shadow: 0px 0px 4px #000000;
		/* IE 7 AND 8 DO NOT SUPPORT BLUR PROPERTY OF SHADOWS */
	<?php endif; ?>
}

#te_<?php echo $suffix; ?> .odd {
	border-bottom: none; /* to override k2 style */
	padding: 0; /* to override k2 style */
	background-color: transparent; /* to override k2 style */
}

#te_<?php echo $suffix; ?> .even {
	border-bottom: none; /* to override k2 style */
	padding: 0; /* to override k2 style */
	background-color: transparent; /* to override k2 style */
}

<?php if ($border_width > 0) : ?>
	#te_<?php echo $suffix; ?> .innerperson {	
		border: <?php echo $border_width; ?>px solid <?php echo $pic_bgcolor; ?>;	
	}
<?php endif; ?>

#te_<?php echo $suffix; ?> .personpicture {
	float: none !important;
	z-index: 75 !important;
}

#te_<?php echo $suffix; ?> .picture {
	width: 100% !important;
	margin-left: -<?php echo intval($picture_width / 2); ?>px;
	left: 50%;
}

html[dir="rtl"] #te_<?php echo $suffix; ?> .picture {
	margin-right: -<?php echo intval($picture_width / 2); ?>px;
	right: 50%;
}

#te_<?php echo $suffix; ?> .personinfo {	
	position: absolute;
	overflow: auto;
	top: 0;
	left: 0;
	right: 0;
	<?php if ($show_links || $show_vcard) : ?>
		height: <?php echo ($picture_height - 25); ?>px;
	<?php else : ?>
		height: <?php echo $picture_height; ?>px;
	<?php endif; ?>
	padding: 5px 8px;
	box-sizing: border-box;
}

#te_<?php echo $suffix; ?> .index1.fieldposition .fieldvalue {
	font-style: italic;
}

#te_<?php echo $suffix; ?> .personfield,
#te_<?php echo $suffix; ?> .personlinks {
	font-size: 0.9em;
}
							
#te_<?php echo $suffix; ?> .personfield.fieldname span {
	font-size: 1.5em;
	font-weight: bold;
	line-height: 1.2em;
}

#te_<?php echo $suffix; ?> .featured .picture_veil .feature {
	display: block;	
	position: absolute;
	top: 5px;
	left: 15px;
}

#te_<?php echo $suffix; ?> .iconlinks {
	bottom: <?php echo ($border_width + 2); ?>px;
	<?php if ($show_vcard) : ?>
		left: <?php echo ($border_width + 5); ?>px;
	<?php else : ?>
		width: 100%;
	<?php endif; ?>
}

<?php if (!$show_vcard) : ?>
	#te_<?php echo $suffix; ?> .iconlinks ul {
		margin: 0 auto;
	}
<?php endif; ?>

#te_<?php echo $suffix; ?> .vcard {
	bottom: <?php echo ($border_width + 2); ?>px;
	right: <?php echo ($border_width + 5); ?>px;
}

#te_<?php echo $suffix; ?> .vcard a span {
	display: none;
}
	
#te_<?php echo $suffix; ?> .personinfo .fieldname,						
#te_<?php echo $suffix; ?> .personinfo .category,
#te_<?php echo $suffix; ?> .personinfo .tags {
	font-size: .8em;
	text-align: left !important;
}

/* animations */

#te_<?php echo $suffix; ?> .personpicture {
   -webkit-transition: all 0.3s ease-in-out;
   -moz-transition: all 0.3s ease-in-out;
   -o-transition: all 0.3s ease-in-out;
   -ms-transition: all 0.3s ease-in-out;
   transition: all 0.3s ease-in-out;
}

#te_<?php echo $suffix; ?> .outerperson:hover .personpicture {
   -webkit-transform: translateX(<?php echo $picture_width; ?>px);
   -moz-transform: translateX(<?php echo $picture_width; ?>px);
   -o-transform: translateX(<?php echo $picture_width; ?>px);
   -ms-transform: translateX(<?php echo $picture_width; ?>px);
   transform: translateX(<?php echo $picture_width; ?>px);
}

#te_<?php echo $suffix; ?>.ie8 .outerperson:hover .personpicture {
	-ms-filter: "progid:DXImageTransform.Microsoft.Matrix(M11=1, M12=0, M21=0, M22=1, SizingMethod='auto expand')";
	margin-left: <?php echo $picture_width; ?>px;
}

#te_<?php echo $suffix; ?> .personinfo {
   -webkit-transform: translateX(-<?php echo $picture_width; ?>px);
   -moz-transform: translateX(-<?php echo $picture_width; ?>px);
   -o-transform: translateX(-<?php echo $picture_width; ?>px);
   -ms-transform: translateX(-<?php echo $picture_width; ?>px);
   transform: translateX(-<?php echo $picture_width; ?>px);
   -webkit-transition: all 0.3s ease-in-out;
   -moz-transition: all 0.3s ease-in-out;
   -o-transition: all 0.3s ease-in-out;
   -ms-transition: all 0.3s ease-in-out;
   transition: all 0.3s ease-in-out;
}

#te_<?php echo $suffix; ?>.ie8 .personinfo {
	-ms-filter: "progid:DXImageTransform.Microsoft.Matrix(M11=1, M12=0, M21=0, M22=1, SizingMethod='auto expand')";
	margin-left: -<?php echo $picture_width; ?>px;
}

#te_<?php echo $suffix; ?> .outerperson:hover .personinfo {
   -webkit-transform: translateX(0px);
   -moz-transform: translateX(0px);
   -o-transform: translateX(0px);
   -ms-transform: translateX(0px);
   transform: translateX(0px);
}

#te_<?php echo $suffix; ?>.ie8 .outerperson:hover .personinfo {
	-ms-filter: "progid:DXImageTransform.Microsoft.Matrix(M11=1, M12=0, M21=0, M22=1, SizingMethod='auto expand')";
	margin-left: 0px;
}
