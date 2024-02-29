<?php
$users = [
    ['name' => 'User 1', 'role' => 'admin', 'email' => 'user1@example.com'],
    ['name' => 'User 2', 'role' => 'moderator', 'email' => 'user2@example.com'],
    ['name' => 'User 3', 'role' => 'member', 'email' => 'user3@example.com'],
    ['name' => 'User 4', 'role' => 'member', 'email' => 'user4@example.com'],
];

foreach ($users as $user):
    ?>
        <div class="cell">
            <div class="card">
                <div class="card-section">
                    <h4><?= htmlspecialchars($user['name']) ?></h4>
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
    ?>