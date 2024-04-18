<?php
    $staticScripts = ["/Javascript/roundslider.js", "/Javascript/schedule.js"];
?>

<div class="customcell cell medium-3 ">
        <div class="day-label text-center">Monday</div>
            <div class="button-group ">
                <button class="custombtn ">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="1"></div>

        </div>
        <div class=" customcell cell medium-3">
        <div class="day-label text-center">Tuesday</div>
              <div class="button-group">
                <button class="custombtn">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="2"></div>

        </div>
        <div class="customcell cell medium-3">
        <div class="day-label text-center">Wednesday</div>

            <div class="button-group">
                <button class="custombtn">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="3"></div>

        </div>
        <div class="customcell cell medium-3">
        <div class="day-label text-center">Thursday</div>
            <div class="button-group">
                <button class="custombtn">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="4"></div>

        </div>
        
        <div class=" customcell cell medium-4 medium-offset-1">
        <div class="day-label text-center">Friday</div>
            <div class="button-group">
                <button class="custombtn">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="5"></div>

        </div>
        <div class="customcell cell medium-4">
        <div class="day-label text-center">Saturday</div>

            <div class="button-group">
                <button class="custombtn">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="6"></div>

        </div>
        <div class="customcell cell medium-4">
        <div class="day-label text-center">Sunday</div>
            <div class="button-group">
                <button class="custombtn">Not Cleaning</button>
                <button class="custombtn">Cleaning </button>
                <button class="custombtn">Delete</button>
                <button class="custombtn">Apply</button>
            </div>
            <div class="round-slider" data-slider-id="7"></div>
        </div>
        <div class="customcell cell medium-12 medium-offset-4">
            <button id="submitSchedule" class="submit-btn" disabled>Submit Schedule</button>
        </div>


        <script>
$("#submitSchedule").on("click", function() {
    // Check if data for all sliders is present in localStorage
    var allSlidersSaved = true;
    for (var i = 1; i <= 7; i++) {
        if (!localStorage.getItem('slider' + i)) {
            allSlidersSaved = false;
            break;
        }
    }

    if (allSlidersSaved) {
        let data = formatSliderData(); // Get formatted slider data
        console.log("Data ready for submission:", data); // Log data to console for verification
        sendDataToDatabase(data);
    } else {
        alert("Please make sure all sliders are set and applied before submitting.");
    }
});

function formatSliderData() {
    const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    let schedules = [];

    $(".round-slider").each(function(index) {
        var sliderId = $(this).data('slider-id');
        var handles = JSON.parse(localStorage.getItem('slider' + sliderId));
        if (handles && handles.length > 2) {  // Ensure there are more than two handles
            // Sort handles by value to ensure the correct order
            handles.sort((a, b) => a.value - b.value);

            // Start checking from the third handle as the first two are default and usually orange
            for (let i = 2; i < handles.length; i++) {
                if (handles[i].rangeColor === "aqua") {
                    let startSegment = handles[i-1].value;  // Use segment value directly
                    let endSegment = handles[i].value;  // Use segment value directly
                    schedules.push({
                        day: days[index],
                        startSegment: startSegment,
                        endSegment: endSegment,
                    });
                }
            }
        }
    });

    return schedules;
}



function sendDataToDatabase(scheduleData) {
    $.ajax({
        url: '/schedule/create_row',  
        type: 'POST',
        contentType: 'application/json',   
        data: JSON.stringify({schedules: scheduleData}),
        success: function(response) {
            console.log('Data submitted successfully:', response);
            alert('Schedule submitted successfully!');
        },
        error: function(xhr, status, error) {
            console.error('Error submitting data:', error);
            alert('Failed to submit schedule. Please try again.');
        }
    });
}
        </script>