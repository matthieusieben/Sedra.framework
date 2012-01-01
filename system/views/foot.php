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
		<?php if (!empty($blocks['footer'])): ?>
			<div id="footer" class="main-box">
				<?php foreach ($blocks['footer'] as $_block): ?>
					<div class="black block">
						<?php echo $_block; ?>
					</div>
				<?php endforeach ?>
				<div class="clear"></div>
			</div>
		<?php endif ?>
	</div>
	<?php Hook::call(HOOK_HTML_BODY_END); ?>
</body>
</html>