<?php

//TODO: Proper Schema

/*$currentUser = ['role' => 1, 2, or 3, 'userId' => 1, 'homeless' => false];
$users = [
    ['userId' => 1, 'forename' => 'User', 'surname' => '1', 'role' => 'admin', 'email' => 'user1@example.com'],
    ['userId' => 5, 'forename' => 'User', 'surname' => '2', 'role' => 'moderator', 'email' => 'user2@example.com'],
    ['userId' => 8, 'forename' => 'User', 'surname' => '3', 'role' => 'member', 'email' => 'user3@example.com'],
    ['userId' => 34, 'forename' => 'User', 'surname' => '4', 'role' => 'member', 'email' => 'user4@example.com'],
];*/


//TODO: A view for the homeless
if($currentUser['homeless']==true)
{
?>
    <div class='cell'>
        <div class='card'>
            <div class='card-section'>
                You have no home do you want to make one?
                <button id="createHousehold" class=" custombtn success button" type="button">Create Household</a>
            </div>
        </div>
    </div>
<?php
}
else
{

//TODO: Do we want the admin to be able to see users emails or not?
if($currentUser['isAdmin']) //Admin view
{
    foreach ($users as $user):
    ?>
        <div class="cell">
            <div class="card">
                <div class="card-section">
                    <h4><?= htmlspecialchars($user['forename'].' '.$user['surname']) ?></h4>
                    <p>Role: <?= htmlspecialchars($user['role']) ?></p>
                    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
                    <?php if ($user['role'] == 'admin'): ?>
                    <button id="deleteHousehold" type="button" class="custombtn alert button">Delete House</button>
                    <?php else: ?>
                        <button type="button" class=" custombtn alert button">Kick from house</button>
                    <?php endif; ?>
                    <?php if ($user['role'] == 'member'): ?>
                    <button type="button" class=" custombtn success button">Give Moderator privileges</button>
                    <?php elseif ($user['role'] == 'moderator'): ?>
                        <button type="button" class="custombtn alert button">Demote to member</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    endforeach;
}
else //Non-admin view
{
    foreach ($users as $user):
    ?>
        <div class="cell">
            <div class="card">
                <div class="card-section">
                    <h4><?= htmlspecialchars($user['forename'].' '.$user['surname']) ?></h4>
                    <p>Role: <?= htmlspecialchars($user['role']) ?></p>

                    <?php
                    // Allow the user to leave the house, only for non-admins
                    if ($user['userId'] == $currentUser['userId']){ ?>
                    <button id="leaveHousehold" type="button" class="custombtn alert button">Leave House</button>
                    <?php } ?>
                </div>
            </div>
        </div>
<?php
    endforeach;
}
}
?>

<script>
    $('#createHousehold').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'household/create', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Get the updated page
                //TODO: We could probably just update it ourselves but this is the fast and dirty way!
                console.log(response.message);
                $.ajax({
                    url: 'household', // Path to your household.php file
                    type: 'GET', // GET method to fetch data
                    success: function(response) {
                        // Insert the fetched content into the 'household-content' div
                        $('#main-content').html(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        console.error("Error: " + status + " " + error);
                    }
                });
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });

    $('#leaveHousehold').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'household/leave', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Get the updated page
                //TODO: We could probably just update it ourselves but this is the fast and dirty way!
                console.log(response.message);
                $.ajax({
                    url: 'household', // Path to your household.php file
                    type: 'GET', // GET method to fetch data
                    success: function(response) {
                        // Insert the fetched content into the 'household-content' div
                        $('#main-content').html(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        console.error("Error: " + status + " " + error);
                    }
                });
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });

    $('#deleteHousehold').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'household/delete', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Get the updated page
                //TODO: We could probably just update it ourselves but this is the fast and dirty way!
                console.log(response.message);
                $.ajax({
                    url: 'household', // Path to your household.php file
                    type: 'GET', // GET method to fetch data
                    success: function(response) {
                        // Insert the fetched content into the 'household-content' div
                        $('#main-content').html(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        console.error("Error: " + status + " " + error);
                    }
                });
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });
</script>