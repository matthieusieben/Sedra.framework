		</section>

		<?php if (!empty($aside)): ?>
			<aside class="sidebar span3">
				<?php if (is_array($aside)): ?>
					<?php echo theme($aside) ?>
				<?php else: ?>
					<?php echo $aside ?>
				<?php endif ?>
			</aside>
		<?php endif ?>

	</div>

	<footer id="footer" role="contentinfo">
		<?php require 'views/copyright.php' ?>
	</footer>
</div>