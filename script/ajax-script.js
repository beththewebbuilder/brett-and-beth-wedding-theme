$(document).ready(function() {

    var responseSelected = false;
    var nameEntered = false;

    const confettiHeartDefaults = {
        spread: 360,
        ticks: 100,
        gravity: 0,
        decay: 0.94,
        startVelocity: 30,
        shapes: ["heart"],
        colors: ["FFC0CB", "FF69B4", "FF1493", "C71585"],
    };

    $(document).on("change", "input[name='response']", function () {
        responseSelected = true;
        disableButton();
        if($('input[name="response"]:checked').val() == 'yes') {
            confetti({
                ...confettiHeartDefaults,
                particleCount: 50,
                scalar: 2,
            });

            confetti({
                ...confettiHeartDefaults,
                particleCount: 25,
                scalar: 3,
            });

            confetti({
                ...confettiHeartDefaults,
                particleCount: 10,
                scalar: 4,
            });

            $(".hide-accept").addClass("show-accept").removeClass("hide-accept");
            $(".show-reject").addClass("hide-reject").removeClass("show-reject");
        }
        else {
            $(".hide-reject").addClass("show-reject").removeClass("hide-reject");
            $(".show-accept").addClass("hide-accept").removeClass("show-accept");
        };
        $(".hide-until-rsvp-selected").addClass("show-rsvp-selected").removeClass("hide-until-rsvp-selected");
    });

    

    $("#name").on('input', function() {
        if($("#name").val().length != 0) {
            nameEntered = true;
        }
        else {
            nameEntered = false;
        }
        disableButton();
    });

    function disableButton() {
        if(responseSelected && nameEntered) {
            $("#send_rsvp_btn").prop('disabled', false);
            $("#send_rsvp_btn").removeClass('disabled');
        }
        else {
            $("#send_rsvp_btn").prop('disabled', true);
            $("#send_rsvp_btn").addClass('disabled');
        }
    }

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