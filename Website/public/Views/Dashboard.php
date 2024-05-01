<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/dashboard.css">
    <link rel="stylesheet" href="/CSS/scheduleTimeRangeControl.css">
    
    <?php // TODO: only used on the schedule page so we should insert this line with php ideally ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/roundSlider/1.6.1/roundslider.css" integrity="sha512-XO53CaiPx+m4HUiZ02P4OEGLyyT46mJQzWhwqYsdqRR7IOjPuujK0UPAK9ckSfcJE4ED7dT9pF9r78yXoOKeYw==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js" crossorigin="anonymous"></script>    
    <script src="/Javascript/dashboard.js"></script>

</head>
<body>
<?php include 'sidebar.php'; ?>

            <div id="main-content" class="grid-x grid-margin-x small-up-1 medium-up-3 large-up-4">
            <?php 
                if(isset($page))
                {
                    if($page !== false)
                        include($page);
                }
                else
                {
                    // Default view goes here.
            ?>
<table class="hover unstriped" id="taskTable">
    <thead>
        <tr>
            <th>Task Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Duration</th>
            <th>Day of Week</th>
        </tr>
    </thead>
    <tbody>
        <tr class="task-group-header" data-toggler=".InProgress">
            <th colspan="6">In Progress</th>
        </tr>
        <tr class="task-group-header" data-toggler=".Missed">
            <th colspan="6">Missed Tasks</th>
        </tr>
        <tr class="task-group-header" data-toggler=".Upcoming">
            <th colspan="6">Upcoming Tasks</th>
        </tr>
    </tbody>
</table>


<script> 
// Dashboard.js
$(document).ready(function () {
    $(document).foundation();
    const currentUserID = <?php echo $data['currentUser']['userId']; ?>;
    console.log(currentUserID);

    $('#taskTable').on('click', '.task-group-header', function() {
        var togglerClass = $(this).data('toggler');
        $(togglerClass).toggle(); // Toggle visibility of elements with the class specified in data-toggler
    });

    function convertDayToNumber(dayName) {
        const days = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday','Sunday'];
        return days.indexOf(dayName.trim()); // Use trim() to remove any extra whitespace
    }

    function segmentToTime(segment) {
        const totalMinutes = segment * 15; // Each segment is 15 minutes
        let hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${ampm}`;
    }

    function calculateDuration(begin, end) {
        const durationMinutes = (end - begin) * 15;
        const hours = Math.floor(durationMinutes / 60);
        const minutes = durationMinutes % 60;
        return `${hours}h ${minutes}m`;
    }

    function appendJobToCategory(job, category, beginTime, endTime) {
        var $lastRow = $(`.task-group-header[data-toggler=".${category}"]`).last();
        var $newRow = $(`<tr class="task-details ${category}" style="display: none;">
            <td>${job.task} in ${job.room}</td>
            <td>${beginTime}</td>
            <td>${endTime}</td>
            <td>${calculateDuration(job.begin, job.end)}</td>
            <td>${job.day}</td>
        </tr>`);

        $newRow.insertAfter($lastRow);
    }

    const now = new Date();
    let currentDayOfWeek = now.getDay(); 
    currentDayOfWeek = currentDayOfWeek === 0 ? 7 : currentDayOfWeek; 
    const currentTime = now.getHours() * 4 + Math.floor(now.getMinutes() / 15); 

    $.ajax({
        url: '/household/gen_rota',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.statusCode === 200) {
                $.each(response.data, function(userId, user) {
                    if (userId == currentUserID) {
                        $.each(user.jobs, function(index, job) {
                            var beginTime = segmentToTime(job.begin);
                            var endTime = segmentToTime(job.end);
                            var jobDay = convertDayToNumber(job.day); 
                            var jobBeginSegment = job.begin;
    
                            var category;
                            if ((jobDay === 0 && currentDayOfWeek === 1) || // Sunday tasks on Monday
                                (jobDay < currentDayOfWeek)) {
                                category = 'Missed'; // Tasks from previous week's Sunday or earlier days
                            } else if (jobDay === currentDayOfWeek && jobBeginSegment >= currentTime) {
                                category = 'In Progress'; // Tasks scheduled for today but not started yet
                            } else {
                                category = 'Upcoming'; // Tasks scheduled for future days
                            }
    
                            appendJobToCategory(job, category, beginTime, endTime);
                        });
                    }
                });
            } else {
                console.error('Failed to fetch data: ' + response.errorMessage);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching data: ' + textStatus);
        }
    });
});

</script>



<style> 
.task-details.Missed {
    background-color: #FFCCCC; /* Light red */
}

.task-details.Upcoming {
    background-color: #FFFFCC; /* Light yellow */
}

.task-details.InProgress {
    background-color: #CCFFCC; /* Light green */
}
</style>

















            <?php } ?>
            </div>
        </div>
    </div>
</div>




<?php 
    if(isset($dynamicScripts))
    {
        foreach($dynamicScripts as $path)
        {
            echo("<script>\n");
            include($path);
            echo("</script>\n");
        }
    }

    if(isset($staticScripts))
    {
        foreach($staticScripts as $path)
        {
            echo("<script src=\"" . $path . "\"></script>\n");
        }
    }
?>
</body>
</html> 