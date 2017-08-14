function showConcertsToApprove( concerts ) {
	var source   = $("#date-template").html();
	var template = Handlebars.compile(source);
	var context = concerts;
	var html    = template(context);
	$('.content').prepend(html);
	$('.no-more').html('No More Unchecked Events...');
	setVerifyStates();
	refreshStats();
	$('.filter-unchecked').click();
}

function clickAction ( eventID, state ) {
	window.localStorage.setItem(eventID, state);
	$('#action-' + eventID + ' .fake').removeClass('fake-tag');
	$('#action-' + eventID + ' .real').removeClass('real-tag');
	$('#action-' + eventID + ' .' + state).addClass(state + '-tag');
	refreshStats();

	var action = $('.filter .active').attr('data-action');
	showHideEvents(action);

}

function setVerifyStates () {
	$.each(window.localStorage, function(key, value){
		clickAction ( key, value );
	});
}

function refreshStats() {
	
	var noTicket = parseInt($('.date').length) - parseInt($('.ticket-link').length);
	var toCheck = parseInt($('.date').length) - parseInt(($('.real-tag').length) + parseInt($('.fake-tag').length));
	$('.stat-created span').html($('.date').length);
	$('.stat-check span').html(toCheck);
	$('.stat-questionable span').html($('.fake-tag').length);
	$('.stat-noticket span').html(noTicket);

}

function showHideEvents(action){

	if (action == 'unchecked') {
		$('.date-container').each(function(){
			console.log($(this).find('.real-tag').length);
			if (($(this).find('.real-tag').length || $(this).find('.fake-tag').length)) {
				$(this).addClass('hide');
			} else {
				$(this).removeClass('hide');
			}
		});
	} else if (action == 'questionable') {
		$('.date-container').each(function(){
			if (($(this).find('.fake-tag').length)) {
				$(this).removeClass('hide');
			} else {
				$(this).addClass('hide');
			}
		});
	} else if (action == 'good') {
		$('.date-container').each(function(){
			if (($(this).find('.real-tag').length)) {
				$(this).removeClass('hide');
			} else {
				$(this).addClass('hide');
			}
		});
	} else if (action == 'no-ticket') {
		$('.date-container').each(function(){
			if (($(this).find('.ticket-link').length)) {
				$(this).addClass('hide');
			} else {
				$(this).removeClass('hide');
			}
		});
	} else {
		$('.date-container').removeClass('hide');
	}
}

$(document).ready(function(){

	$.ajax({
	  url: "http://localhost/new-popular-events/server/check-popular.php",
	}).done(function( data ) {

	  showConcertsToApprove(data);

	}).fail(function(jqXHR, textStatus, errorThrown ) {
	  console.log(textStatus + '-' + errorThrown );
	  $('.content').html('Error! Check your VPN fool!');
	});

	$(document).on('click', '.verify', function(e){
		e.preventDefault();
		var eventID = $(this).parent().attr('data-event');
		var state = $(this).attr('data-action');
		clickAction ( eventID, state );
	});

	$(document).on('click', '.filter a', function(e){
		e.preventDefault();
		$(this).parent().children('a').removeClass('active');
		$(this).addClass('active')

		var action = $(this).attr('data-action')

		showHideEvents(action);

	});

});

