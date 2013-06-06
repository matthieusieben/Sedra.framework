
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
