$.fn.textWidth = function() {
	var node, original, width;
	original = $(this).html();
	node = $("<span style='position:absolute;width:auto;left:-9999px'>" + original + "</span>");
	node.css('font-family', $(this).css('font-family')).css('font-size', $(this).css('font-size'));
	$('body').append(node);
	width = node.width();
	node.remove();
	return width;
};

$(function() {
	$('.table-ellipsis td').each(function() {
		var content;
		content = $(this).text().replace(/\s+/gi, ' ');
		if ($(this).textWidth() > $(this).width()) {
			return $(this).attr('data-content', content).popover({
				container: 'body',
				placement: 'left',
				trigger: 'hover',
				html: true
			});
		}
	});
});
