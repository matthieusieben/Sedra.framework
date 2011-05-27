<?php Load::helper('configurator'); ?>
<?php Load::helper('html'); ?>
<?php require 'views/head.php' ?>
<?php css('configurator.css'); ?>
<?php require 'js.php' ?>
<?php require 'views/body.php' ?>

<div id="mmmb_configurator">
	<ul id="mmmb_step_selector">
		<?php $i = 1; ?>
		<?php foreach ($form as $section): ?>
			<?php $_id = 'section_' . $section['id']; ?>
			<li class="<?php echo $_id; ?>">
				<h2>
					<a href="#<?php echo $_id; ?>">
						<span class="step_num"><?php echo $i++; ?></span>
						<span class="step_title"><?php echo translate_field($section); ?></span>
					</a>
				</h2>
				<div>
					<?php if ($_d = translate_field($section, 'description')): ?>
						<p><?php echo $_d; ?></p>
					<?php endif ?>
				</div>
			</li>
		<?php endforeach ?>
		<li class="check_out">
			<h2>
				<a href="#check_out">
					<span class="step_num"><?php echo $i++; ?></span>
					<span class="step_title"><?php p('Check out'); ?></span>
				</a>
			</h2>
			<div>
				<p>Check out description. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
		</li>
	</ul>
	<form method="post" action="<?php echo Url::make('configurator'); ?>">
		<ul id="mmmb_sections">
			<?php foreach ($form as $section): ?>
				<li id="section_<?php echo $section['id'] ?>" class="section">
					<h2><?php echo translate_field($section); ?></h2>
					<div class="step_content">
						<ul class="categories">
							<?php foreach ($section['categories'] as $category): ?>
								<li id="category_<?php echo $category['id'] ?>" class="category">
									<h3><?php echo translate_field($category); ?></h3>
									<?php if ($_g = translate_field($category, 'guidance')): ?>
										<p class="guidance">
											<?php echo $_g; ?>
										</p>
									<?php endif ?>
									<ul class="items">
										<?php foreach ($category['items'] as $item): ?>
											<li class="item">
												<?php $_id = 'item_'.$item['iid'] ?>
												<p>
												<?php if ($item['group_index']): ?>
													<input type="radio" id="<?php echo $_id; ?>" <?php if($item['selected']) echo 'checked="checked"' ?> name="categories[<?php echo $category['id'] ?>][<?php echo $item['group_index'] ?>]" value="<?php echo $item['iid'] ?>" />
												<?php else: ?>
													<input type="checkbox" id="<?php echo $_id; ?>" <?php if($item['selected']) echo 'checked="checked"' ?> name="categories[<?php echo $category['id'] ?>][NULL][]" value="<?php echo $item['iid'] ?>" />
												<?php endif ?>
													<label for="<?php echo $_id; ?>">
														<?php echo translate_field($item); ?>
													</label>
												</p>
												<?php if ($_d = translate_field($item, 'description')): ?>
													<p class="description"><?php echo $_d; ?></p>
												<?php endif ?>
											</li>
										<?php endforeach ?>
									</ul>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
					<div class="guidance_container"></div>
					<div class="clear"></div>
				</li>
			<?php endforeach ?>
			<li id="check_out" class="section">
				<h2><?php p('Last step'); ?></h2>
				<div id="mmmb_xsrf_token" class="hidden"><?php echo $xsrf_token; ?></div>
				<input type="hidden" name="save_config" value="1" />
				<input type="submit" name="save" value="<?php p('Save') ?>" />
			</li>
		</ul>
	</form>
</div>

<?php require 'views/foot.php' ?>