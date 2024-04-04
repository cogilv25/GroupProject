// Task and Room Tables
let roomNames = [];
let roomIds = [];
let taskNames = [];
let taskDescs = [];
let taskIds = [];
let taskRooms = [];
let taskRoomsOld = [];

//TODO: added descs list but not integrated, add splices and creations where needed.
//TODO: Split static and dynamic js to allow browsers to cache the static parts.
//TODO: The generated js is a lot bigger than neccessary as it fills the arrays
//        one item at a time rather than just creating the full array in one line.

<?php
	$i = 0;
	foreach ($rooms as $roomId => $room)
	{
		$trackRoomIndexes[$i] = $roomId;
		echo("roomIds[" . $i . "] = " . $roomId .";\n");
		echo("roomNames[" . $i . "] = \"" . $room ."\";\n");
		++$i;
	}

	$i = 0;
	foreach ($tasks as $taskId => $task)
	{
		echo("taskIds[" . $i . "] = " . $taskId .";\n");
		echo("taskNames[" . $i . "] = \"" . $task['name'] ."\";\n");
		echo("taskDescs[" . $i . "] = \"" . $task['description'] ."\";\n");
		echo("taskRooms[" . $i . "] = [];\n");
		echo("taskRoomsOld[" . $i . "] = [];\n");
		foreach($task['rooms'] as $roomId => $isAssigned)
		{
			$j = array_search($roomId, $trackRoomIndexes);
			echo("taskRooms[" . $i . "][" . $j . "] = " . ($isAssigned  ? "true":"false") . ";\n");
			echo("taskRoomsOld[" . $i . "][" . $j . "] = " . ($isAssigned  ? "true":"false") . ";\n");
		}
		++$i;
	}
?>

const taskNameInput = document.getElementById("taskName");
const taskDescInput = document.getElementById("taskDescription");
const taskDropdown = document.getElementById("taskDropdown");
const taskList = document.getElementById("taskList");
const jobList =  document.getElementById("taskHasRoomList");

const addButton = document.getElementById("addButton");
const updateButton = document.getElementById("updateAssignedRoomsButton");

function createTask()
{
	let name = taskNameInput.value;
	let desc = taskDescInput.value;
	let formData = "name=" + name + "&description=" + desc;
	$.ajax({
            type: "POST",
            url: "/task/create", // Specify your own URL for the login form
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response) {
					let id = response.data;
					let index = taskIds.length;
					taskIds[index] = id;
					taskNames[index] = name;

					taskRooms[index] = [];
					for(i in roomIds)
					{
						taskRooms[index][i] = false;
					}

					taskNameInput.value = "";
					taskDescInput.value = "";
					renderDropdown();
					renderTaskList();
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

function updateAssignedRooms()
{
	let id = taskDropdown.value;
	//I am completely bewildered why indexOf doesn't work here!
	let taskIndex = taskIds.indexOf(parseInt(id));
	let newTaskRoomsOldAtIndex = [];

	for(i in taskRooms[taskIndex])
	{
		let element = document.getElementById("assignedCheckbox" + roomIds[i]);
		taskRooms[taskIndex][i] = element.checked;
		newTaskRoomsOldAtIndex[i] = element.checked;
	}

	 let formData = "taskId=" + id;

	let counter = 0;

	//Get diff between taskRooms & taskRoomsOld
	for(i in taskRooms[taskIndex])
	{
		if(taskRoomsOld[taskIndex][i]!=taskRooms[taskIndex][i])
		{
			formData += "&op" + counter + "=";
			if(taskRooms[taskIndex][i])
				formData += "1";
			else
				formData += "0";
			formData += "&roomId" + counter + "=" + roomIds[i];
			++counter;
		}
	}
	formData += "&nOps=" + counter;

	// //TODO: Ajax call
	$.ajax({
        type: "POST",
        url: "/task/update_rooms", // Specify your own URL for the login form
        data: formData,
        dataType: "json",
        success: function(response) {
            if (response && response.message) {
				taskRoomsOld[taskIndex] = newTaskRoomsOldAtIndex;
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

function updateTask()
{
	//TODO: @BeforeShipping Not sure where input is coming from in this case
	// let name = "GetInputFromSomewhere";
	// let desc = "GetInputFromSomewhere"
	// let id = 6969;
	// let formData="taskId=" + id + "&name=" + name + "&description=" + desc;
	// $.ajax({
    //         type: "POST",
    //         url: "/task/update", // Specify your own URL for the login form
    //         data: formData,
    //         dataType: "json",
    //         success: function(response) {
    //             if (response && response.message) {
	// 				let index = taskIds.findIndex(function(taskId) { return taskId == id});

	// 				taskNames[index] = name;

	// 				renderDropdown();
	// 				renderTaskList();
	// 				renderJobList();
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

function deleteTask(event)
{
	let id = event.currentTarget.taskId;
	let formData="taskId=" + id;
	$.ajax({
            type: "POST",
            url: "/task/delete", // Specify your own URL for the login form
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response && response.message) {
					let index = taskIds.findIndex(function(taskId) { return taskId == id});

					taskIds.splice(index,1);
					taskNames.splice(index, 1);
					taskRooms.splice(index, 1);

					renderDropdown();
					renderTaskList();
					renderJobList();
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
	taskDropdown.textContent = "";
	for(i in taskIds)
	{
		let option = document.createElement("option");
		option.textContent = taskNames[i];
		option.value = taskIds[i];
		taskDropdown.appendChild(option);
	}
	renderAssignedRooms();
}

function renderTaskList()
{
	taskList.textContent = "";
	for(i in taskIds)
	{
		let listItem = document.createElement("li");
		listItem.textContent = taskNames[i];
		let listItemButton = document.createElement("button");
		listItemButton.classList.add("button","alert");
		listItemButton.textContent = "Delete Task";
		listItemButton.style.marginLeft = "10px";
		listItemButton.taskId = taskIds[i];
		listItemButton.addEventListener("click", deleteTask);
		listItemButton.addEventListener("touch", deleteTask);
		listItem.appendChild(listItemButton);
		taskList.appendChild(listItem);
	}
}

function renderAssignedRooms()
{
	let id = taskDropdown.value;
	let index = taskIds.findIndex(function(taskId) { return taskId == id});

	for(i in taskRooms[index])
	{
		let element = document.getElementById("assignedCheckbox" + roomIds[i]);
		element.checked = taskRooms[index][i];
	}
}

//Carryover terminology from backend Job is a "A Room in a Task"
function renderJobList()
{
	jobList.textContent = "";
	for(r in taskIds)
	{
		for(t in taskRooms[r])
		{
			if(!taskRooms[r][t])
				continue;

			let listItem = document.createElement("li");
			listItem.textContent = taskNames[r] + " + " + roomNames[t];
			let listItemButton = document.createElement("button");
			listItemButton.classList.add("button","alert");
			listItemButton.textContent = "Delete Link";
			listItemButton.style.marginLeft = "10px";
			listItem.appendChild(listItemButton);
			jobList.appendChild(listItem);
		}
	}
}

addButton.addEventListener("click", createTask);
addButton.addEventListener("touch", createTask);

updateButton.addEventListener("click", updateAssignedRooms);
updateButton.addEventListener("touch", updateAssignedRooms);

taskDropdown.addEventListener("change", renderAssignedRooms);

for(i in taskIds)
{
	let element = document.getElementById("deleteTaskButton"+taskIds[i]);
	element.addEventListener("click", deleteTask);
	element.addEventListener("touch", deleteTask);
	element.taskId = taskIds[i];
}