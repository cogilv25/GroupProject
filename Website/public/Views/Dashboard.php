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
                            <th>Day of  weeek</th>
                            <th>Priority</th>
                            <th>Points to gain</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="task-group-header" data-toggler=".Inprogress">
                            <th colspan="6">
                                <a href="javascript:void(0);" class="toggle-group">In Progress</a>
                            </th>
                        </tr>
                        <tr class="task-details Inprogress" style="display: none;">
                            <td>test</td>
                            <td>test</td>
                            <td>test</td>
                            <td>test</td>
                            <td>test</td>
                            <td>test</td>
                            <td>test</td>
                        </tr>
                        <tr class="task-group-header" data-toggler=".to-do">
                            <th colspan="6">
                                <a href="javascript:void(0);" class="toggle-group">To-do</a>
                            </th>
                        </tr>
                        <tr class="task-details to-do" style="display: none;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>























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