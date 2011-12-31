			</div>
			<div id="column">
				<div class="block"><h2>Block</h2><p>lorem ipsum</p></div>
			</div>
			<?php if (!empty($blocks['column'])): ?>
				<div id="column">
					<?php foreach ($blocks['column'] as $_block): ?>
						<div class="block">
							<?php echo $_block; ?>
						</div>
					<?php endforeach ?>
				</div>
			<?php endif ?>
			<div class="clear"></div>
		</div>
		<div id="footer" class="main-box">
			<?php if (!empty($blocks['footer'])): ?>
				<?php foreach ($blocks['footer'] as $_block): ?>
					<div class="block">
						<?php echo $_block; ?>
					</div>
				<?php endforeach ?>
				<div class="clear"></div>
			<?php endif ?>
		</div>	
	</div>
	<?php include_source('debug.php'); ?>
	<?php Hook::call(HOOK_HTML_BODY_END); ?>
</body>
</html>