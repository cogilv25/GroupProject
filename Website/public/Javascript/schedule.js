var localStorageEmpty = true;

for (var i = 0; i < 7; i++) {
    if (localStorage.getItem('slider' + (i + 1))) {
        localStorageEmpty = false; // If at least one slider is found in localStorage, set to false
        break; // Exit loop early since localStorage is not empty
    }
}
// If localStorage is empty, disable the button and add a tooltip
if (localStorageEmpty) {
    $("#submitSchedule").prop('disabled', true); // Disable the button
    $("#submitSchedule").attr('title', 'Please apply sliders before submitting'); // Add tooltip
}
// Initialize an array to keep track of whether sliders are applied
var slidersApplied = new Array(7).fill(false);

// Function to add clock numbers around the slider
$.fn.roundSlider.prototype.addClockNumbers = function() {
  const container = this.container.find('.rs-inner-container');
  const radius = this.options.radius - 30;  // Adjust radius as needed to fit numbers

  // Loop to add clock numbers around the slider
  for (let i = 0; i < 24; i++) {
    const angleDeg = (360 / 24) * i - 90;  // Adjust angle calculation for 24 numbers
    const angleRad = angleDeg * (Math.PI / 180);
    const x = radius * Math.cos(angleRad) + container.width() / 2;
    const y = radius * Math.sin(angleRad) + container.height() / 2;

    // Create and position the clock number
    const number = $('<div></div>').text(i).css({
      position: 'absolute',
      left: `${x}px`,
      top: `${y}px`,
      transform: 'translate(-50%, -50%)',
      userSelect: 'none'
    }).addClass('rs-clock-number');

    container.append(number); // Add number to container
  }
};

$.fn.roundSlider.prototype._handleDragDistance = 180;
// Create custom handles
$.fn.roundSlider.prototype.createCustomHandles = function () {
  var o = this.options;
  for (var i = 0; i < o.customHandles.length; i++) {
    var handle = o.customHandles[i];
    var index = handle.index;
    console.log("Creating handle:", handle);
    // create the handle here
    this._createHandle(index);
    this.setValue(handle.value, index);
    this["range" + index] = this._createSVG("path", { stroke: handle.rangeColor, "stroke-width": o.width });
    this._append(this.$svgEle, [this["range" + index]]);
  }
}

// Update the range values of the slider
$.fn.roundSlider.prototype.updateRangeValues = function () {
  var handles = this._handles(); // Accesses current slider handles directly
  // Update all the range values
  if (handles.length > 1) {
    for (var i = 0; i < handles.length - 1; i++) {
      var index = handles.eq(i).attr("index");
      var nextIndex = handles.eq(i + 1).attr("index");
      var value = this["_handle" + index].angle;
      var nextValue = this["_handle" + nextIndex].angle;

      // Calculate and update the range values
      var d1 = this._drawPath(value, nextValue, this.centerRadius);
      var nextHandleColor = this.options.customHandles.find(h => h.index === String(nextIndex)).rangeColor;
      this._setAttribute(this["range" + index], { d: d1, stroke: nextHandleColor });
    }
  }
}

// Validate handle values
$.fn.roundSlider.prototype.validateHandleValues = function (e) {
  var value = e.value, o = this.options,
      activeIndex = this._active, handles = this._handles();

  if (handles.length > 1) {
    for (var i = 0; i < handles.length; i++) {
      var currentIndex = handles.eq(i).attr("index");
      if (currentIndex != activeIndex) continue;

      var preValue = handles.eq(i - 1).attr("aria-valuenow");
      var nextValue = handles.eq(i + 1).attr("aria-valuenow");
      if (i == 0) preValue = o.min;
      else if (i == handles.length - 1) nextValue = o.max;

      // Validate the handle values
      if (value < preValue) {
        this.setValue(preValue);
        return false;
      }
      if (value > nextValue) {
        this.setValue(nextValue);
        return false;
      }
    }
  }
}


$.fn.roundSlider.prototype._activeHandleBar = function (index) {
  index = (index != undefined) ? index : this._active;
  return $(this.container[0].querySelectorAll(`[index='${index}']`)).parent();
}

$.fn.roundSlider.prototype.refreshSlider = function () {
  this._destroyControl();
  this._onInit();
  this._handleBars().remove();
  this.options.value = null;
  this.createCustomHandles();
  this.updateRangeValues();
  this.addClockNumbers();
}

$(".round-slider").each(function(index) {
  var sliderId = $(this).data('slider-id');
  var storedHandles = localStorage.getItem('slider' + sliderId);
  var initialHandles = storedHandles ? JSON.parse(storedHandles) : [
      {index: "1", value: 0, rangeColor: "orange"},
      {index: "2", value: 5, rangeColor: "orange"}
  ];
    $(this).roundSlider({
        sliderType: "default",
        animation: false,
        radius: 120,
        width: 15,
        min: 0,
        max: 95,
        startAngle: 90,
        endAngle: "+360",
        showTooltip: false,
        customHandles: jQuery.extend(true, [], initialHandles),
        change: function(e) {

          var index = e.handle.index;
          var value = e.value;
          var handleToUpdate = this.options.customHandles.find(h => h.index === String(e.handle.index));
          if (handleToUpdate) {
              handleToUpdate.value = e.value;

            }

          console.log("Handle value changed:", e.value);

          checkAllApplied();
          },
        create: function() {
            this._handleBars().remove();
            this.createCustomHandles();
            this.addClockNumbers();
        },
        valueChange: function(e) {
            this.updateRangeValues();
        },
        beforeValueChange: function(e) {
            return this.validateHandleValues(e);
        }
    });
});



