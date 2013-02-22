
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?php echo file_url('assets/js/jquery.min.js'); ?>"><\/script>')</script>

	<?php echo theme_js('assets/js/bootstrap.min.js'); ?>

	<?php hook_invoke('html_foot'); ?>

	<?php if($_ga = config('site.ga')): ?>
		<script>
			var _gaq=[['_setAccount','<?php echo $_ga; ?>'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
		</script>
	<?php endif; ?>
</body>
</html>
