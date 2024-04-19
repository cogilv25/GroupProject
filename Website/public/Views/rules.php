<?php 
/*
$rules = [
    [
        'forename' => 'John',
        'surname' => 'Smith',
        'name' => 'Hoovering',
        'justification' => 'John really doesn't like hoovering and so implores the admins to take pity upon him.',
        'type' => 'user_task',
        'ruleId' => 1,
        'active' => true
    ],
    // Add more rules as needed
];

Example Data:
$rules = [
    ['forename' => 'Billy', 'surname' => 'Bob', 'name' => 'Living Room', just' => 'He is a ghost and bemoans entering it.', 
        'type' => 'user_room', 'ruleId' => 1];
];

*/
$staticScripts = ['Javascript/rule.js'];
?>

<div class="cell small-12" style="width: 100%;">
    <div class="add-task-button-container">
        <a href="rule/create" class="button" id="addTaskButton">Add Rule</a>
    </div>
    <div class="table-wrapper">
        <table class="unstriped custom-table" >
            <thead>
                <tr>
                    <th width="100">Member</th>
                    <th width="150">Rule</th>
                    <th width="300">Justification</th>
                    <?php if($currentUser['role'] != 'member') { ?>
                    <th width="50">Enabled</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach($rules as $id => $rule) { 
                $name = $rule['forename'] . " " . $rule['surname'];
                $desc = $rule['type'] == "user_task" ? " is exempt from " : " does not clean the ";?>
                <tr>
                    <td><?=$name?></td>
                    <td><?=$name . $desc . $rule['name']?></td>
                    <td><?=$rule['just']?></td>
                    <?php if($currentUser['role'] != 'member') { ?>
                    <td><input rule_type="<?=$rule['type']?>" rule_id="<?=$rule['ruleId']?>" onchange="onRuleCheckboxToggled(event)" type="checkbox" id="check1" <?=$rule['active']?"checked":""?>></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
           