			</div>
			<div id="column">
				<?php if (isset($blocks['column'])): ?>
					<?php foreach ($blocks['column'] as $_block): ?>
						<?php echo $_block; ?>
					<?php endforeach ?>
				<?php endif ?>
			</div>
			<div class="clear"></div>
		</div>
		<div id="footer">
			<?php if (!empty($blocks['footer'])): ?>
				<?php foreach ($blocks['footer'] as $_block): ?>
					<?php echo $_block; ?>
				<?php endforeach ?>
				<div class="clear"></div>
			<?php endif ?>
			<?php require 'debug.php' ?>
		</div>
	</div>
</body>
</html>