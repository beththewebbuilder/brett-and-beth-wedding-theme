$(document).ready(function() {
    const confettiHeartDefaults = {
        spread: 360,
        ticks: 100,
        gravity: 0,
        decay: 0.94,
        startVelocity: 30,
        shapes: ["heart"],
        colors: ["FFC0CB", "FF69B4", "FF1493", "C71585"],
    };

    function runHeartConfetti() {
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
    }

    // PARTY SCRIPT - START //
    var responseSelected = false;
    var nameEntered = false;

    $(document).on("change", "input[name='response']", function () {
        responseSelected = true;
        disableButton();
        if($('input[name="response"]:checked').val() == 'yes') {
            runHeartConfetti();
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
            $(".form-response-box").show();
            $(".loading-send").show();
        
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
                    $(".loading-send").hide();
                    
                    if(response.toUpperCase().indexOf('ERROR') >= 0) {
                        alert('error!');
                    }
                    else {
                        $(".loaded-content").show();
                        if($('input[name="response"]:checked').val() == 'yes') {
                            $(".yes-response").show();
                            // show 'can't wat to see you' message
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
                        }
                        else {
                            $(".no-response").show();
                        }
                        $('#name').val("");
                        $('#people').val("");
                        $('#people').val(2);
                        $('#song').val("");
                        $('#details').val("");
                        $('input[name="response"]').prop("checked", false);

                        responseSelected = false;
                        nameEntered = false;
                        disableButton();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('error!');
                }
            })
        }
        return false;
    });
    // PARTY SCRIPT - END //

    // WEEKEND SCRIPT - START //
    var weekendNameEntered = false;
    var weekendResponseSelected = false;
    var dietaryNoneSelected = true;

    $(document).on("change", "input[name='weekend-response']", function() {
        weekendResponseSelected = true;
        disableWeekendButton();
        var response = $('input[name="weekend-response"]:checked').val();

        if(response.indexOf('yes') >= 0 ) {
            runHeartConfetti();
            $(".hide-accept").addClass("show-accept").removeClass("hide-accept");
            $(".weekend-only-hide").addClass("weekend-only-show").removeClass("weekend-only-hide");
            $(".party-only-hide").addClass("party-only-show").removeClass("party-only-hide");

            $(".show-reject").addClass("hide-reject").removeClass("show-reject");
        }

        if(response == 'yes-weekend-only') {
            $(".weekend-only-hide").addClass("weekend-only-show").removeClass("weekend-only-hide");
            $(".party-only-show").addClass("party-only-hide").removeClass("party-only-show");
        }
        if(response == 'yes-party-only') {
            $(".party-only-hide").addClass("party-only-show").removeClass("party-only-hide");
            $(".weekend-only-show").addClass("weekend-only-hide").removeClass("weekend-only-show");
        }
        if(response == 'no') {
            $(".hide-reject").addClass("show-reject").removeClass("hide-reject");
            $(".show-accept").addClass("hide-accept").removeClass("show-accept");
            $(".party-only-show").addClass("party-only-hide").removeClass("party-only-show");
            $(".weekend-only-show").addClass("weekend-only-hide").removeClass("weekend-only-show");
        }

        $(".hide-until-rsvp-selected").addClass("show-rsvp-selected").removeClass("hide-until-rsvp-selected");
    });

    $("#weekend-name").on('input', function() {
        if($("#weekend-name").val().length > 0) {
            weekendNameEntered = true;
        }
        else {
            weekendNameEntered = false;
        }
        disableWeekendButton();
    });

    function disableWeekendButton() {
        if(weekendResponseSelected && weekendNameEntered) {
            $("#send_weekend_rsvp_btn").prop('disabled', false);
            $("#send_weekend_rsvp_btn").removeClass('disabled');
        }
        else {
            $("#send_weekend_rsvp_btn").prop('disabled', true);
            $("#send_weekend_rsvp_btn").addClass('disabled');
        }
    }

    $(document).on("change", "input[name='weekend-dietary']", function() {
        var checkedBoxes$ = $('input[name="weekend-dietary"]:checked');
        
        if(checkedBoxes$.length < 1) {
            dietaryNoneSelected = true;
            $('#dietary-none').prop("checked", true);
        }
        
        checkedBoxes$.each(function(index, value){
            this.id != 'none';
            dietaryNoneSelected = false;
            $('#dietary-none').prop("checked", false);
        });
    });

    $('#send_weekend_rsvp_btn').click(function() {

    })
    // WEEKEND SCRIPT - END //

    $("#close-modal").click(function() {
        $(".yes-response").hide();
        $(".no-response").hide();
        $(".form-response-box").hide();
    });
})