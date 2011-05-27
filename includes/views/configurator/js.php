<?php js('jquery.qtip.js'); ?>
<?php css('js/jquery.qtip.css'); ?>

<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function($) {
		var INIT = true;
		
		var $steps_selector = $('#mmmb_step_selector'),
			$steps_desc = $steps_selector.find('> li'),
			$sections_container = $('#mmmb_sections').wrap('<div id="mmmb_sections_wrapper"></div>'),
			$mmmb_sections_wrapper = $('#mmmb_sections_wrapper'),
			$sections = $sections_container.find('> li'),
			steps_selector_iwidth = $steps_selector.innerWidth();

		<?php # Setup accordion ?>
		$steps_desc.each(function(i){
			var $this = $(this),
				$links = $this.find('h2 > a'),
				$step_num = $this.find('.step_num'),
				vbanner_width = $step_num.first().outerWidth(true),
				new_width = steps_selector_iwidth - (vbanner_width * $steps_desc.length),
				left_space = vbanner_width * i;

			<?php # Adjust size & position ?>
			$this.css({
				'width': new_width+'px',
				'left': left_space+'px'
			});

			<?php # Step switch when clicking a link ?>
			$links.click(function(){
				var $link = $(this),
					href = $link.attr('href'),
					section_id = href.substring(1);

				$steps_desc.each(function(j){
					var $this = $(this);
					
					if(i==j)	$this.addClass('selected');
					else		$this.removeClass('selected');
					
					if(j<=i) {
						if(INIT)	$this.css({'left':vbanner_width * j});
						else		$this.stop().animate({'left':vbanner_width * j});
					} else {
						if(INIT)	$this.css({'left':steps_selector_iwidth - ($steps_desc.length - j) * vbanner_width});
						else		$this.stop().animate({'left':steps_selector_iwidth - ($steps_desc.length - j) * vbanner_width});
					}
				});
				
				$sections.animate({height: 'hide'}, INIT ? 0 : null);
				$('#' + section_id).stop().animate({height: 'show'}, INIT ? 0 : null);
				
				return false;
			});
		});

		<?php # Simulate click on current step from url hash ?>
		var default_hash = '#'+$sections.first().attr('id'),
			queryhash = window.location.hash.length && $sections_container.find('>' + window.location.hash).length ? window.location.hash : default_hash,
			queryclass = '.' + queryhash.substring(1);
		$('#mmmb_step_selector > ' + queryclass + ' .step_num').first().click();

		<?php # Guidance on section hover ?>
		$('#mmmb_sections > li').each(function () {
			var	$section = $(this),
				$guidance_container = $section.find('.guidance_container'),
				$categories = $section.find('li.category');

			$categories.each(function () {
				var	$category = $(this),
					$guidance = $category.find('.guidance');

				$guidance_container.html($guidance);
				$category.hover(function() {
					$guidance_container.html($guidance);
				});
			});
		});

		<?php # Tooltip ?>
		$('li.item').each(function(i){
			var $this = $(this),
				$desc = $this.find('.description').hide();

			if($desc) {
				$this.find('label').qtip({
					content: {
						title: '<?php p("Details"); ?>',
						text: $desc
					},
					position: {
						my: "bottom left",
						at: "top left"
					},
					style: {
						tip: true,
						classes: "ui-tooltip-green"
					}
				});
			}
		});

		<?php # When clicking a form checkbox ?>
		$('#mmmb_configurator input:checkbox, #mmmb_configurator input:radio').click(function() {
			var form_data = $("#mmmb_configurator form").serialize();
			<?php # Get new values through AJAX ?>
			$.ajax({
			    data: form_data,
			    success: function(data, textStatus) {
					for(i in data) {
						$('#'+i).html(data[i]);
					}
			    },
			    error: function(a) {
					console.log(a);
			        alert("<?php p('Your last action wasn\'t sent to the server. Please be sure to hit the save button when you are done.'); ?>");
			    },
			    dataType: 'json',
			    url: '<?php echo Url::make("configurator/ajax"); ?>',
			    type: 'POST'
			});
			<?php # Update details block ?>
			var	$details = $('#mmmb_details'),
				$items = $details.find('.item'),
				active_items = form_data.split(/&/g);
			$items.hide();
			for (var i = 0; i < active_items.length; i++) {
				var input = active_items[i].split(/=/g);
				if(input[0].substring(0,10) == "categories") {
					item_id = input[1];
					$details.find('.item_'+item_id).show();
				}
			}
		});
		
		INIT = false;
	});
</script>