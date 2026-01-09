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

        if(responseSelected && nameEntered) {
            $(".form-response-box").show();
            $(".loading-send").show();
        
            $.ajax({
                type: 'POST',
                url : myajax.ajaxurl,
                dataType: 'json',
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
                    
                    if(!response.success) {
                        alert('Hmm, there was a problem. Please try again or email rsvp@brett-and-beth.co.uk.');
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
                    alert('Hmm, there was a problem. Please try again or email rsvp@brett-and-beth.co.uk.');
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

    $(document).on("change", "input[id='dietary-yes']", function() {
        var checkedBoxes$ = $('input[name="weekend-dietary"]:checked');
        checkedBoxes$.each(function(index, value){
            $(this).prop("checked", false);
        });    
        $('#dietary-details').val("");

        if($(this).prop("checked")) {
            $(".dietary-options-hide").addClass("dietary-options-show").removeClass("dietary-options-hide");
        }
        else {
            $(".dietary-options-show").addClass("dietary-options-hide").removeClass("dietary-options-show");
        }
    });

    $(document).on("change", "input[name='weekend-stay']", function() {
        var response = $(this).val();
        $("#weekend-stay-details").val("");
        if(response == 'yes') {
            $(".not-staying-whole-weekend-show").addClass("not-staying-whole-weekend-hide").removeClass("not-staying-whole-weekend-show");
        }
        else {
            $(".not-staying-whole-weekend-hide").addClass("not-staying-whole-weekend-show").removeClass("not-staying-whole-weekend-hide");
        }
    });

    $('#send_weekend_rsvp_btn').click(function() {
        if(weekendNameEntered && weekendResponseSelected) {
            $(".form-response-box").show();
            $(".loading-send").show();

            //Acceptance to which events//
            var acceptanceResponse = $('input[name="weekend-response"]:checked').val();
            var acceptanceText = "are unable to attend either event and regrettfully decline";
            var acceptWeekend = false;
            var acceptParty = false;
            if(acceptanceResponse == 'yes-all'){
                acceptanceText = "will be attending BOTH Wedding Weekend and Happily Ever After Party";
                acceptWeekend = true;
                acceptParty = true;
            }
            else if(acceptanceResponse == 'yes-weekend-only') {
                acceptanceText = "will be attending ONLY the Wedding Weekend and are unable to attend the Happily Ever After Party";
                acceptWeekend = true;
            }
            else if(acceptanceResponse == 'yes-party-only') {
                acceptanceText = "will be attending ONLY the Happily Ever After Party and are unable to attend the Wedding Weekend";
                acceptParty = true;
            }

            //Dietry requirements//
            var dietaryText = "";
            var dietaryRequirements = false;
            if($("#dietary-yes").prop("checked")) {
                dietaryRequirements = true;
                var dietaryResponse = $('input[name="weekend-dietary"]:checked');
                dietaryResponse.each(function(index, value){
                    if((dietaryResponse.length > 1) && index > 0) {
                        dietaryText += ", ";
                    }
                    dietaryText += $(this).val();
                });
                dietaryText += ". " + $('#dietary-details').val();
            }
            

            //Staying both nights//
            var stayingBothNights = false
            if($("input[name='weekend-stay']:checked").val() == "yes") {
                stayingBothNights = true;
            }
            
            $.ajax({
                type: 'POST',
                url : myajax.ajaxurl,
                dataType: 'json',
                data: {
                    action: "save_weekend_rsvp",
                    name: $('#weekend-name').val(),
                    acceptWeekend: acceptWeekend,
                    acceptParty: acceptParty,
                    acceptText: acceptanceText,
                    stayBothNights: stayingBothNights,
                    stayDetails: $("#weekend-stay-details").val(),
                    dietaryRequirements: dietaryRequirements,
                    dietaryDetails: dietaryText, 
                    song: $('#weekend-song').val(),
                    message: $('#weekend-details').val()
                },
                success: function(response) {
                    $(".loading-send").hide();
                    
                    if(!response.success) {
                        alert('Hmm, there was a problem. Please try again or email rsvp@brett-and-beth.co.uk.');
                    }
                    else {
                        $(".loaded-content").show();
                        if(acceptanceResponse.indexOf('yes') >= 0) {
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
                        $('#weekend-name').val("");
                        $('#weekend-song').val("");
                        $('#weekend-details').val("");
                        $('input[name="weekend-response"]').prop("checked", false);
                        $('#dietary-details').val("");
                        $("input[name='weekend-dietary']").each(function(){
                            $(this).prop("checked", false);
                        });
                        $('#dietary-yes').prop("checked", false);
                        $('input[name="weekend-response"]').prop("checked", false);
                        $("#weekend-stay-details").val("");

                        weekendResponseSelected = false;
                        weekendNameEntered = false;
                        dietaryNoneSelected = true;
                        disableWeekendButton();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('Hmm, there was a problem. Please try again or email rsvp@brett-and-beth.co.uk.');
                }
            })
            $(".loaded-content").hide();
        }
        return false;
    });
    // WEEKEND SCRIPT - END //

    $("#close-modal").click(function() {
        $(".yes-response").hide();
        $(".no-response").hide();
        $(".form-response-box").hide();
    });
})