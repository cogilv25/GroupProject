<div class="container">
<?php
	$currentUser = ['role' => 'member', 'userId' => 1, 'forename' => 'Calum', 'surname' => 'Lindsay'];
	$tasks = [ 1 => ['name' => "Hoovering"], 2 => ['name' => "Dusting"]];
	$rooms = [ 1 => ['name' => "Living Room"], 2 => ['name' => "Kitchen"]];
	//require "scheduleTimeRangeControl.html";
?>
	    <div style="display: flex; flex-direction: column; width: 100%; justify-content: center;">
        <div style=" display: flex; width: 85%; margin-inline: auto; padding: 3px;">
        </div>  
    <div id="time-range-view" class="view">
        <div id="set-block" class="setBlock">
        </div>
    </div>
    <div style=" display: flex; width: 85%; justify-content: space-between; margin-inline: auto; padding: 3px;">
            <button class="time-range-button" id="context-button" type="button" ontouchend="invertRangeMeaning();" onclick="invertRangeMeaning();">Available</button>
            <button class="time-range-button" id="apply-button" type="button" ontouchend="addRangeToSchedule();" onclick="addRangeToSchedule();">Apply</button>
    </div> 
    </div>
    <div style="display: flex; flex-direction: column; width: 100%; justify-content: center; margin-top: 50px;"> 
    <div id="schedule-view" class="view">
        <div id="set-block" class="setBlock">
        </div>
    </div>
    </div>
	<div class="three-lists-container">
        <div class="list-container">
        	<h3> Users </h3>
        	<?php if($currentUser['role'] == 'member') { ?>
            	<button onclick="showUserSchedule(<?=$currentUser['userId']?>);" class ="list-item"> Me </button>
            <?php } else {
            	foreach($users as $userId => $user) { ?>
            	<button onclick="showUserSchedule(<?=$user['userId']?>);" class ="list-item"> <?=$user['forename'] .' '.$user['surname']?> </button>
        	<?php }} ?>
        </div>
        <div class="list-container">
        	<h3> Rooms </h3>
        	<?php foreach($rooms as $roomId => $room) { ?>
            <button onclick="showRoomSchedule(<?=$roomId?>);" class ="list-item"> <?=$room['name']?> </button>
        	<?php } ?>
        </div>
        <div class="list-container">
        	<h3> Tasks </h3>
        	<?php foreach($tasks as $taskId => $task) { ?>
            <button onclick="showTaskSchedule(<?=$taskId?>);" class ="list-item"> <?=$task['name']?> </button>
        	<?php } ?>
        </div>
    </div>
</div>