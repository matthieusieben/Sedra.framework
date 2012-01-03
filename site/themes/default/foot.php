			</div>
			<?php $_column = Blocks::get('column'); ?>
			<?php if (!empty($_column)): ?>
				<div id="column">
					<?php foreach ($_column as $_block): ?>
						<div class="block">
							<?php echo Blocks::generate($_block); ?>
						</div>
					<?php endforeach ?>
				</div>
			<?php endif ?>
			<div class="clear"></div>
		</div>
		<div id="footer" class="main-box">
			<?php $_footer = Blocks::get('footer'); ?>
			<?php if (!empty($_footer)): ?>
				<?php foreach ($_footer as $_block): ?>
					<div class="black block">
						<?php echo Blocks::generate($_block); ?>
					</div>
				<?php endforeach ?>
			<?php endif ?>
			<div class="clear"></div>
			<div id="footnote">
				<span id="copyright">Copyright &copy; <?php date('Y'); ?></span>
				<?php if (!empty($footnote)): ?>
					<span> - <?php echo $footnote; ?></span>
				<?php endif ?>
			</div>
		</div>
	</div>
	<?php Hook::call('html_body_end'); ?>
</body>
</html>