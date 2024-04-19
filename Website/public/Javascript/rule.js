function onRuleCheckboxToggled(event)
{
	let element = event.currentTarget;

	let state = element.checked ? 1 : 0;
	let id = element.getAttribute('rule_id');

	let type = element.getAttribute('rule_type');
	let url = "rule/toggle/" + type;
	console.log(url, state);

	$.ajax({
        url: url, // Path to your household.php file
        type: 'POST', // GET method to fetch data
        data: 'ruleId='+ id + "&state=" + state,
        success: function(response) {
            console.log(response);
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
            console.log(xhr.responseText);
        }
    });
}