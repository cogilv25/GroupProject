$(document).ready(function () {
    $(document).foundation();
    $('.toggle-group').click(function () {
        $(this).toggleClass('up'); // Toggle the 'up' class
        $(this).closest('tr').nextUntil('.task-group-header').toggle();
    });
});
