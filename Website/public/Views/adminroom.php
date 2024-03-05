<!-- Input schema 

{value} donates a value from the database

$currentUser =
[
    'homeless' = {userHasHome}, // Boolean
    'userId' = {userId},
    'isAdmin' = {userIsAdmin} // Boolean
]

$tasks =
[
    {taskId1} = {taskName1},
    {taskId2} = {taskName2},
    {taskId3} = {taskName3},
    {taskId4} = {taskName4},
]

$rooms =
[ 
   {roomId1} => 
   [ 
        'name' => {roomName1},
        'tasks' =>              //If a taskId row is true the room has the task    
        [
            {task1Id} => true,
            {task2Id} => true,                              
            {task3Id} => false,
            {task4Id} => false
        ]
    ], 
    {roomId2} => 
    [
        'name' => {roomName2},
        'tasks' =>
        [
            {task1Id} => false,
            {task2Id} => true,
            {task3Id} => false,
            {task4Id} => true
        ]
    ]
]

TODO:   Handle admin vs member vs homeless

TODO:   Will need to pass the php table 'rooms' to javascript somehow so that it
            can dynamically show relevant data for each room when it is selected.  

TODO:   Need to figure out how create, delete, update works for room_has_task rules
        could just send the backend a request whenever the user changes something 
        and it gives you a response.. probably want to avoid passing RHTId's about?  

TODO:   When a room is created it probably makes sense for the backend to pass back
        the new id on success and the frontend can just insert it where it's needed? 
        We could also just send the whole page again but I think this might get slow
        if there are more than a handful of users..                                   -->

         <div class="cell small-12 medium-6 large-auto">
            <!-- Room Name and Input Button -->
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                    <label for="roomName">Room Name</label>
                    <input type="text" id="roomName" placeholder="Enter Room Name">
                </div>

            </div>
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                <button class="button" type="button"  style="margin-top: 1.5rem; width:100%;">Add</button>
                </div>
            </div>
            <!-- Select Room -->
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                    <label for="roomDropdown">Select Room</label>
                    <select id="roomDropdown">
                        <?php foreach ($rooms as $roomId => $room) {?>
                        <option value="<?=$roomId?>"><?=$room['name']?></option>
                        <?php }?>
                    </select>
                </div>
            </div>

            <!-- Assign Tasks -->
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                    <fieldset class="fieldset">
                        <legend>Assign Tasks</legend>
                        <?php foreach($rooms[array_key_first($rooms)]['tasks'] as $taskId => $value){?>
                        <input id="<?=$taskId?>" type="checkbox" <?=$value ? "checked" : ""?>><label for="<?=$taskId?>"><?=$tasks[$taskId]?></label><br>
                        <?php } ?>
                    </fieldset>
                </div>
            </div>
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                <button class="button" type="button"  style="margin-top: 1.5rem; width:100%;">Assign Room to Task</button>
                </div>
            </div>

        <div class="cell small-12 medium-6 large-4">
            <div class="callout" style="height: 100%;">
                <h5>What rooms have tasks</h5>
                <ul>
                    <?php foreach ($rooms as $roomId => $room) { 
                        foreach ($room['tasks'] as $taskId => $taskRequired) { 
                            if($taskRequired){                                  ?>
                    <li>
                        <?=$room['name']?> + <?=$tasks[$taskId]?>
                        <button class="button" style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Link</button>
                    </li>
                    <?php } 
                            } 
                              }  ?>
                </ul>
            </div>
        </div>

        </div>

        <div class="cell small-12 medium-6 large-4">
            <div class="callout" style="height: 100%;">
                <h5>Room</h5>
                <ul>
                    <?php foreach ($rooms as $roomId => $room) 
                    { ?>
                    <li>
                        <?=$room['name']?>
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Room</button>

                    </li>
                <?php } ?>
                </ul>
            </div>
        </div>