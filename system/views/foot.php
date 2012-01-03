			</div>
		</div>
		<div id="footer" class="main-box">
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