function checkAndHighlightSliders() {
  var allMatch = true;
  $(".round-slider").each(function() {
      var sliderId = $(this).data('slider-id');
      var currentValues = $(this).data("roundSlider").options.customHandles;
      var storedValues = localStorage.getItem('slider' + sliderId);
      
      if (JSON.stringify(currentValues) !== storedValues) {
          $(this).css("background", "red");  // Change background color if different
          allMatch = false;
      } else {
          $(this).css("background", "");  // Reset background color if the same
      }
  });
  return allMatch;
}




var sliderObj = $("#slider1").data("roundSlider");

function updateHandles(count, sliderObj) {
    sliderObj.options.customHandles = allHandles.slice(0, count);
    sliderObj.refreshSlider();
    sliderObj.addClockNumbers();
}

function addNewHandle(color, sliderObj) {
    var handles = sliderObj.options.customHandles;

    if (handles.length === 0) {
        alert("Add at least one handle to initialize.");
        return;
    }
    var lastHandleValue = handles[handles.length - 1].value;
    var newHandleValue = (lastHandleValue + 4) % 97; 

    if (handles.some(handle => handle.value === newHandleValue)) {
        alert("Handle overlap, try again.");
        return;
    }

    var newHandleIndex = String(handles.length + 1);
    var newHandle = {
        index: newHandleIndex,
        value: newHandleValue,
        rangeColor: color
    };

    handles.push(newHandle);
    sliderObj.options.customHandles = handles;
    sliderObj.refreshSlider();
}

$(".button-group").each(function() {
    var $slider = $(this).next(".round-slider");
    var sliderId = $slider.data('slider-id');
    var sliderObj = $slider.data("roundSlider");

    $(this).find(".custombtn").on("click", function() {
        var buttonType = $(this).text().trim();
        switch(buttonType) {
            case "Not Cleaning":
                addNewHandle("orange", sliderObj);
                break;
            case "Cleaning":
                addNewHandle("aqua", sliderObj);
                break;
            case "Delete":
                if (sliderObj.options.customHandles.length > 2) {
                    sliderObj.options.customHandles.pop();
                    sliderObj.refreshSlider();
                } else {
                    alert("Cannot remove the initial two handles.");
                }
                break;
                case "Apply":
                    localStorage.setItem('slider' + sliderId, JSON.stringify(sliderObj.options.customHandles));
                    slidersApplied[sliderId - 1] = true;
                    break;
            }
    });
});
// Check if all sliders are properly applied before enabling the "Submit Schedule" button
function checkAllApplied() {
    if (slidersApplied.every(Boolean) ||checkAndHighlightSliders() ) {
        $("#submitSchedule").prop('disabled', false); // Enable the button if everything is correct
    } else {
        $("#submitSchedule").prop('disabled', true);
    }
}
// When any custom button is clicked, check again and make sure everything is still okay
$(".custombtn").on("click", function() {
    checkAllApplied();
});

// When attempting to submit the schedule, perform final checks
$("#submitSchedule").on("click", function() {
  if (!areAllSlidersApplied()) {
      alert("Please make sure all sliders are set and applied before submitting.");
      return;
  }
    // Next, ensure there are no discrepancies
  if (!checkAndHighlightSliders()) {
    return;
  }
    // Everything checks out, proceed to format the data
  let data = formatSliderData(); // Get formatted slider data
  sendDataToDatabase(data);
});
// Check if every slider has corresponding saved data in localStorage
function areAllSlidersApplied() {
  var allSlidersSaved = true;
  for (var i = 1; i <= 7; i++) {
      if (!localStorage.getItem('slider' + i)) {
          allSlidersSaved = false;
          break;
      }
  }
  return allSlidersSaved;
}
// Check sliders against their saved values and highlight any discrepancies
function checkAndHighlightSliders() {
  var allMatch = true;
  $(".round-slider").each(function() {
      var sliderId = $(this).data('slider-id');
      var currentValues = $(this).data("roundSlider").options.customHandles;
      var storedValues = JSON.parse(localStorage.getItem('slider' + sliderId) || '[]');

      if (JSON.stringify(currentValues) !== JSON.stringify(storedValues)) {
        $(this).css("background-color", " rgba(245, 0, 0, 0.5)");
          $(this).attr({
            'data-tooltip': '',
            'title': 'Changes not applied. Click Apply to save changes.',
            'data-position': 'top',
            'data-alignment': 'center'
        });
        allMatch = false;
      } else {
          $(this).css("background", "");
          $(this).removeAttr('data-tooltip title data-position data-alignment');  // Reset background color if the same
      }
  });
  return allMatch;
}
// Format the data from sliders to a structured format for the backend
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
        url: '/schedule/update',  
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

