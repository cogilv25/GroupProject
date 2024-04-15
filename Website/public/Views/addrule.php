<?php
$dynamicScripts = ["Javascript/addRule.js"];
/*
Rule Input Data Examples:

1. Room Time Restriction:
- 'userId' => [User ID],
- 'roomName' => [Name of the room],
- 'day' => [Day of the week],

2. Task Time Restriction:
- 'userId' => [User ID],
- 'taskName' => [Name of the task],
- 'day' => [Day of the week],


3. User Task Restriction:
- 'userId' => [User ID],
- 'taskName' => [Name of the task],


4. User Room Restriction:
- 'userId' => [User ID],
- 'roomName' => [Name of the room],


Example Data:
$ruleData = [
    ['userId' => 1, 'roomName' => 'kitchen', 'day' => 'Monday'],
    ['userId' => 2, 'taskName' => 'Vacuuming', 'day' => 'Wednesday'],
    ['userId' => 3, 'taskName' => 'Dusting'],
    ['userId' => 4, 'roomName' => 'living room'],
];
*/

?>

<div class="cell">
	<h2 class="text-center">Create a Rule</h2>
	<?php
	// Commenting this out for now as I'm not sure if it will stay here..
	// <h5 class="customh5">Room Time Restriction</h5>
	// <div class="grid-x grid-padding-x align-center">
	// 	<div class="cell">
	// 		<div class="grid-x grid-padding-x">
	// 			<div class="cell small-3">
	// 				<label for="input1">Room Name</label>
	// 				<input class="custominputs" id="input1" type="text" placeholder="Input 1">
	// 			</div>
	// 			<div class="cell small-3">
	// 				<label for="input2">Day</label>
	// 				<input class="custominputs"id="input2" type="text" placeholder="Input 2">
	// 			</div>
	// 			<div class="cell small-3 ">
	// 				<label  id=""  for="input3">Start Time: </label>
	//                 <input type="text" class="form-control" value="09:30">
	// 			</div>
	// 			<div class="cell small-3">
	// 				<label for="input4">End Time: </label>
	// 				<input class="custominputs"id="input4" type="text" placeholder="Input 4">
	// 			</div>
	// 			<div class="cell small-12">
	// 				<button class="button expanded">Submit</button>
	// 			</div>
	// 		</div>
	// 	</div>
	// </div>
	// <h5 class="customh5">Task Time Restriction</h5>
	// <div class="grid-x grid-padding-x align-center">
	// 	<div class="cell">
	// 		<div class="grid-x grid-padding-x">
	// 			<div class="cell small-3">
	// 				<label for="input1">Task Name</label>
	// 				<input class="custominputs" id="input1" type="text" placeholder="Input 1">
	// 			</div>
	// 			<div class="cell small-3">
	// 				<label for="input2">Day</label>
	// 				<input class="custominputs"id="input2" type="text" placeholder="Input 2">
	// 			</div>
	// 			<div class="cell small-3">
	// 				<label  for="input3">Start Time: </label>
	// 				<input class="custominputs"id="input3" type="text" placeholder="Input 3">
	// 			</div>
	// 			<div class="cell small-3">
	// 				<label for="input4">End Time: </label>
	// 				<input class="custominputs"id="input4" type="text" placeholder="Input 4">
	// 			</div>
	// 			<div class="cell small-12">
	// 				<button class="button expanded">Submit</button>
	// 			</div>
	// 		</div>
	// 	</div>
	// </div>
	?>
	<h5 class="customh5">User Task Restriction</h5>
	<div class="grid-x grid-padding-x align-center">
		<div class="cell">
			<div class="grid-x grid-padding-x">
				<div class="<?=$currentUser['role'] == 'member' ? "cell small-12":"cell small-6"?>">
					<label for="taskSelect">Task</label>
					<select name="taskId" class="custominputs" id="taskSelect">
				<?php   foreach($tasks as $id => $task) { ?>
							<option value="<?=$id?>"><?=$task['name']?></option>
				<?php   } ?>
					</select>
				</div>
		<?php 	if(!($currentUser['role'] == "member")) 
				{ ?>
					<div class="cell small-6">
						<label for="taskUserSelect">User</label>
						<select name="userId" class="custominputs" id="taskUserSelect">
				<?php   foreach($users as $id => $user) { ?>
							<option value="<?=$id?>"><?=$user['forename']." ".$user['surname']?></option>
				<?php   } ?>
						</select>
					</div>
		<?php	} ?>
				<div class="cell small-12">
					<button class="button expanded" onclick="submitTaskRule();">Submit</button>
				</div>
			</div>
		</div>
	</div>
	<h5 class="customh5">User Room Restriction</h5>
	<div class="grid-x grid-padding-x align-center">
		<div class="cell">
			<div class="grid-x grid-padding-x">
				<div class="<?=$currentUser['role'] == 'member' ? "cell small-12":"cell small-6"?>">
					<label for="roomId">Room</label>
					<select name="roomId" class="custominputs" id="roomSelect">
				<?php   foreach($rooms as $id => $room) { ?>
							<option value="<?=$id?>"><?=$room['name']?></option>
				<?php   } ?>
					</select>
				</div>
		<?php 	if(!($currentUser['role'] == "member")) 
				{ ?>
					<div class="cell small-6">
						<label for="roomUserSelect">User</label>
						<select name="userId" class="custominputs" id="roomUserSelect">
				<?php   foreach($users as $id => $user) { ?>
							<option value="<?=$id?>"><?=$user['forename']." ".$user['surname']?></option>
				<?php   } ?>
						</select>
					</div>
		<?php	} ?>
				<div class="cell small-12">
					<button class="button expanded" onclick="submitRoomRule();">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>