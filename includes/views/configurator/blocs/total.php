<div id="mmmb_total" class="block">
	<p>
		<?php p('Price') ?>
		<span id="mmmb_price"><?php echo $summary['price']; ?></span>
		&euro;
	</p>
	<p>
		<?php p('Squared meter price') ?>
		<span id="mmmb_squared_meter_price"><?php echo intval($summary['price']/$summary['surface']); ?></span>
		&euro;/m<sup>2</sup>
	</p>
</div>