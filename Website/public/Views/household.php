<?php

//TODO: Proper Schema

/*
$currentUser = ['role' => ('member', 'admin', or 'owner'), 'userId' => 1, 'homeless' => false];
$users = [
    ['userId' => 1, 'forename' => 'User', 'surname' => '1', 'role' => 'admin', 'email' => 'user1@example.com'],
    ['userId' => 5, 'forename' => 'User', 'surname' => '2', 'role' => 'moderator', 'email' => 'user2@example.com'],
    ['userId' => 8, 'forename' => 'User', 'surname' => '3', 'role' => 'member', 'email' => 'user3@example.com'],
    ['userId' => 34, 'forename' => 'User', 'surname' => '4', 'role' => 'member', 'email' => 'user4@example.com'],
];*/


//TODO: Promote and Kick button functionallity

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
if($currentUser['role'] == 'owner') //Owner view
{
    foreach ($users as $user):
    ?>
        <div class="cell">
            <div class="card">
                <div class="card-section">
                    <h4><?= htmlspecialchars($user['forename'].' '.$user['surname']) ?></h4>
                    <p>Role: <?= htmlspecialchars($user['role']) ?></p>
                    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <?php   if ($user['role'] == 'owner')
                    { ?>
                    <button onclick="deleteHouse();" type="button" class="custombtn alert button">Delete House</button>
            <?php   } else { ?>
                        <button onclick="kickUser(<?=$user['userId']?>);" type="button" class=" custombtn alert button">Kick</button>
            <?php       if ($user['role'] == 'member') { ?>
                        <button onclick="promoteUser(<?=$user['userId']?>);" type="button" class=" custombtn success button">Promote</button>
            <?php       } else { ?>
                            <button onclick="demoteUser(<?=$user['userId']?>);" type="button" class="custombtn alert button">Demote</button>
            <?php       } 
                    } ?>
                </div>
            </div>
        </div>
<?php
    endforeach;
}
elseif($currentUser['role'] == 'admin')
{
    foreach ($users as $user):
    ?>
        <div class="cell">
            <div class="card">
                <div class="card-section">
                    <h4><?= htmlspecialchars($user['forename'].' '.$user['surname']) ?></h4>
                    <p>Role: <?= htmlspecialchars($user['role']) ?></p>
            <?php   // Allow the user to leave the house, only for non-owners
                    if ($user['userId'] == $currentUser['userId'])
                    { ?>
                        <button onclick="leaveHouse();" type="button" class="custombtn alert button">Leave House</button>
            <?php   } ?>
            <?php   if ($user['role'] == 'member') 
                    { ?>
                        <button onclick="kickUser(<?=$user['userId']?>);" type="button" class=" custombtn alert button">Kick</button>
            <?php   } ?>
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
                    // Allow the user to leave the house, only for non-owners
                    if ($user['userId'] == $currentUser['userId']){ ?>
                    <button onclick="leaveHouse();" type="button" class="custombtn alert button">Leave House</button>
                    <?php } ?>
                </div>
            </div>
        </div>
<?php
    endforeach;
}
}
?>
<!-- TODO: Move script to a seperate file and include with php -->
<script>
    function kickUser(userId)
    {
        $.ajax({
            url: 'household/remove', // Path to your household.php file
            type: 'POST', // GET method to fetch data
            data: 'userId='+userId,
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
                console.log(xhr.responseText);
            }
        });
    }

    function promoteUser(userId)
    {
        $.ajax({
            url: 'household/promote', // Path to your household.php file
            type: 'POST', // GET method to fetch data
            data: 'userId='+userId,
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    }

    function demoteUser(userId)
    {
        $.ajax({
            url: 'household/demote', // Path to your household.php file
            type: 'POST', // GET method to fetch data
            data: 'userId='+userId,
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    }

    function createHouse() {
        $.ajax({
            url: 'household/create', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    }

    function leaveHouse() {
        $.ajax({
            url: 'household/leave', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    }

    function deleteHouse() {
        $.ajax({
            url: 'household/delete', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    }
</script>