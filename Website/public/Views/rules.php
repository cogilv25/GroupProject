<?php 
/*
$rulesData = [
    [
        'Members' => 'Test 1',
        'RuleName' => 'No hoovering at 15:00',
        'TypeOfRule' => 'User + Task',
    ],
    // Add more rules as needed
    [
        'Members' => 'Test 2',
        'RuleName' => 'No cooking after 22:00',
        'TypeOfRule' => 'User + Room',
    ],
];

Example Data:
$adminruleData = [
    ['userId' => 1, 'rulename' => 'No hoovering at 15:00', 'TypeOfRule' => 'User + Task']
];
*/
?>

<div class="cell small-12" style="width: 100%;">
    <div class="add-task-button-container">
        <a href="rule/create" class="button" id="addTaskButton">Add Rule</a>
    </div>
    <div class="table-wrapper">
        <table class="unstriped custom-table" >
            <thead>
                <tr>
                <th width="200">Members</th>
                <th width="200">Rule Name</th>
                <th width="200">Type of Rule</th>
                <th width="150">Enable</th>
                <th width="150">Disable</th>
                <th width="150">Modify</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>Test 1</td>
                <td>No hoovering at 15:00</td>
                <td>User + Task</td>
                <td><input type="checkbox" name="check1"></td>
                <td><input type="checkbox" name="check2"></td>
                <td><input type="checkbox" name="check3"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
           