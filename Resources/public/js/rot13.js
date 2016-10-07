$ = jQuery;
$(document).ready(function() {
	// De-obfuscate rot13-encoded mail addresses
	$('[data-obfuscated]').each(function() {
		// Either content and/or href attribute may be obfuscated:

		// - Has href attribute & begins with "mailto:"
		var href = $(this).attr('href');
		if (!!href && href.substring(0, 'mailto:'.length) == 'mailto:')
			$(this).attr('href', 'mailto:' + deObf(href.substring('mailto:'.length)));

		// - content
		$(this).text(deObf($(this).text()));

		$(this).removeAttr('data-obfuscated');
	});

});

function deObf(string)
{
	return string.replace(/[a-zA-Z]/g,function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);});
}
