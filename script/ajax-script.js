$(document).ready(function() {

    $("#name").on('input', function() {
        if($("#name").val().length != 0) {
            $("#send_rsvp_btn").prop('disabled', false);
            $("#send_rsvp_btn").removeClass('disabled');
        }
        else {
            $("#send_rsvp_btn").prop('disabled', true);
            $("#send_rsvp_btn").addClass('disabled');
        }
    });

    $('#send_rsvp_btn').click(function() {

        if($("#name").val().length != 0) {
            $(".loading-background").show();
        
            $.ajax({
                type: 'POST',
                url : myajax.ajaxurl,
                dateType: 'json',
                data: {
                    action: "save_rsvp",
                    name: $('#name').val(),
                    people: $('#people').val(),
                    response: $('input[name="response"]:checked').val(),
                    song: $('#song').val(),
                    message: $('#details').val()
                },
                success: function(response) {
                    $(".loading-background").hide();
                    
                    if(response.toUpperCase().indexOf('ERROR') >= 0) {
                        alert('error!');
                    }
                    else {
                        $(".form-response-box").show();
                        if($('input[name="response"]:checked').val() == 'yes') {
                            $(".yes-response").show();
                            // show 'can't wat to see you' message
                            confetti({
                                particleCount: 100,
                                spread: 70,
                                origin: { y: 0.6 },
                            });
                        }
                        else {
                            $(".no-response").show();
                        }
                        $('#name').val("");
                        $('#people').val("");
                        $('#people').val(2);
                        $('#song').val("");
                        $('#details').val("");
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('error!');
                }
            })
        }
        return false;
    });

    $("#close-modal").click(function() {
        $(".yes-response").hide();
        $(".no-response").hide();
        $(".form-response-box").hide();
    });
})