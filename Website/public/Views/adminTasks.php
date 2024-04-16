<?php 
$dynamicScripts = ["Javascript/task.js"];
 ?>

<?php if($currentUser['homeless'] === false) { ?>
    <div class="cell small-12 medium-6 large-auto">
        <!-- Room Name and Input Button -->
        <div class="grid-x grid-padding-x align-middle">
            <div class="cell small-12">
                <label for="TaskName">Task Name</label>
                <input type="text" id="taskName" placeholder="Enter Task Name">
                <label for="TaskDesc">Task Description</label>
                <input type="text" id="taskDescription" placeholder="Enter Task description">
            </div>
        </div>
        <div class="grid-x grid-padding-x align-middle">
            <div class="cell small-12">
            <button id="addButton" class="button" type="button"  style="margin-top: 1.5rem; width:100%;">Add</button>
            </div>
        </div>
        <!-- Select Room -->
        <div class="grid-x grid-padding-x align-middle">
            <div class="cell small-12">
                <label for="taskDropdown">Select Task</label>
                <select id="taskDropdown">
                    <?php foreach($tasks as $taskId => $task) { ?>
                    <option value="<?=$taskId?>"><?=$task['name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>

        <!-- Assign Tasks -->
        <div class="grid-x grid-padding-x align-middle">
            <div class="cell small-12">
                <fieldset class="fieldset">
                    <legend>Assigned Rooms</legend>
                    <!--loop for room and change id to have room id -->
                    <?php 
                    $start = array_key_first($tasks);
                    if($start != null){
                        if(count($rooms) > 0){
                            foreach($tasks[$start]['rooms'] as $roomId => $value){ ?>
                                <input id="assignedCheckbox<?=$roomId?>" type="checkbox" <?=$value?"checked":""?>>
                                <label for="assignedCheckbox<?=$roomId?>"> <?=$rooms[$roomId]?> </label>
                                <br>
                        
                    
                    <?php }}} ?>
                </fieldset>
            </div>
        </div>
        <div class="grid-x grid-padding-x align-middle">
            <div class="cell small-12">
            <button id="updateAssignedRoomsButton" class="button" type="button"  style="margin-top: 1.5rem; width:100%;">Update</button>
            </div>
        </div>

    </div>

    <!-- Right Section - Rooms List -->
    <div class="cell small-12 medium-6 large-4">
        <div id="taskList" class="callout" style="height: 100%;">
            <h5>Tasks  List</h5>
            <ul>
                <?php foreach($tasks as $taskId => $task){?>
                <li>
                    <?=$task['name']?>
                    <br>
                    <!-- <button class="button " style="margin-left: 10px;">Update</button> -->
                    <button id="deleteTaskButton<?=$taskId?>" class="button alert" style="margin-left: 10px;">Delete </button>

                </li>
            <?php } ?>
            </ul>
        </div>
    </div>
<?php } else { ?>
    <div class='cell'>
        <div class='card'>
            <div class='card-section'>
                <h5>You are not a part of any household!</h3>
                <hr>
            </div>
            <div class='card-section'>
                Get an invite link from the owner of the house you wish to join or goto Household to create your own.
            </div>
        </div>
    </div>
<?php } ?>