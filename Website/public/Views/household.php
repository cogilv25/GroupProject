<?php

//No Longer needed but it's maybe nice to have as a reminder of what the input data looks like..?

/*$currentUser = ['isAdmin' => true, 'userId' => 1, 'homeless' => false];
$users = [
    ['userId' => 1, 'forename' => 'User', 'surname' => '1', 'role' => 'admin', 'email' => 'user1@example.com'],
    ['userId' => 5, 'forename' => 'User', 'surname' => '2', 'role' => 'moderator', 'email' => 'user2@example.com'],
    ['userId' => 8, 'forename' => 'User', 'surname' => '3', 'role' => 'member', 'email' => 'user3@example.com'],
    ['userId' => 34, 'forename' => 'User', 'surname' => '4', 'role' => 'member', 'email' => 'user4@example.com'],
];*/


//TODO: A view for the homeless
if($currentUser['homeless']==true)
    echo "<div class='cell'><div class='card'><div class='card-section'><a href='/household/create'>You have no home do you want to make one?</a></div></div></div>";
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
                    <button type="button" class="custombtn alert button">Leave House</button>
                    <?php elseif ($user['role'] == 'moderator'): ?>
                        <button type="button" class=" custombtn alert button">Kick from house</button>
                    <?php endif; ?>
                    <?php if ($user['role'] == 'member'): ?>
                        <button type="button" class="custombtn alert button">Kick from house</button>
                    <button type="button" class=" custombtn success button">Give Moderator privileges</button>
                    <?php else: ?>
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
                    //TODO: I added the currentUser as input from Slim thinking
                    //        you could use it to let the user leave the house? 
                    //if ($user['userId'] == currentUser['userId']): ?>
                    <!-- <button type="button" class="custombtn alert button">Leave House</button> -->
                    <?php //endif ?>
                </div>
            </div>
        </div>
<?php
    endforeach;
}
}
?>