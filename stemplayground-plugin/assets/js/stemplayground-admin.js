jQuery(document).ready(function($) {
	$( "select#uc_post_type" ).change( function() {
		var uc_selected = $(this).val();
		$('.uc_display').each( function() {
			if ( $(this).hasClass( "display-"+uc_selected ) )
				$(this).show();
			else
				$(this).hide();

		});
	});

	$( "select.uc-change" ).change( function() {
		var wpnonce = $('#get_reports_noncename').val();
		var data = {
			'action':'get_geography',
			'_wpnonce' : wpnonce,
			'b' : $(this).find(":selected").val(),
			'd' : $(this).attr('data-update'),
		};
		var updated = $(this).attr('data-update');
		$( "select.uc-change.update-" + updated ).each( function() {
			var updated_select = $(this);
			data['a'] = $(updated_select).attr('data-update');

			$.post(ajaxurl, data, function(response) {
				var parsed_response = $.parseJSON( response );
				$(updated_select).empty().append('<option value="" selected></option>');
				if ( parsed_response['results'] ) {
					var i = 0;
					var options = '';
					while ( i < parsed_response['results'].length ) {
						options += '<option value="'+parsed_response['results'][i]['value']+'">'+parsed_response['results'][i]['label']+'</option>';
						i++;
					}
					$(updated_select).append(options);
				} else {
				//	$(updated_select).empty().append('<option value="" disabled="" selected=""></option>').change();
				}
			});
		});
	});
	
	$('.report-search').click(function(e) {
		e.preventDefault();
		var wpnonce = $('#get_reports_noncename').val();
		var action = $("input[name='action']" ).attr('value');
		var a = $("select[name='uc_post_type'] option:selected" ).val();
		var data = {
			'action': action,
			'_wpnonce' : wpnonce,
			'a' : a,
		};
		data['country'] = $("select[name='country'] option:selected" ).val();
		data['state'] = $("select[name='state'] option:selected" ).val();
		data['school_year'] = $("select[name='school_year'] option:selected" ).val();
		var city_county_search_selected = $("input[type='radio'][name='city_county_search']:checked").val();
		if ( city_county_search_selected == 'city')
			data['city'] = $("select[name='city'] option:selected" ).val();
		else 
			data['county'] = $("select[name='county'] option:selected" ).val();

		switch(a) {
			case 'uc-school':
				data['majority_at_risk'] = $("input[type='checkbox'][name='majority_at_risk'").is(":checked") ? 1 : 0;
				data['majority_esl'] = $("input[type='checkbox'][name='majority_esl'").is(":checked") ? 1 : 0;
				break;
			case 'uc-teacher':
				data['grade'] = $("select[name='grade'] option:selected" ).val();
				data['gender'] = $("select[name='gender'] option:selected" ).val();
				data['stem_proficient'] = $("input[type='checkbox'][name='stem_proficient'").is(":checked") ? 1 : 0;
				data['stem_major'] = $("input[type='checkbox'][name='stem_major'").is(":checked") ? 1 : 0;
				data['stem_ambassador'] = $("input[type='checkbox'][name='stem_ambassador'").is(":checked") ? 1 : 0;
				data['in_majority_at_risk'] = $("input[type='checkbox'][name='in_majority_at_risk'").is(":checked") ? 1 : 0;
				data['in_majority_esl'] = $("input[type='checkbox'][name='in_majority_esl'").is(":checked") ? 1 : 0;
				break;
			case 'uc-class':
				data['grade'] = $("select[name='grade'] option:selected" ).val();
				data['in_majority_at_risk'] = $("input[type='checkbox'][name='in_majority_at_risk'").is(":checked") ? 1 : 0;
				data['in_majority_esl'] = $("input[type='checkbox'][name='in_majority_esl'").is(":checked") ? 1 : 0;
				break;
			case 'uc-group':
				data['grade'] = $("select[name='grade'] option:selected" ).val();
				data['in_majority_at_risk'] = $("input[type='checkbox'][name='in_majority_at_risk'").is(":checked") ? 1 : 0;
				data['in_majority_esl'] = $("input[type='checkbox'][name='in_majority_esl'").is(":checked") ? 1 : 0;
				break;
			case 'uc-student':
				data['grade'] = $("select[name='grade'] option:selected" ).val();
				data['gender'] = $("select[name='gender'] option:selected" ).val();
				data['esl_status'] = $("input[type='checkbox'][name='esl_status'").is(":checked") ? 1 : 0;
				break;
			default:
				break;
		}

		console.log(data);
		$.post(ajaxurl, data, function(response) {
		//	console.log(response);
			var parsed_response = $.parseJSON( response );
			console.log(parsed_response);
			if ( parsed_response['status'] == 'OK' ) {
				var output = '<p>Found '+parsed_response['rows'].length+' results:</p>';
				output += '<table class="widefat striped"><thead><tr>';
				for (var i = 0; i < parsed_response['columns'].length; i++) {
					output += '<th>'+parsed_response['columns'][i]+'</th>';
				}
				var csv_data = [parsed_response['columns']];
				output += '</tr></thead><tbody>';
				if ( typeof parsed_response['rows'] !== 'undefined' ) {
					for (var i = 0; i < parsed_response['rows'].length; i++) {
						output += '<tr>';
						csv_data_row = new Array();
						for (var j = 0; j < parsed_response['rows'][i].length; j++) {
							if ( parsed_response['rows'][i][j]['text'] == null )
								var output_text = 'null';
							else
								var output_text = parsed_response['rows'][i][j]['text'].toString();

							if ( typeof parsed_response['rows'][i][j]['link'] !== 'undefined' )
								outputcell = '<a href="'+parsed_response['rows'][i][j]['link']+'">'+output_text+'</a>';
							else
								outputcell = output_text;
							output += '<th>'+outputcell+'</th>';
							csv_data_row.push( output_text.indexOf(",") ? '"'+parsed_response['rows'][i][j]['text']+'"' : parsed_response['rows'][i][j]['text']);
						}
						output += '</tr>';
						csv_data.push(csv_data_row);
					}
				}
				output += '</tbody></table>';

				$('.search-results').empty().append(output);

				var csvContent = "data:text/csv;charset=utf-8,";
				csv_data.forEach(function(infoArray, index){

					dataString = infoArray.join(",");
					csvContent += index < csv_data.length ? dataString+ "\n" : dataString;

				}); 
				var encodedUri = encodeURI(csvContent);
				var link = document.createElement("a");
				var link_container = document.createElement("p");
				link_container.setAttribute('class', 'submit');
				var linkText = document.createTextNode("Click to download CSV");
				link.appendChild(linkText);
				link.setAttribute('class', 'button button-secondary');
				var location = document.getElementById("wpbody-content");
				location = location.getElementsByClassName("search-results");
				if (typeof link.download != "undefined") {
					link.setAttribute("href", encodedUri);
					link.setAttribute("download", "stem_playground_report.csv");
					link_container.appendChild(link);
					location[0].appendChild(link_container);
				}

			} else {

			}
		});
	});
});