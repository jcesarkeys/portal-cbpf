<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

if ($remove_whitespaces) {
	ob_start(function($buffer) { return preg_replace('/\s+/', ' ', $buffer); });
}

$contactcount = count($list);
$previous_header = '';
$header = '';
?>

<!--[if lte IE 8]>
	<div id="te_<?php echo $class_suffix; ?>" class="te te_<?php echo $class_suffix; ?><?php echo $arrow_class; ?><?php echo $isMobile ? ' mobile' : ''; ?> ie8">
<![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<div id="te_<?php echo $class_suffix; ?>" class="te te_<?php echo $class_suffix; ?><?php echo $arrow_class; ?><?php echo $isMobile ? ' mobile' : ''; ?>">
<!--<![endif]-->

	<?php if (trim($params->get('pretext', ''))) : ?>
		<div class="pretext">
			<?php 				
				if ($params->get('allow_plugins_prepost', 0)) {					
					echo JHTML::_('content.prepare', $params->get('pretext'));
				} else {
					echo $params->get('pretext');
				}
			?>
		</div>
	<?php endif; ?>

	<?php if (empty($cat_order) && $show_category_header) : ?>
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		  	<?php echo JText::_('MOD_TROMBINOSCOPE_MESSAGE_ORDERFORCATEGORYHEADER'); ?>
		</div>
	<?php endif; ?>

	<?php if ($show_arrows && ($arrow_prev_left || $arrow_prev_top)) : ?>
		<div class="items_pagination top<?php echo empty($extra_pagination_classes) ? '' : ' '.$extra_pagination_classes; ?>">
			<ul>
			<?php if ($arrow_prev_left) : ?>
				<li><a id="prev_<?php echo $class_suffix; ?>" class="previous" href="#"><i class="SYWicon-arrow-left2"></i></a></li>
			<?php endif; ?>
			
			<?php if ($arrow_prev_top) : ?>
				<li><a id="prev_<?php echo $class_suffix; ?>" class="previous" href="#"><i class="SYWicon-arrow-up2"></i></a></li>
			<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="personlist">

		<?php foreach ($list as $i => $item) : ?>	
			
			<?php
				// if the user is logged in and the user is linked to the contact, allow edit through the profile.
				$allow_edit = false;
				if ($user->get('guest') != 1 && $item->user_id == $user->get('id')) {
					$allow_edit = true;
				}
			
				// header
				if ($cat_order && $show_category_header) {
					$header = $item->category;
					if ($previous_header == $header) {
						$header = 'no_show';
					} else {
						$previous_header = $header;
					}
				}	
					
				// Convert parameter fields to objects.
				$itemparams = new JRegistry();
				$itemparams->loadString($item->params);	
			
				// link
				$link = '';
				$extra_attributes = '';
				if (in_array($link_access, $groups)) {
					switch ($contact_link) {
						case 'view' : 
							$link = JRoute::_(modTrombinoscopeHelper::getContactRoute('trombinoscopeextended', $item->slug, $item->catid));
							$extra_attributes = ' class="hasTooltip"';
							break;
						case 'standard' : $link = JRoute::_(modTrombinoscopeHelper::getContactRoute('contact', $item->slug, $item->catid));
							$extra_attributes = ' class="hasTooltip"';
							break;
						case 'popup' : // open in a modal window
							$link = JRoute::_(modTrombinoscopeHelper::getContactRoute('contact', $item->slug, $item->catid).'&tmpl=component');
							JHtml::_('behavior.modal', 'a.modal');
							$extra_attributes = ' class="modal hasTooltip" rel="{handler: \'iframe\', size: {x:'.$popup_x.', y:'.$popup_y.'}}"';
							break;
						case 'linka' : $extra_attributes = ' class="hasTooltip" target="_blank"';
						case 'linka_sw' : $link = $itemparams->get('linka');
							break;
						case 'linkb' : $extra_attributes = ' class="hasTooltip" target="_blank"';
						case 'linkb_sw' : $link = $itemparams->get('linkb');
							break;
						case 'linkc' : $extra_attributes = ' class="hasTooltip" target="_blank"';
						case 'linkc_sw' : $link = $itemparams->get('linkc');
							break;
						case 'linkd' : $extra_attributes = ' class="hasTooltip" target="_blank"';
						case 'linkd_sw' : $link = $itemparams->get('linkd');
							break;
						case 'linke' : $extra_attributes = ' class="hasTooltip" target="_blank"';
						case 'linke_sw' : $link = $itemparams->get('linke');
							break;
						case 'generic' : $extra_attributes = ' class="hasTooltip" target="_blank"';
						case 'generic_sw' : $link = $generic_link;
							break;
					}
				}
				
				// category links
				$link_category = '';
				$link_category_header = '';
				
				if ($link_to_category) {
					$link_category = JRoute::_(modTrombinoscopeHelper::getCategoryRoute($item->catid));
				} else if ($link_to_view_category) {
					if (empty($cat_view_id) || $cat_view_id == 'auto') {
						$link_category = JRoute::_('index.php?option=com_trombinoscopeextended&view=trombinoscope&category='.$item->catid.'&referer=module');
					} else {
						$link_category = JRoute::_('index.php?option=com_trombinoscopeextended&view=trombinoscope&Itemid='.$cat_view_id.'&category='.$item->catid);
					}
				}
				
				if ($link_to_category_header) {
					$link_category_header = JRoute::_(modTrombinoscopeHelper::getCategoryRoute($item->catid));
				} elseif ($link_to_view_category_header) {
					if (empty($header_view_id) || $header_view_id == 'auto') {
						$link_category_header = JRoute::_('index.php?option=com_trombinoscopeextended&view=trombinoscope&category='.$item->catid.'&referer=module');
					} else {
						$link_category_header = JRoute::_('index.php?option=com_trombinoscopeextended&view=trombinoscope&Itemid='.$header_view_id.'&category='.$item->catid);
					}
				}
				
				// name format
				$formatted_name = modTrombinoscopeHelper::getFormattedName($item->name, $format_style, $uppercase);
						
				$extraclasses = " personid-".$item->id." catid-".$item->catid;				
				
				if (isset($item->tags)) {
					foreach ($item->tags as $tag) {
						$extraclasses .= " tag-".$tag->id;
					}
				}
				
				$extraclasses .= ($i % 2) ? " even" : " odd";
				
				if ($item->featured && $show_featured) {
					$extraclasses .= " featured";
				}
				
				if ($style_social_icons) {
					$extraclasses .= " social";
				}
				
				if (!$show_picture) {
					$extraclasses .= " text_only";
				} else {
					$extraclasses .= " ";
					if (!$keep_picture_space && empty($item->image) && empty($default_picture) && $globalparams->get('default_image') == null) {
						$extraclasses .= "text_only ghost_";
					}
					switch ($photo_align) {
						case 'l': $extraclasses .= "picture_left"; break;
						case 'r': $extraclasses .= "picture_right"; break;
						case 't': $extraclasses .= "picture_top"; break;
						case 'lr': $extraclasses .= ($i % 2) ? "picture_right" : "picture_left"; break;
						case 'rl': $extraclasses .= ($i % 2) ? "picture_left" : "picture_right"; break;
						default : $extraclasses .= "picture_left";
					}
				}
				
				if ($show_picture && $crop_picture) {	
					if (empty($item->image)) {
						if (empty($default_picture)) {
							if ($globalparams->get('default_image') != null) {
								$item->image = modTrombinoscopeHelper::getCroppedImage($class_suffix, 'global', $globalparams->get('default_image'), $tmp_path, $clear_cache, $picture_width, $picture_height, $crop_picture, $quality, $filter, $create_highres_images);
							} else {
								$item->image = '';
							}
						} else {
							$item->image = modTrombinoscopeHelper::getCroppedImage($class_suffix, 'default', $default_picture, $tmp_path, $clear_cache, $picture_width, $picture_height, $crop_picture, $quality, $filter, $create_highres_images);
						}
					} else {		
						$item->image = modTrombinoscopeHelper::getCroppedImage($class_suffix, $item->id, $item->image, $tmp_path, $clear_cache, $picture_width, $picture_height, $crop_picture, $quality, $filter, $create_highres_images);
					}
					
					if ($item->image == 'error') {
						$item->error[] = JText::_('MOD_TROMBINOSCOPE_ERROR_CREATINGTHUMBNAIL');
						$item->image = '';
					}
				}
			?>
			
			<?php if ($header && $header != 'no_show') : ?>	
				<?php if ($i > 1) : ?>
					</div>
				<?php endif; ?>	
				<div class="groupheader">
					<?php if ($show_category_header) : ?>
						<?php echo '<h'.$header_html_tag.' class="header">'; ?>
							<?php if ($link_category_header) : ?>
								<a href="<?php echo $link_category_header; ?>" class="hasTooltip" title="<?php echo $header ?>">
									<span><?php echo $header ?></span>
								</a>
							<?php else : ?>	
								<span><?php echo $header ?></span>
							<?php endif; ?>
						<?php echo '</h'.$header_html_tag.'>'; ?>
					<?php endif; ?>
				</div>	
				<div class="contactgroup">
			<?php endif; ?>
			
			<div class="person<?php echo $extraclasses ?>">
				<div class="outerperson">
			
					<?php if ($show_errors && !empty($item->error)) : ?>					
						<?php foreach ($item->error as $error) : ?>
							<div class="alert alert-error">
					  			<button type="button" class="close" data-dismiss="alert">&times;</button>
					  			<?php echo $error; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
						
					<?php if ($show_links) : ?>		
						<?php														
							$requested_links = modTrombinoscopeHelper::getRequestedLinks($params, $item);
							$requested_links_output = modTrombinoscopeHelper::getRequestedLinksOutput($requested_links, $params, $item, $extraclasseslinkfields);
						?>		
						<div class="iconlinks">
							<?php if (!empty($requested_links_output)) : ?>
								<ul>
									<?php echo $requested_links_output; ?>
								</ul>
							<?php endif; ?>
						</div>								
					<?php endif; ?>
						
					<?php if ($show_vcard) : ?>
						<div class="vcard">
							<?php if ($standalone) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_contact&view=contact&id='.$item->id.'&format=vcf'); ?>" class="hasTooltip" title="<?php echo JText::_('MOD_TROMBINOSCOPE_DOWNLOAD_VCARD');?>">
									<i class="icon SYWicon-vcard"></i><span><?php echo JText::_('MOD_TROMBINOSCOPE_VCARD');?></span>
								</a>
							<?php else : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_trombinoscopeextended&view=contact&catid='.$item->catid.'&id='.$item->id.'&format=vcf&type='.$vcard_type.'&af='.$address_format); ?>" class="hasTooltip" title="<?php echo JText::_('MOD_TROMBINOSCOPE_DOWNLOAD_VCARD');?>">
									<i class="icon SYWicon-vcard"></i><span><?php echo JText::_('MOD_TROMBINOSCOPE_VCARD');?></span>
								</a>
							<?php endif; ?>
						</div>	
					<?php endif; ?>
					
					<div class="innerperson">
						<div class="personpicture">
						
							<?php if (isset($item->individual_bg) && !empty($item->individual_bg)) : ?>
								<div class="individualbg">
									<div class="innerindividualbg">
										<?php echo JHTML::_('image', $item->individual_bg, null); ?>
									</div>
								</div>
							<?php endif; ?>
						
							<div class="picture<?php echo ($picture_hover_type != 'none' && !empty($link)) ? ' '.$picture_hover_type : ''; ?>">
								<div class="picture_veil">
									<?php if ($item->featured && $show_featured) : ?>
										<div class="feature">
											<i class="icon SYWicon-<?php echo $featured_icon; ?>"></i>
										</div>
									<?php endif; ?>
								</div>
								<?php if (!empty($link)) : ?>	
									<a<?php echo $extra_attributes; ?> href="<?php echo $link; ?>" title="<?php echo $formatted_name; ?>" >
								<?php endif; ?>
								<?php if (!empty($item->image)) : ?>
									<?php if ($create_highres_images) : ?>
										<?php echo JHTML::_('image', $item->image, $formatted_name, array('class' => 'hasTooltip', 'title' => $formatted_name, 'data-src' => str_replace('.', '@2x.', JURI::base(true).'/'.$item->image))); ?>
									<?php else : ?>
										<?php echo JHTML::_('image', $item->image, $formatted_name, array('class' => 'hasTooltip', 'title' => $formatted_name)); ?>
									<?php endif; ?>
								<?php elseif (!empty($default_picture)) : ?>
									<?php echo JHTML::_('image', $default_picture, $formatted_name, array('class' => 'hasTooltip', 'title' => $formatted_name)); ?>
								<?php elseif ($globalparams->get('default_image') != null) : ?>	
									<?php echo JHTML::_('image', $globalparams->get('default_image'), $formatted_name, array('class' => 'hasTooltip', 'title' => $formatted_name)); ?>
								<?php else : ?>							
									<span class="nopicture">&nbsp;</span>
								<?php endif; ?>
								<?php if (!empty($link)) : ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					
						<div class="personinfo">	
							<div class="text_veil">
								<?php if ($item->featured && $show_featured) : ?>
									<div class="feature">
										<i class="icon SYWicon-<?php echo $featured_icon; ?>"></i>
									</div>
								<?php endif; ?>
							</div>				
							
							<?php if ($show_category) : ?>
								<div class="category">
									<?php if ($link_category) : ?>												
										<a href="<?php echo $link_category; ?>" class="hasTooltip" title="<?php echo $item->category; ?>" >
											<span><?php echo $item->category; ?></span>
										</a>
									<?php else : ?>
										<span><?php echo $item->category; ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php if ($show_tags) : ?>
								<?php if (isset($item->tags)) : ?>
									<div class="tags">
										<?php if (JFolder::exists(JPATH_ROOT.'/components/com_trombinoscopeextended/layouts')) : ?>
											<?php 
												if (version_compare(JVERSION, '3.2.0', 'ge')) {
													$layout = new JLayoutFile('tcptag', null, array('component' => 'com_trombinoscopeextended')); // gets override from component 
												} else {
													$layout = new JLayoutFile('tcptag', JPATH_ROOT.'/components/com_trombinoscopeextended/layouts'); // does not get override (Joomla < 3.2)
												}
											?>
										<?php else : ?>
											<?php $layout = new JLayoutFile('tcptag', JPATH_ROOT.'/modules/mod_trombinoscope/layouts'); // no overrides possible ?>
										<?php endif; ?>									
									
										<?php foreach ($item->tags as $tag) :  ?>											
											<?php 												
												$data = array('tag' => $tag);
												if ($link_to_tags) {
													$data['link'] = JRoute::_(TagsHelperRoute::getTagRoute($tag->id . ':' . $tag->alias));
												} elseif ($link_to_view_tags && !empty($tags_view_id)) {
													$data['link'] = JRoute::_('index.php?option=com_trombinoscopeextended&view=trombinoscope&Itemid='.$tags_view_id.'&tag='.$tag->id);
												}
												
												echo $layout->render($data);
											?>												
										<?php endforeach; ?>
									</div>
								<?php endif; ?>	
							<?php endif; ?>	
							
							<?php							
								$requested_infos = modTrombinoscopeHelper::getRequestedInfos($params, $item);								
								$name_infos = modTrombinoscopeHelper::getRequestedName($params, $item);
								
								if (empty($link_label) && !empty($link)) {
									modTrombinoscopeHelper::setRequestedNameElement($name_infos, 'name', 'name_link'); // $name_infos['name'] = 'name_link';
									modTrombinoscopeHelper::setRequestedNameElement($name_infos, 'value', '<a'.$extra_attributes.' href="'.$link.'"'.modTrombinoscopeHelper::getTitleAttribute($formatted_name, $name_tooltip).'><span>'.$formatted_name.'</span></a>');
								} else {
									modTrombinoscopeHelper::setRequestedNameElement($name_infos, 'name', 'name');
									modTrombinoscopeHelper::setRequestedNameElement($name_infos, 'value', $formatted_name);
								}
								
								echo modTrombinoscopeHelper::getRequestedNameOutput($name_infos, $params, $item, $extraclassesfields);
								echo modTrombinoscopeHelper::getRequestedInfosOutput($requested_infos, $params, $item, $extraclassesfields);
							?>
																					
							<?php
								$show_go = false;
								if (!empty($link_label) && !empty($link)) {
									$show_go = true;
								}
							
								$show_edit = false;
								if ($allow_edit && $link_to_edit && JPluginHelper::isEnabled('user', 'editcontactinprofile')) {
									$show_edit = true;
								}
							?>
							
							<?php if ($show_go || $show_edit) : ?>				
								<div class="personlinks">
									<?php if ($show_go) : ?>	
										<div class="personlink go">
											<a<?php echo $extra_attributes; ?> href="<?php echo $link; ?>" title="<?php echo $formatted_name; ?>">
												<?php if ($doc->getDirection() == 'rtl') : ?>
													<i class="icon SYWicon-arrow-left"></i><span><?php echo $link_label; ?></span>
												<?php else : ?>												
													<i class="icon SYWicon-arrow-right"></i><span><?php echo $link_label; ?></span>
												<?php endif; ?>
											</a>
										</div>
									<?php endif; ?>
									
									<?php if ($show_edit) : ?>					
										<div class="personlink edit">
											<a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.$item->user_id);?>" class="hasTooltip" title="<?php echo JText::_('MOD_TROMBINOSCOPE_EDIT_CONTACT'); ?>">
												<?php if (empty($edit_link_label)) : ?>
													<i class="icon SYWicon-pencil"></i><span><?php echo JText::_('MOD_TROMBINOSCOPE_EDIT_CONTACT'); ?></span>
												<?php else : ?>
													<i class="icon SYWicon-pencil"></i><span><?php echo trim($edit_link_label); ?></span>
												<?php endif; ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>			
					</div>
				</div>
			</div>
			
			<?php if ($i == $contactcount && $header) : ?>
				</div>
			<?php endif; ?>
			
		<?php endforeach; ?>
	</div>
	
	<?php if ($show_arrows && ($arrow_prevnext_bottom || $arrow_next_right || $arrow_next_bottom)) : ?>
		<div class="items_pagination bottom<?php echo empty($extra_pagination_classes) ? '' : ' '.$extra_pagination_classes; ?>">
			<ul>
			<?php if ($arrow_prevnext_bottom) : ?>
				<li><a id="prev_<?php echo $class_suffix; ?>" class="previous" href="#"><i class="SYWicon-arrow-left2"></i></a></li>
				<li><a id="next_<?php echo $class_suffix; ?>" class="next" href="#"><i class="SYWicon-arrow-right2"></i></a></li>
			<?php endif; ?>
			
			<?php if ($arrow_next_right) : ?>
				<li><a id="next_<?php echo $class_suffix; ?>" class="next" href="#"><i class="SYWicon-arrow-right2"></i></a></li>
			<?php endif; ?>
			
			<?php if ($arrow_next_bottom) : ?>
				<li><a id="next_<?php echo $class_suffix; ?>" class="next" href="#"><i class="SYWicon-arrow-down2"></i></a></li>
			<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<?php if ($module_link) : ?>
		<div class="module_link">
			<a href="<?php echo $module_link; ?>" class="hasTooltip<?php echo empty($module_link_class) ? '' : ' '.$module_link_class; ?>" title="<?php echo $module_link_label ?>"<?php echo $module_link_isExternal ? ' target="_blank"' : ''; ?>><span><?php echo $module_link_label ?></span></a>
		</div>
	<?php endif; ?>
	
	<?php if (trim($params->get('posttext', ''))) : ?>
		<div class="posttext">			
			<?php 				
				if ($params->get('allow_plugins_prepost', 0)) {
					echo JHTML::_('content.prepare', $params->get('posttext'));
				} else {
					echo $params->get('posttext');
				}
			?>
		</div>
	<?php endif; ?>
</div>

<?php if ($remove_whitespaces) : ?>
	<?php ob_get_flush(); ?>
<?php endif; ?>