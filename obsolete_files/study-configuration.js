$(function(){
	checkAutoCondition(); //Check initial state of auto condition
	$('#submitbtn').click(getJSON);
	$('#autoconditionid').click(checkAutoCondition); //Checks to see if the auto condition is set
});

//Query's register_config.php to get JSON data
function getJSON () {
	$.ajax('register_config.php', {
		type: 'POST',
		data: {
			'autoconditionid': $('#autoconditionid').val(),
			'conditionId': $('#conditionId').val(),
			'cssURL': $('#cssURL').val(),
			'taskBarCSS': $('#taskBarCSS').val(),
			'buttonText': $('#buttonText').val(),
			'returnURL': $('#returnURL').val(),
			'taskText': $('#taskText').val(),
			'taskHTML': $('#taskHTML').val(),
			'tabShowText': $('#tabShowText').val(),
			'tabHideText': $('#tabHideText').val()
		},
		success: jsonSuccess
	});
}
/*  Determins weather the auto condition id is set and disables the ID and css fields in the form
 *	if the condition box is checked. If box is unchecked all fields will be available
 *
 */
function checkAutoCondition (argument) {
	// body...
	var condition = $('#conditionId');
	var css = $('#cssURL');
	if($('#autoconditionid').attr('checked')){
		condition.attr("disabled", "true");
		// commented out the removeAttr lines because 
		// the fields still need data in them (for now)
		//condition.removeAttr("value");
		css.attr("disabled", "true");
		//css.removeAttr("value");
	} else {
		condition.removeAttr('disabled');
		css.removeAttr('disabled');
	}

}

function jsonSuccess (response) {
	// body...
	alert('configuration created');
}
