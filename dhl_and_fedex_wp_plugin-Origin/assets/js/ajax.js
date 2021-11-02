jQuery(document).ready(function ($) {

      $.ajax({
            url: "http://dhlfedexplugin.bitsclansolutions.com/wp-json/dfp/v1/countries?token=8adcf977d016e1ed5d641843fd98b8b3631d9843",
            type: "GET",
            success: function (response) {

                console.log(response.body_response.data);
              $('#Source option').remove();
                $('#Source').append($('<option>').text('Select Source').attr('value', 0));
                $.each(response.body_response.data, function (i, value) {
                    $('#Source').append($('<option>').text(value.name).attr('value', value.id));
                });
				$('#Destination option').remove();
                $('#Destination').append($('<option>').text('Select Destination').attr('value', 0));
                $.each(response.body_response.data, function (i, value) {
                    $('#Destination').append($('<option>').text(value.name).attr('value', value.id));
                });
			}


        });
    $('#Source').on('change', function () {
		$.ajax({
            url: "http://dhlfedexplugin.bitsclansolutions.com/wp-json/dfp/v1/states?token=8adcf977d016e1ed5d641843fd98b8b3631d9843&country_id="+$(this).val(),
            type: "GET",
            success: function (response) {

                console.log(response.body_response.data);
              $('#states option').remove();
                $('#states').append($('<option>').text('Select State').attr('value', 0));
                $.each(response.body_response.data, function (i, value) {
                    $('#states').append($('<option>').text(value.name).attr('value', value.id));
                });
			}


        });

    });

    $('#states').on('change', function () {
		$.ajax({
            url: "http://dhlfedexplugin.bitsclansolutions.com/wp-json/dfp/v1/cities?token=8adcf977d016e1ed5d641843fd98b8b3631d9843&state_id="+$(this).val(),
            type: "GET",
            success: function (response) {
                console.log(response.body_response.data);
              $('#cities option').remove();
                $('#cities').append($('<option>').text('Select City').attr('value', 0));
                $.each(response.body_response.data, function (i, value) {
                    $('#cities').append($('<option>').text(value.name).attr('value', value.id));
                });
			}


        });

    });
//---------------------------------------------------------------------------------
// Destination
//---------------------------------------------------------------------------------
    $('#Destination').on('change', function () {
		$.ajax({
            url: "http://dhlfedexplugin.bitsclansolutions.com/wp-json/dfp/v1/states?token=8adcf977d016e1ed5d641843fd98b8b3631d9843&country_id="+$(this).val(),
            type: "GET",
            success: function (response) {

                console.log(response.body_response.data);
              $('#ship_states option').remove();
                $('#ship_states').append($('<option>').text('Select State').attr('value', 0));
                $.each(response.body_response.data, function (i, value) {
                    $('#ship_states').append($('<option>').text(value.name).attr('value', value.id));
                });
			}


        });

    });

//-----------------------------------------------------------------------------------
    $('#ship_states').on('change', function () {
		$.ajax({
            url: "http://dhlfedexplugin.bitsclansolutions.com/wp-json/dfp/v1/cities?token=8adcf977d016e1ed5d641843fd98b8b3631d9843&state_id="+$(this).val(),
            type: "GET",
            success: function (response) {

                console.log(response.body_response.data);
              $('#ship_cities option').remove();
                $('#ship_cities').append($('<option>').text('Select City').attr('value', 0));
                $.each(response.body_response.data, function (i, value) {
                    $('#ship_cities').append($('<option>').text(value.name).attr('value', value.id));
                });
			}


        });

    });
//-----------------------------------------------------------------------------------
    $('#Rates').click(function () {

        var warehouse = $("#Source").val();
        var states = $("#states").val();
        var cities = $("#cities").val();
        var country = $("#Destination").val();
        var ship_states = $("#ship_states").val();
        var ship_postal = $("#ship_postal").val();
        var postal = $("#postal").val();
        var ship_cities = $("#ship_cities").val();
        var weight = $("#Weight").val();
        var length = $("#Length").val();
        var width = $("#Width").val();
        var height = $("#Height").val();
        /*-------------- Shipping form --------------------
        ---------------- Developer: Sohaib
        ---------------- Changes: display error message on empty inputs, show loading indicator
        ---------------- Changed files: shipping/assets/js; shipping/shipShortcode.php
        */
       if (warehouse == 0 || states == 0 || cities == 0 || country == 0 || ship_states == 0 || ship_cities == 0 || weight == 0 || length == 0 || width ==0 || height == 0) {
        var errorMessage = document.getElementById("error_message");
        errorMessage.style = 'display: block !important';
        return;
    }
      $.ajax({
        url: "http://dhlfedexplugin.bitsclansolutions.com/wp-json/dfp/v1/getpostaldetails?token=8adcf977d016e1ed5d641843fd98b8b3631d9843",
        type: "POST",
        data: {
            source_country_id: warehouse,
            source_state_id: states,
            source_city_id: cities,
            destination_country_id: country,
            destination_state_id: ship_states,
            destination_city_id: ship_cities,
            
        },
        success: function (response) {
    $.ajax({
        url: wpship_ajax_url.ajax_url,
        type: "POST",
        data: {
            source_city: response.body_response.data.source.city,
            source_country: response.body_response.data.source.country,
            source_iso2: response.body_response.data.source.iso2,
            source_postalcode: response.body_response.data.source.postal_code,
            destination_city: response.body_response.data.destination.city,
            destination_country: response.body_response.data.destination.country,
            destination_iso2: response.body_response.data.destination.iso2,
            destination_postalcode: response.body_response.data.destination.postal_code,
            length: length,
            width: width,
            height: height,
            weight: weight,
            action: 'my_action',
        },
        success: function (response) {
            $("#loader").addClass("hidden");
            $('.renderDiv').html(JSON.parse(response));
            $(".renderDiv").slideDown();

        },
        error: function() {
            $("#loader").addClass("hidden");
            $("#response_error").removeClass("hidden");
        }

    });

            },
             beforeSend: function() {
            $("#shipping_rate_form").slideUp();
            $("#loader").removeClass("hidden");
        },


        });
    });




});

function showTemplate(template) {
    console.log(template);
    jQuery(".renderDiv").html(template);
    jQuery(".renderDiv").slideDown();


}
