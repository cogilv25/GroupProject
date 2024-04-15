const roomInput = document.getElementById("roomSelect");
const taskInput = document.getElementById("taskSelect");

function submitRoomRule()
{
	let formData = "roomId=" + roomInput.value;
	<?php if($currentUser['role'] != 'member') { ?>
	formData += "&userId=" + document.getElementById("roomUserSelect").value;
	<?php } ?>
	$.ajax({
            type: "POST",
            url: "/rule/create/user_room", // Specify your own URL for the login form
            data: formData,
            success: function(response) {
                if (!response)
                {
                    console.error("Error: Invalid response format");
                }
                else
                {
                    window.location.href = "/rule";
                }
            },
            error: function(xhr, status, error)
            {
                console.error(status, error);
            }
        });
}

function submitTaskRule()
{
	let formData = "taskId=" + taskInput.value;
	<?php if($currentUser['role'] != 'member') { ?>
	formData += "&userId=" + document.getElementById("taskUserSelect").value;
	<?php } ?>
	$.ajax({
            type: "POST",
            url: "/rule/create/user_task", // Specify your own URL for the login form
            data: formData,
            success: function(response) {
                if (!response)
                {
                    console.error("Error: Invalid response format");
                }
                else
                {
                    window.location.href = "/rule";
                }
            },
            error: function(xhr, status, error)
            {
                console.error(status, error);
            }
        });
}