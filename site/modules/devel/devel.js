$(document).ready(function(){
	$("#devel-queries").find("tr").each(function() {
		var $tr = $(this);
		var $links = $tr.find(".ops > a");
		var $queries = $tr.find(".query > div");

		$links.click(function() {
			$queries.hide();
			$tr.find('.'+$(this).attr("class")).show();
			return false;
		});
	});
});
