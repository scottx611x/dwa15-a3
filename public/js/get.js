/*
Post form data directly to our php class
 */

$(document).ready(function(){
    $("#submit").click(function(e){

        // Prevent default form submission behavior of a page reload
        e.preventDefault();

        // Gather values set in form
        var numWords = parseInt($("#numWords").val());
        var numIncludeChecked = $("#numIncludeChecked").prop('checked');
        var numIncluded = parseInt($("#numIncluded").val());
        var symbolIncludeChecked = $("#symbolIncludeChecked").prop('checked');
        var symbolIncluded = $("#symbolIncluded").val();

        // Construct payload
        var payload = {
            numWords: numWords,
            numIncludeChecked: numIncludeChecked,
            numIncluded: numIncluded,
            symbolIncludeChecked: symbolIncludeChecked,
            symbolIncluded: symbolIncluded
        };

        $.get("/generatePassword", payload
        ).done(function(data) {
            try {
                var errorObj = eval("(" + data + ")");
                var errorMessages = [];
                if (errorObj["Error"]) {
                    for (var key in errorObj["Error"]) {
                        errorMessages.push(errorObj["Error"][key] + '\n')
                    }
                    $("#generated-error").modal();
                    $("#error-container").text(errorMessages);
                }
            }
            catch(err) {
                $("#generated-password").modal();
                $("#pass-container").text(data);
            }
        });
    });
});

