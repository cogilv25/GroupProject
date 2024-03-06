<?php

declare(strict_types=1);

use App\Application\Actions\HouseHold;
use App\Application\Actions\User;
use App\Application\Actions\Room;
use App\Application\Actions\Task;
use App\Application\Actions\Rule;
use App\Application\Actions\Schedule;

use App\Application\Middleware\AuthenticationMiddleware;
use App\Application\Middleware;

use Slim\Exception\HttpUnauthorizedException;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    //TODO: Single page logic
   $app->map(['GET', 'POST'], '/', function (Request $request, Response $response) {
        $renderer = $this->get('renderer'); 

        $userId = $request->getAttribute('userId');
        if($userId==0)
            return $renderer->render($response, 'Authpage.html');
        else
            return $response->withHeader('Location', '/Dashboard.php')->withStatus(302);
    });


    $app->get('/Dashboard.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'Dashboard.php', $data);
    });


    $app->get('/admindashboard.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'admindashboard.php', $data);
    });


    $app->get('/adminhousehold.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'adminhousehold.php', $data);
    });


    

    $app->get('/adminroom.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'adminroom.php', $data);
    });

    $app->get('/rules.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'rules.php', $data);
    });
    $app->get('/addrule.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'addrule.php', $data);
    });


    $app->get('/adminTasks.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'adminTasks.php', $data);
    });

    $app->get('/adminRules.php', function (Request $request, Response $response) {
        $userId = $request->getAttribute('userId');
        if($userId==0)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($userId);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'adminRules.php', $data);
    });

    //User Actions
    $app->post('/login', User\LoginAction::class);
    $app->post('/signup', User\RegisterAction::class);
    $app->get("/logout", function(Request $request, Response $response) {
        session_unset();
        session_destroy();
        return $response->withHeader('Location', '/')->withStatus(302);
    });

    //UserSchedule Actions
    $app->group('/schedule', function (Group $group)
    {
        $group->post('/create_row', Schedule\CreateUserScheduleRowAction::class);
        $group->post('/update_row', Schedule\UpdateUserScheduleRowAction::class);
        $group->post('/delete_row', Schedule\DeleteUserScheduleRowAction::class);
        $group->get('/delete', Schedule\DeleteUserScheduleAction::class);
        $group->get('/list', Schedule\GetUserScheduleAction::class);
    });

    //HouseHold Actions
    $app->group('/household', function (Group $group)
    {
        $group->get('/create', HouseHold\CreateHouseHoldAction::class);
        $group->get('/join/{id}', HouseHold\JoinHouseHoldAction::class);
        $group->get('/delete', HouseHold\DeleteHouseHoldAction::class);
        $group->get('/leave', HouseHold\LeaveHouseHoldAction::class);
        $group->post('/remove', Household\RemoveUserHouseHoldAction::class);
        $group->get('/list', Household\ListHouseholdAction::class);
        $group->get('/schedules', Schedule\GetHouseholdUserSchedulesAction::class);
    });

    //Room Actions
    $app->group('/room', function (Group $group)
    {
        $group->post('/create', Room\CreateRoomAction::class);
        $group->post('/update', Room\UpdateRoomAction::class);
        $group->post('/delete', Room\DeleteRoomAction::class);
        $group->get('/list', Room\ListRoomAction::class);
    });

    //Task Actions
    $app->group('/task', function (Group $group)
    {
        $group->post('/create', Task\CreateTaskAction::class);
        $group->post('/update', Task\UpdateTaskAction::class);
        $group->post('/delete', Task\DeleteTaskAction::class);
        $group->get('/list', Task\ListTaskAction::class);
    });

    //Rule Actions
    $app->group('/rule', function (Group $group)
    {
        $group->group('/create', function (Group $createGroup)
            {
                $createGroup->post('/room_time', Rule\CreateRoomTimeRuleAction::class);
                $createGroup->post('/task_time', Rule\CreateTaskTimeRuleAction::class);
                $createGroup->post('/user_task', Rule\CreateUserTaskRuleAction::class);
                $createGroup->post('/user_room', Rule\CreateUserRoomRuleAction::class);
            });
        $group->post('/delete', Rule\DeleteRuleAction::class);
        $group->get('/list', Rule\ListRuleAction::class);
    });
};
