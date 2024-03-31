// Room and Task Tables
let taskNames = [];
let taskIds = [];
let roomNames = [];
let roomIds = [];
let roomTasks = [];
let roomTasksOld = [];


//TODO: The generated js is a lot bigger than neccessary as it fills
//        the arrays one item at a time rather than just creating the
//        
<?php
	$i = 0;
	foreach ($tasks as $taskId => $task)
	{
		$trackTaskIndexes[$i] = $taskId;
		echo("taskIds[" . $i . "] = " . $taskId .";\n");
		echo("taskNames[" . $i . "] = \"" . $task ."\";\n");
		++$i;
	}

	$i = 0;
	foreach ($rooms as $roomId => $room)
	{
		echo("roomIds[" . $i . "] = " . $roomId .";\n");
		echo("roomNames[" . $i . "] = \"" . $room['name'] ."\";\n");
		echo("roomTasks[" . $i . "] = [];\n");
		echo("roomTasksOld[" . $i . "] = [];\n");
		foreach($room['tasks'] as $taskId => $isAssigned)
		{
			$j = array_search($taskId, $trackTaskIndexes);
			echo("roomTasks[" . $i . "][" . $j . "] = " . ($isAssigned  ? "true":"false") . ";\n");
			echo("roomTasksOld[" . $i . "][" . $j . "] = " . ($isAssigned  ? "true":"false") . ";\n");
		}
		++$i;
	}
?>

const roomNameInput = document.getElementById("roomName");
const roomDropdown = document.getElementById("roomDropdown");
const roomList = document.getElementById("roomList");

const addButton = document.getElementById("addButton");
const updateButton = document.getElementById("updateAssignedTasksButton");

function createRoom()
{
	let name = roomNameInput.value;
	let formData = "name=" + name;
	$.ajax({
            type: "POST",
            url: "/room/create", // Specify your own URL for the login form
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response) {
					let id = response.data;
					let index = roomIds.length;
					roomIds[index] = id;
					roomNames[index] = name;

					roomTasks[index] = [];
					for(i in taskIds)
					{
						roomTasks[index][i] = false;
					}

					roomNameInput.value = "";
					renderDropdown();
					renderRoomList();
                } else {
                    console.error("Error: Invalid response format");
                }
            },
            error: function(xhr, status, error) {
                try {
                	//TODO: User Error
                    response = JSON.parse(xhr.responseText);
                    console.error("Error: ", response.message);
                } catch (e) {
                    console.error("Error submitting login form: ", error);
                }
            }
        });
}

function updateAssignedTasks()
{
	let id = roomDropdown.value;
	//I am completely bewildered why indexOf doesn't work here!
	let roomIndex = roomIds.indexOf(parseInt(id));
	let newRoomTasksOldAtIndex = [];

	for(i in roomTasks[roomIndex])
	{
		let element = document.getElementById("assignedCheckbox" + taskIds[i]);
		roomTasks[roomIndex][i] = element.checked;
		newRoomTasksOldAtIndex[i] = element.checked;
	}

	 let formData = "roomId=" + id;

	let counter = 0;

	//Get diff between roomTasks & roomTasksOld
	for(i in roomTasks[roomIndex])
	{
		if(roomTasksOld[roomIndex][i]!=roomTasks[roomIndex][i])
		{
			formData += "&op" + counter + "=";
			if(roomTasks[roomIndex][i])
				formData += "1";
			else
				formData += "0";
			formData += "&taskId" + counter + "=" + taskIds[i];
			++counter;
		}
	}
	formData += "&nOps=" + counter;

	// //TODO: Ajax call
	$.ajax({
        type: "POST",
        url: "/room/update_tasks", // Specify your own URL for the login form
        data: formData,
        dataType: "json",
        success: function(response) {
            if (response && response.message) {
				roomTasksOld[roomIndex] = newRoomTasksOldAtIndex;
            } else {
                console.error("Error: Invalid response format");
            }
        },
        error: function(xhr, status, error) {
            try {
            	//TODO: User Error
                response = JSON.parse(xhr.responseText);
                console.error("Error: ", response.message);
            } catch (e) {
                console.error("Error submitting login form: ", error);
            }
        }
    });
}

function updateRoom()
{
	//TODO: @BeforeShipping Not sure where input is coming from in this case
	// let name = "GetInputFromSomewhere";
	// let id = 6969;
	// let formData="roomId=" + id + "&name=" + name;
	// $.ajax({
    //         type: "POST",
    //         url: "/room/update", // Specify your own URL for the login form
    //         data: formData,
    //         dataType: "json",
    //         success: function(response) {
    //             if (response && response.message) {
	// 				let index = roomIds.findIndex(function(roomId) { return roomId == id});

	// 				roomNames[index] = name;

	// 				renderDropdown();
	// 				renderRoomList();
    //             } else {
    //                 console.error("Error: Invalid response format");
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             try {
    //             	//TODO: User Error
    //                 response = JSON.parse(xhr.responseText);
    //                 console.error("Error: ", response.message);
    //             } catch (e) {
    //                 console.error("Error submitting login form: ", error);
    //             }
    //         }
    //     });
}

function deleteRoom(event)
{
	let id = event.currentTarget.roomId;
	let formData="roomId=" + id;
	$.ajax({
            type: "POST",
            url: "/room/delete", // Specify your own URL for the login form
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response && response.message) {
					let index = roomIds.findIndex(function(roomId) { return roomId == id});

					roomIds.splice(index,1);
					roomNames.splice(index, 1);
					roomTasks.splice(index, 1);

					renderDropdown();
					renderRoomList();
                } else {
                    console.error("Error: Invalid response format");
                }
            },
            error: function(xhr, status, error) {
                try {
                	//TODO: User Error
                    response = JSON.parse(xhr.responseText);
                    console.error("Error: ", response.message);
                } catch (e) {
                    console.error("Error submitting login form: ", error);
                }
            }
        });
}

function renderDropdown()
{
	roomDropdown.textContent = "";
	for(i in roomIds)
	{
		let option = document.createElement("option");
		option.textContent = roomNames[i];
		option.value = roomIds[i];
		roomDropdown.appendChild(option);
	}
}

function renderRoomList()
{
	roomList.textContent = "";
	for(i in roomIds)
	{
		let listItem = document.createElement("li");
		listItem.textContent = roomNames[i];
		let listItemButton = document.createElement("button");
		listItemButton.classList.add("button","alert");
		listItemButton.textContent = "Delete Room";
		listItemButton.style.marginLeft = "10px";
		listItemButton.roomId = roomIds[i];
		listItemButton.addEventListener("click", deleteRoom);
		listItemButton.addEventListener("touch", deleteRoom);
		listItem.appendChild(listItemButton);
		roomList.appendChild(listItem);
	}
}

function renderAssignedTasks()
{
	let id = roomDropdown.value;
	let index = roomIds.findIndex(function(roomId) { return roomId == id});

	for(i in roomTasks[index])
	{
		let element = document.getElementById("assignedCheckbox" + taskIds[i]);
		element.checked = roomTasks[index][i];
	}
}

addButton.addEventListener("click", createRoom);
addButton.addEventListener("touch", createRoom);

updateButton.addEventListener("click", updateAssignedTasks);
updateButton.addEventListener("touch", updateAssignedTasks);

roomDropdown.addEventListener("change", renderAssignedTasks);

for(i in roomIds)
{
	let element = document.getElementById("deleteRoomButton"+roomIds[i]);
	element.addEventListener("click", deleteRoom);
	element.addEventListener("touch", deleteRoom);
	element.roomId = roomIds[i];
}