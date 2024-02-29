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

        $loggedIn = $request->getAttribute('loggedIn');
        if(!$loggedIn)
            return $renderer->render($response, 'Authpage.html');
        else
            return $response->withHeader('Location', '/Dashboard.php')->withStatus(302);
    })->add(AuthenticationMiddleware::class);


    $app->get('/Dashboard.php', function (Request $request, Response $response) {
        $loggedIn = $request->getAttribute('loggedIn');
        if(!$loggedIn)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($loggedIn['userId']);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'Dashboard.php', $data);
    })->add(AuthenticationMiddleware::class);

    //User Actions
    $app->post('/login', User\LoginAction::class)->add(AuthenticationMiddleware::class);
    $app->post('/signup', User\RegisterAction::class)->add(AuthenticationMiddleware::class);
    $app->get("/logout", function(Request $request, Response $response) {
        session_unset();
        session_destroy();
        return $response->withHeader('Location', '/')->withStatus(302);
    });

    //Schedule Actions
    $app->group('/schedule', function (Group $group)
    {
        $group->post('/create_row', Schedule\CreateScheduleRowAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/update_row', Schedule\UpdateScheduleRowAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/delete_row', Schedule\DeleteScheduleRowAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/delete', Schedule\DeleteScheduleAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/list', Schedule\GetScheduleAction::class)->add(AuthenticationMiddleware::class);
    });

    //HouseHold Actions
    $app->group('/household', function (Group $group)
    {
        $group->get('/create', HouseHold\CreateHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/join/{id}', HouseHold\JoinHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/delete', HouseHold\DeleteHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/leave', HouseHold\LeaveHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/remove', Household\RemoveUserHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/list', Household\ListHouseholdAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/schedules', Schedule\GetHouseholdSchedulesAction::class)->add(AuthenticationMiddleware::class);
    });

    //Room Actions
    $app->group('/room', function (Group $group)
    {
        $group->post('/create', Room\CreateRoomAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/update', Room\UpdateRoomAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/delete', Room\DeleteRoomAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/list', Room\ListRoomAction::class)->add(AuthenticationMiddleware::class);
    });

    //Task Actions
    $app->group('/task', function (Group $group)
    {
        $group->post('/create', Task\CreateTaskAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/update', Task\UpdateTaskAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/delete', Task\DeleteTaskAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/list', Task\ListTaskAction::class)->add(AuthenticationMiddleware::class);
    });

    //Rule Actions
    $app->group('/rule', function (Group $group)
    {
        $group->group('/create', function (Group $createGroup)
            {
                $createGroup->post('/room_time', Rule\CreateRoomTimeRuleAction::class)->add(AuthenticationMiddleware::class);
                $createGroup->post('/task_time', Rule\CreateTaskTimeRuleAction::class)->add(AuthenticationMiddleware::class);
                $createGroup->post('/user_task', Rule\CreateUserTaskRuleAction::class)->add(AuthenticationMiddleware::class);
                $createGroup->post('/user_room', Rule\CreateUserRoomRuleAction::class)->add(AuthenticationMiddleware::class);
            });
        $group->post('/delete', Rule\DeleteRuleAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/list', Rule\ListRuleAction::class)->add(AuthenticationMiddleware::class);
    });
};
