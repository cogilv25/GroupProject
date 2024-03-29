const setBlock = document.getElementById('set-block');
const unsetBlock = document.getElementById('unset-block');
const view = document.getElementById('time-range-view');
const contextButton = document.getElementById('context-button');
let controlRect = view.getBoundingClientRect();
let blockRect = setBlock.getBoundingClientRect();

const scheduleView = document.getElementById('schedule-view');
let scheduleRect = scheduleView.getBoundingClientRect();
let scheduleBaseLayer = document.createElement("div");
scheduleBaseLayer.classList.add("unsetBlock");
scheduleBaseLayer.left = "0%";
scheduleBaseLayer.width = "100%";

let isDragging = false;
let rangeAffirmative = true;
let begin = 24;
let end = 60;
let startRangeSelected = true;
let startX = 0;
let schedule = [[30,56]];

//TODO: sometimes begin == end, fix
function updateRange(newBegin, newEnd)
{
    newBegin = Math.floor(newBegin);
    newEnd = Math.floor(newEnd);
    if(startRangeSelected)
    {
        if(newBegin >= newEnd) newEnd++;
    }
    else
    {
        if(newEnd <= newBegin) newBegin-- ;
    }

    if(newBegin < 0) newBegin = 0;
    if(newBegin > 95) newBegin = 95;
    if(newEnd > 96) newEnd = 96;
    if(newEnd < 1) newEnd = 1;

    begin = newBegin;
    end = newEnd;
    setBlock.style.width = ((end - begin) / 96 * 100) + '%';
    setBlock.style.left = (begin / 96 * 100) + '%';
}

function invertRangeMeaning()
{
    rangeAffirmative = !rangeAffirmative;
    setBlock.style.backgroundColor = rangeAffirmative ? "#1a1" : "#c11";
    contextButton.innerHTML = rangeAffirmative ? "Available" : "Unavailable";
}

function dragRangeInteraction(xPosition)
{
    if(!isDragging) return;

    let xMin = controlRect.x;
    let xMax = xMin + controlRect.width;
    let position = (xPosition - xMin) / controlRect.width * 96;

    if(startRangeSelected)
        updateRange(position ,end);
    else
        updateRange(begin, position);
}


document.addEventListener('touchmove', function(event){ dragRangeInteraction(event.touches[0].pageX);});
document.addEventListener('mousemove', function(event){ dragRangeInteraction(event.clientX);});


function beginRangeInteraction(xPosition)
{
    blockRect = setBlock.getBoundingClientRect();
    controlRect = view.getBoundingClientRect();

    isDragging = true;
    if(xPosition < blockRect.x + (blockRect.width/2))
        startRangeSelected = true;
    else
        startRangeSelected = false;
    dragRangeInteraction(xPosition);
}

view.addEventListener('mousedown', function(event){ 
    event.preventDefault();
    beginRangeInteraction(event.clientX);
});

view.addEventListener('touchstart', function(event){
    event.preventDefault();
    beginRangeInteraction(event.touches[0].pageX);
})

function endRangeInteraction(event)
{
    event.preventDefault();
    isDragging = false;
}

document.addEventListener('mouseup', endRangeInteraction);
document.addEventListener('touchend', endRangeInteraction);
document.addEventListener('touchcancel', endRangeInteraction);

function drawSchedule()
{
    scheduleRect = scheduleView.getBoundingClientRect();
    //Clear the schedule view
    scheduleView.textContent = '';
    scheduleView.appendChild(scheduleBaseLayer);
    let position = 0;

    for (var i = 0; i < schedule.length; i++) {
        let start = (schedule[i][0]/96) * 100;
        let width = ((schedule[i][1] - schedule[i][0])/96) * 100;
        let box = document.createElement("div");
        box.classList.add("setBlock");
        box.style.left = start + "%";
        box.style.width = width + "%";
        scheduleView.appendChild(box);
    }
}

function addRangeToSchedule()
{
    if(begin >= end) return;

    var newSchedule = [];
    var i = 0;

    //Switch on add / remove modes
    if(rangeAffirmative)
    {
        //Collision detection
        var newEntry = [begin, end];
        for (; i < schedule.length; i++) {
            if(begin <= schedule[i][1])
            {
                // New entry is completely before i
                if(end < schedule[i][0])
                    break;

                // Middle of i
                if(begin > schedule[i][0])
                    newEntry[0] = schedule[i][0];

                // Find endpoint of new entry
                for(;i<schedule.length-1;i++)
                    if(newEntry[1] <= schedule[i][1]) break;

                // New entry is completely after i
                if(newEntry[0] > schedule[i][1])
                {
                    newSchedule.push(schedule[i]);
                    i++;
                    break;
                }

                // New entry is completely before i
                if(newEntry[1] < schedule[i][0])
                    break;


                // New entry collides with i
                newEntry[1] = Math.max(newEntry[1],schedule[i][1]);
                i++;
                break;
            }
            newSchedule.push(schedule[i]);
        }

        newSchedule.push(newEntry);

        for (; i < schedule.length; i++)
        {
            newSchedule.push(schedule[i]);
        }
    }
    else
    {
        for (; i < schedule.length; i++)
        {
            if(begin <= schedule[i][1])
            {
                // Delete range does not collide with anything.
                if(end < schedule[i][0]) break;

                // Middle of i
                if(begin > schedule[i][0])
                {
                    if(end < schedule[i][1])
                    {
                        // Split into 2
                        newSchedule.push([schedule[i][0], begin]);
                        newSchedule.push([end, schedule[i][1]]);
                        i++;
                        break;
                    }
                    else
                        newSchedule.push([schedule[i][0], begin]);
                }
                else
                {
                    if(end < schedule[i][1])
                        newSchedule.push([end, schedule[i][1]]);
                }

            }
            else
                newSchedule.push(schedule[i]);
        }

        for (; i < schedule.length; i++)
                newSchedule.push(schedule[i]);
    }

    schedule = newSchedule;
    drawSchedule();
}

updateRange(90,95);
drawSchedule();