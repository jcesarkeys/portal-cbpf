<?php
/**
* @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

$tag = $displayData['tag'];
$link = isset($displayData['link']) ? $displayData['link'] : '';
$onclick = isset($displayData['onclick']) ? $displayData['onclick'] : '';

$tagParams = new JRegistry($tag->params);
$tag_class = $tagParams->get('tag_link_class', 'label label-info');
?>

<span class="tag tag-<?php echo $tag->id; ?> <?php echo $tag_class; ?>">	
	<?php if ($link) : ?>									
		<a href="<?php echo $link; ?>"><span><?php echo $this->escape($tag->title); ?></span></a>	
	<?php elseif ($onclick) : ?>
		<a href="" onclick="<?php echo $onclick; ?>"><span><?php echo $this->escape($tag->title); ?></span></a>
	<?php else : ?>
		<span><?php echo $this->escape($tag->title); ?></span>
	<?php endif; ?>
</span>