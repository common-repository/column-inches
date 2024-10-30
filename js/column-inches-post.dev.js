// Column inches
// Compress with: http://jscompress.com/
(function($) {
	wpColumnInches = {

		init : function() {
			var t = this, last = 0, co = $('#content');

			jQuery("<td/>")
				.attr("id", "wp-column-inches")
				.html("Column inches: <span id='column-inches'></span>")
				.insertAfter("#wp-word-count");
			
			t.block = 0;
			t.ci(co.val());
			co.keyup( function(e) {
				if ( e.keyCode == last ) return true;
				if ( 13 == e.keyCode || 8 == last || 46 == last ) t.ci(co.val());
				last = e.keyCode;
				return true;
			});
		},

		ci : function(tx) {
			var t = this, w = $('#column-inches'), tc = 0;

			if ( t.block ) return;
			t.block = 1;

			setTimeout( function() {
				var output = '';
				var i = 0;
				var length = columnInchSettings.length;
				var numWords = parseInt(jQuery("#word-count").text());
				for (x in columnInchSettings) {
					columnInch = columnInchSettings[x];
					var inches = Math.ceil(numWords / columnInch['count'] );
					var title = columnInch['name'] + ': ' + inches + ' column inch' + (inches != 1 ? "es" : "");
					output += '<span title="' + title + '" style="border-bottom: 1px dotted #666; cursor: help;">' + inches + '</span>';
					output += (length > 1 && i++ < length - 1) ? ' / ' : ''; // add slashes in the middle to separate column inch counts
				}
				w.html(output);

				setTimeout( function() { t.block = 0; }, 2000 );
			}, 1 );
		}
	}

	$(document).ready( function(){ wpColumnInches.init(); } );
}(jQuery));
