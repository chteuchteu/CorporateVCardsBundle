$(document).ready(function() {
	var card = $('.card');

	// Rotate card
	card.click(function(e) {
		// Don't rotate card on link click
		if ($(e.target).is('a'))
			return;

		rotateCard(this);
	});

	// Email modal
	$('[href="#mail-form"]').magnificPopup({
		type: 'inline',
		mainClass: 'mfp-img-mobile',
		preloader: false,
		focus: '#email',
		callbacks: {
			beforeOpen: function() {
				if($(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#email';
				}
			}
		}
	});
});

function rotateCard(btn){
	var $card = $(btn).closest('.card-container');

	if($card.hasClass('hover')){
		$card.removeClass('hover');
	} else {
		$card.addClass('hover');
	}
}
