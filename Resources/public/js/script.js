$(document).ready(function() {
	var card = $('.card');

	// Rotate card on qr-code icon click, or whole back surface click
	card.find('#rotate-handle, .back').click(function(e) {
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
