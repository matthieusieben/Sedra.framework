<?php Load::helper('configurator'); ?>


<div id="mmmb_details" class="block">
	<ul id="mmmb_details_sections" class="sections">
		<?php foreach ($form as $section): ?>
			<li class="section">
				<h2><?php echo translate_field($section); ?></h2>
				<ul class="items">
					<?php foreach ($section['categories'] as $category): ?>
						<?php foreach ($category['items'] as $item): ?>
							<?php $_id = 'item_'.$item['iid'] ?>
							<li class="item <?php echo $_id; ?>"<?php if(!$item['selected']) echo ' style="display:none";'; ?>>
								<?php echo translate_field($item); ?>
							</li>
						<?php endforeach ?>
					<?php endforeach ?>
				</ul>
			</li>
		<?php endforeach ?>
	</ul>
</div>