jQuery(document).ready(function($) {
	var count = <?php echo $_GET['count']; ?>;
	$add = $("<a/>")
			.attr("id", "add_words_inch")
			.attr("href", "#")
			.text("+")
			.click(function() { addColumn(this); return false; });
		
	// Bind remove onclicks from output table above:
	$(".remove_row").click(function() { $(this).parent().parent().remove(); return false; });
			
	$input = $("input[name='<?php echo $_GET['option']; ?>[words_inch][1][count]']");
	$input.after($add).after(" ");
	$tr = $input.parent().parent().parent().children(":last");
	
	function addColumn(from) {
		$newRow = $("<tr/>")
			.attr("valign", "top")
			.append($("<th/>")
					.attr("scope", "row"))
			.append($("<td/>")
					.append("Name: ")
					.append($("<input/>")
						.attr("type", "text")
						.attr("autocomplete", "off")
						.attr("name", "<?php echo $_GET['option']; ?>[words_inch][" + count + "][name]")
						)
					.append(" Words per column inch: ")
					.append($("<input/>")
						.attr("type", "text")
						.attr("autocomplete", "off")
						.attr("name", "<?php echo $_GET['option']; ?>[words_inch][" + count + "][count]")
						.addClass("small-text")
						)
					.append(" ")
					.append($("<a/>")
						.attr("id", "add_words_inch")
						.attr("href", "#")
						.text("-")
						.click(function() { $(this).parent().parent().remove(); return false; })
						)
					)
			;
		count++;
		
		$tr.before($newRow);
	}
});
