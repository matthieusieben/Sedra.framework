<?php $attributes = (array) @$attributes; ?>
<?php attributes_setup($attributes); ?>
<?php $attributes['class'][] = 'table'; ?>
<?php if(!empty($header) || !empty($rows) || !empty($footer)): ?>
	<table<?php echo attributes($attributes); ?>>

		<?php if(!empty($header)): ?>
			<thead>
				<tr>
					<?php foreach($header as $_value): ?>
						<?php if (is_string($_value)): ?>
							<th>
								<?php echo $_value; ?>
							</th>
						<?php else: ?>
							<th>
								<?php echo theme($_value); ?>
							</th>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			</thead>
		<?php endif; ?>

		<?php if(!empty($rows)): ?>
			<tbody>
				<?php foreach($rows as $_row): ?>
					<tr>
						<?php foreach($_row as $_value): ?>
							<?php if (is_string($_value)): ?>
								<td>
									<?php echo $_value; ?>
								</td>
							<?php else: ?>
								<td>
									<?php echo theme($_value); ?>
								</td>
							<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		<?php endif; ?>

		<?php if(!empty($footer)): ?>
			<tfoot>
				<tr>
					<?php foreach($footer as $_value): ?>
						<td><?php echo $_value; ?></td>
					<?php endforeach; ?>
				</tr>
			</tfoot>
		<?php endif; ?>

	</table>
<?php endif; ?>