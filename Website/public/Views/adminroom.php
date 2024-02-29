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
                        <option value="room1">Room 1</option>
                        <option value="room2">Room 2</option>
                    </select>
                </div>
            </div>

            <!-- Assign Tasks -->
            <div class="grid-x grid-padding-x align-middle">
                <div class="cell small-12">
                    <fieldset class="fieldset">
                        <legend>Assign Tasks</legend>
                        <input id="task1" type="checkbox"><label for="task1">Task 1</label><br>
                        <input id="task2" type="checkbox"><label for="task2">Task 2</label><br>
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
                <h5>Whats room has tasks</h5>
                <ul>
                    <li>
                        Living Room + Hoovering
                        <button class="button" style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Link</button>
                    </li>
                    <li>
                        Kitchen + Washing Dishes
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert " style="margin-left: 10px;">Delete Link</button>
                    </li>
                    <!-- Add more tasks and rooms as needed -->
                </ul>
            </div>
        </div>

        </div>

        <div class="cell small-12 medium-6 large-4">
            <div class="callout" style="height: 100%;">
                <h5>Room</h5>
                <ul>
                    <li>
                        Living Room
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Room</button>

                    </li>
                    <li>
                        Kitchen
                        <button class="button " style="margin-left: 10px;">Update</button>
                        <button class="button alert" style="margin-left: 10px;">Delete Room</button>

                    </li>
                    <!-- Add more tasks and rooms as needed -->
                </ul>
            </div>
        </div>