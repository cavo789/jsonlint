<?php

declare(strict_types=1);

/**
 * AUTHOR : AVONTURE Christophe
 *
 * Written date : 6 october 2018
 *
 * JSON lint
 * A very simple JSON lint interface, no server-side processing, everything
 * done by Javascript
 *
 * @link https://stackoverflow.com/a/7220510/1065340 for the color output
 *
 * Get the code on GitHub: https://github.com/cavo789/jsonlint
 *
 */

 // Sample content
$json = '{"name":"John","age":30,"cars":["Ford","BMW","Fiat"],' .
	'"places":["Africa","America","Asia","Australia"]}';

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="author" content="Christophe Avonture" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8;" />
		<title>JSONLint & Pretty print</title>
		<style>
			.string { color: green; }
			.number { color: darkorange; }
			.boolean { color: blue; }
			.null { color: magenta; }
			.key { color: red; }
			#ErrorMessage { color : red; padding-bottom: 10px; }
		</style>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	</head>
	<body>
		<div class="container">
			<div class="page-header"><h1>JSONLint & Pretty print</h1></div>
			<div class="container">
				<div class="form-group">
					<label for="json">Copy/Paste your JSON content in the textbox below then click on the Lint button:</label>
					<textarea class="form-control" rows="5" id="json" name="json"><?php echo $json; ?></textarea>
				</div>
				<button data-eval="0" type="button" id="btnLint" class="btn btn-primary">Lint</button>
				<hr/>
				<div id="Error" style="display:none;">
					<div id="ErrorMessage"></div>
					<div>Your JSON isn't valid (perhaps a double-quote is missing). 
					Click on "Lint but first eval()" to first ask javascript to evaluate the 
					string before. Please keep in mind that if the content is not a JSON but 
					a command, evaluating the command can be dangerous if your browser allow 
					the instruction to be fired (like f.i. "alert('GOTTA!');")</div>
				</div>
				<pre id="Result" class="language-json"></pre>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
		<script type="text/javascript">

			//https://stackoverflow.com/a/7220510/1065340
			function syntaxHighlight(json) {
				json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
				return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
					var cls = 'number';
					if (/^"/.test(match)) {
							if (/:$/.test(match)) {
								cls = 'key';
							} else {
								cls = 'string';
							}
					} else if (/true|false/.test(match)) {
							cls = 'boolean';
					} else if (/null/.test(match)) {
							cls = 'null';
					}
					return '<span class="' + cls + '">' + match + '</span>';
				});
			}

			// Remember the old submitted text
			var oldJSON = "";

			// When the JSON has been modified, allow to try linting again
			$("#json").on('change', function() {
				var $json = $(this).val();
				if($json == oldJSON) {
					return; 
				}

				oldJSON = $json;
				
				$('#Result').html('');

				// Enable the button back
				$('#btnLint').prop("disabled", false);

				$('#Error').hide();

				$('#btnLint')
					.text('Lint')
					.data('eval', 0)
					.addClass('btn-primary')
					.removeClass('btn-warning')
					.removeClass('btn-danger');
			});

			$('#btnLint').click(function(e)  {

				// Remember the current string
				oldJSON = $json;

				$('#Error').hide();

				// data-eval will be set to 1 if, first, an eval() should be 
				// executed on the JSON string for trying to solve a syntax error
				// { user :  <-- IS INVALID
				// { "user" :  <-- IS WELL VALID.   eval() will solve this					
				var $bEvalFirst = $(this).data('eval');

				// Get the JSON string
				var $json = $('#json').val();

				// And convert as an array
				try {
					var $JSON = JSON.parse($json);// And now, display
					$('#Result').html(syntaxHighlight(JSON.stringify($JSON, undefined, 2)));	
				} catch (error) {

					if ($bEvalFirst == 0) {
						// An error has occurred with, "just" a lint, suggest eval then lint
						$('#btnLint')
							.text('Lint but first eval()')
							.data('eval', 1)
							.addClass('btn-warning')
							.removeClass('btn-primary');

						$('#ErrorMessage').html(error);
						$('#Error').show();

					} else if ($bEvalFirst == 1) {

						// Try eval() first

						try {
							// Try to beautify the string. If still incorrect, an error will occurs							
							var $JSON = JSON.stringify(eval('('+$json+')'), undefined, 2);
							
							// The string is now valid, get the string it once more
							// but not "prettyfied" and reset the input textbox
							$('#json').val(JSON.stringify(eval('('+$json+')')));

							// Reset the button "Lint" with data-eval=0 and btn-primary back
							$('#btnLint')
								.text('Lint')
								.data('eval', 0)
								.addClass('btn-primary')
								.removeClass('btn-warning');

							// Output the result
							$('#Result').html(syntaxHighlight($JSON));	
						} catch (error) {
							$('#ErrorMessage').html(error);
							$('#Error').show();
							
							$('#btnLint')
								.text('INVALID JSON, PLEASE MODIFY YOUR PROPOSAL')
								.data('eval', 2)
								.addClass('btn-danger')
								.removeClass('btn-warning')
								.prop("disabled", true);
						}
					} else {
						$('#ErrorMessage').html('Please solve your issue and try again');
						$('#Error').show();
					}
				}
			});
		</script>
	</body>
</html>
