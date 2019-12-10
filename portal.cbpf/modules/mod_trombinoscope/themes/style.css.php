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

	#te_<?php echo $suffix; ?> .groupheader {
	    font-size: <?php echo $font_size; ?>px;
	}

	#te_<?php echo $suffix; ?> .person {
		font-size: <?php echo $font_size; ?>px;
		width: <?php echo $card_width; ?><?php echo $card_width_unit; ?>;		
		<?php if ($card_width_unit == '%') : ?>
			margin-left: <?php echo $margin_in_perc; ?>%; 
			margin-right: <?php echo $margin_in_perc; ?>%;
		<?php else : ?>
			margin-left: 3px;
			margin-right: 3px;
		<?php endif; ?>
	}
					
						#te_<?php echo $suffix; ?> .iconlinks .icon {
							font-size: <?php echo $iconfont_size; ?>em;
							color: <?php echo $iconfont_color; ?>;
						}
			
				#te_<?php echo $suffix; ?> .vcard .icon {
				    font-size: <?php echo $iconfont_size; ?>em;
					color: <?php echo $iconfont_color; ?>;
    			}				

		<?php if ($card_height > 0) : ?>
			#te_<?php echo $suffix; ?> .innerperson {			
				height: <?php echo $card_height; ?>px;
			}		
		<?php endif; ?>
			
				#te_<?php echo $suffix; ?> .featured .feature .icon {
				    font-size: <?php echo $iconfont_size; ?>em;
					color: <?php echo $iconfont_color; ?>;
    			}
				
				#te_<?php echo $suffix; ?> .picture {
	    			width: <?php echo $picture_width; ?>px;
					height: <?php echo $picture_height; ?>px;					
					<?php if ($border_radius > 0) : ?>
						border-radius: <?php echo $border_radius; ?>px;
						-moz-border-radius: <?php echo $border_radius; ?>px;
						-webkit-border-radius: <?php echo $border_radius; ?>px;
						/* IE 7 AND 8 DO NOT SUPPORT BORDER RADIUS */
					<?php endif; ?>
				}
					
					#te_<?php echo $suffix; ?> .picture img {
						max-width: <?php echo $picture_width; ?>px;
						max-height: <?php echo $picture_height; ?>px;						
						<?php if ($border_radius_img > 0) : ?>
							border-radius: <?php echo $border_radius_img; ?>px;
							-moz-border-radius: <?php echo $border_radius_img; ?>px;
							-webkit-border-radius: <?php echo $border_radius_img; ?>px;
							/* IE 7 AND 8 DO NOT SUPPORT BORDER RADIUS */
						<?php endif; ?>
					}
			
			<?php if (!$overflow) : ?>
				#te_<?php echo $suffix; ?> .personinfo {					
					overflow: hidden;
				}
			<?php endif; ?>
			
				<?php if (!$force_one_line) : ?>
					#te_<?php echo $suffix; ?> .personfield.fieldname {
						white-space: normal;
					}
				<?php endif; ?>
				
					#te_<?php echo $suffix; ?> .personinfo .icon {
						font-size: <?php echo $iconfont_size; ?>em;
						color: <?php echo $iconfont_color; ?>;
					}
					
					#te_<?php echo $suffix; ?> .personinfo .noicon {
						font-size: <?php echo $iconfont_size; ?>em;
					}
				
					<?php if ($label_width > 0) : ?>
						#te_<?php echo $suffix; ?> .fieldlabel {
							width: <?php echo $label_width; ?>px;
						}
					<?php endif; ?>
		
/* carousel */

<?php if ($animated) : ?>
	/* avoid the flickering when loading - has to be done before the wrapper is applied */
	#te_<?php echo $suffix; ?> .personlist {
		overflow: hidden;
		height: 0px;
	}
<?php endif; ?>

#te_<?php echo $suffix; ?> .items_pagination {
	font-size: <?php echo $arrow_size; ?>em;
}

#te_<?php echo $suffix; ?>.side_navigation .items_pagination {
	top: <?php echo $arrow_offset; ?>px;
}

#te_<?php echo $suffix; ?>.above_navigation .items_pagination.top {
	margin-bottom: <?php echo $arrow_offset; ?>px;
}

#te_<?php echo $suffix; ?>.above_navigation .items_pagination.bottom {
	margin-top: <?php echo $arrow_offset; ?>px;
}

#te_<?php echo $suffix; ?>.under_navigation .items_pagination {
	margin-top: <?php echo $arrow_offset; ?>px;
}
