<!-- Input schema 

{value} donates a value from the database

$currentUser =
[
    'homeless' = {userHasHome}, // Boolean
    'userId' = {userId},
    'isAdmin' = {userIsAdmin} // Boolean
]

$rooms =
[
    {roomId1} = {roomName1},
    {roomId2} = {roomName2},
    {roomId3} = {roomName3},
    {roomId4} = {roomName4},
]

$tasks =
[ 
   {taskId1} => 
   [ 
        'name' => {taskName1},
        'description' => {taskDesc1},
        'rooms' =>                  // If a roomId row is true then the  
        [                           // task is performed in the room.
            {room1Id} => true,
            {room2Id} => true,                              
            {room3Id} => false,
            {room4Id} => false
        ]
    ], 
    {taskId2} => 
    [
        'name' => {taskName2},
        'description' => {taskDesc2},
        'rooms' =>
        [
            {room1Id} => false,
            {room2Id} => true,
            {room3Id} => false,
            {room4Id} => true
        ]
    ]
]

TODO:   Handle php input        -->

        <div class="cell small-12 medium-6 large-auto">
            <!-- Room Name and Input Button -->
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                    <label for="TaskName">Task Name</label>
                    <input type="text" id="TaskName" placeholder="Enter Task Name">
                    <label for="TaskDesc">Task Name</label>
                    <input type="text" id="TaskDesc" placeholder="Enter Task description">
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
                    <label for="taskDropdown">Select Task</label>
                    <select id="taskDropdown">
                        <option value="room1">Task: Hoovering</option>
                        <option value="room2">Task: Washing up dishes</option>
                    </select>
                </div>
            </div>

            <!-- Assign Tasks -->
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                    <fieldset class="fieldset">
                        <legend>Assign Rooms</legend>
                        <!--loop for room and change id to have room id -->
                        <input id="room1" type="checkbox"><label for="room1">Living Room</label><br>
                        <input id="room2" type="checkbox"><label for="room2">Kitchen</label><br>
                    </fieldset>
                </div>
            </div>
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                <button class="button" type="button"  style="margin-top: 1.5rem; width:100%;">Assign Room to task</button>
                </div>
            </div>


            <div class="cell small-12 medium-6 large-4">
                <div class="callout" style="height: 100%;">
                    <h5>Whats Task has a room</h5>
                    <ul>
                        <li>
                        Hoovering in Living Room
                        <br>
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Link</button>
                        </li>
                        <li>
                        Washing up dishes in Kitchen
                        <br>
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Link</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Right Section - Rooms List -->
        <div class="cell small-12 medium-6 large-4">
            <div class="callout" style="height: 100%;">
                <h5>Tasks  List</h5>
                <ul>
                    <li>
                        Hoovering 
                        <br>
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete </button>

                    </li>
                    <li>
                        Washing up dishes
                        <br>
                        <button class="button" style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete</button>

                    </li>
                </ul>
            </div>
        </div>
