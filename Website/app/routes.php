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

    //Serves the login page for visitors, the dashboard for logged in members and
    //    the admin dashboard for logged in admins
    $app->map(['GET', 'POST'], '/', function (Request $request, Response $response) {
        $renderer = $this->get('renderer');
        $userId = $request->getAttribute('userId');

        if($userId==0)
            return $renderer->render($response, 'authpage.html');
        else
        {
            $db = $this->get('db');
            $link = $db->getUserInviteLink($userId);
            $houseRole = $db->getUserHouseAndRole($userId);
                 if ($houseRole === false) {
            // User is "homeless", i.e., not part of any household
            $data = [
                'currentUser' => ['userId' => $userId, 'homeless' => true],
                'link' => $link
            ];
            $dashboard = "dashboard.php";
        } else {
            // User is part of a household
            $data = ['currentUser' => ['userId' => $userId,'role' => $houseRole[1],'homeless' => false ],'link' => $link];
            $dashboard = ($link == "No Link") ? "dashboard.php" : "admindashboard.php";        }
        
            return $renderer->render($response, $dashboard, $data);
        }
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
    $app->group('/schedule', function (Group $group) {
        $group->get('', function (Request $request, Response $response) {
            $userId = $request->getAttribute('userId');
            if ($userId == 0)
                return $response->withHeader('Location', '/')->withStatus(302);
    
            $renderer = $this->get('renderer');
            $db = $this->get('db');
            $link = $db->getUserInviteLink($userId);
            $houseRole = $db->getUserHouseAndRole($userId);
    
            if ($houseRole === false) {
                // User is "homeless", i.e., not part of any household
                $data = [
                    'currentUser' => ['userId' => $userId, 'homeless' => true],
                    'link' => $link,
                    'page' => "schedule.php"
                ];
                $dashboard = "dashboard.php"; // Default dashboard for homeless users
            } else {
                // User is part of a household
                $data = [
                    'currentUser' => [
                        'userId' => $userId,
                        'role' => $houseRole[1],
                        'homeless' => false
                    ],
                    'link' => $link,
                    'page' => "schedule.php"
                ];
                $dashboard = ($link == "No Link") ? "dashboard.php" : "admindashboard.php";
            }
            return $renderer->render($response, $dashboard, $data);
        });
        $group->post('/create_row', Schedule\CreateUserScheduleRowAction::class);
        $group->post('/update_row', Schedule\UpdateUserScheduleRowAction::class); // Updates a single row
        $group->post('/delete_row', Schedule\DeleteUserScheduleRowAction::class);
        $group->post('/update', Schedule\UpdateUserScheduleAction::class);        // Updates the whole schedule
        $group->get('/delete', Schedule\DeleteUserScheduleAction::class);
        $group->get('/data', Schedule\GetUserScheduleAction::class);
    });

    //HouseHold Actions
    $app->group('/household', function (Group $group)
    {
        $group->get('', function(Request $request, Response $response) {
            $renderer = $this->get('renderer');
            $userId = $request->getAttribute('userId');

            if($userId==0)
                return $response->withHeader('Location', '/')->withStatus(302);

            $db = $this->get('db');
            $invite = $db->getUserInviteLink($userId);
            $houseRole = $db->getUserHouseAndRole($userId);

            if($houseRole === false)
            {
                $data = ['currentUser' => ['userId' => $userId, 'homeless' => true]];
                $dashboard = "dashboard.php";
            }
            else
            {
                $data = ['users' => $db->getUsersInHousehold($houseRole[0])];
                $data['currentUser'] = ['userId' => $userId, 'role' => $houseRole[1], 'homeless' => false ];
                $dashboard = ($houseRole[1] == "member") ? "dashboard.php" : "admindashboard.php";
            }

            $data['page'] = "household.php";
            $data['link'] = $invite;

            return $renderer->render($response, $dashboard, $data);
        });
        $group->get('/create', HouseHold\CreateHouseHoldAction::class);
        //TODO: Unique codes for household join links
        $group->get('/join/{id}/{uuid}', HouseHold\JoinHouseHoldAction::class);
        $group->get('/delete', HouseHold\DeleteHouseHoldAction::class);
        $group->get('/leave', HouseHold\LeaveHouseHoldAction::class);
        $group->post('/transfer', HouseHold\TransferHouseHoldAction::class);
        $group->post('/remove', Household\RemoveUserHouseHoldAction::class);
        $group->post('/promote', Household\PromoteUserHouseHoldAction::class);
        $group->post('/demote', Household\DemoteUserHouseHoldAction::class);
        $group->get('/data', Household\ListHouseholdAction::class);
        $group->get('/schedules', Schedule\GetHouseholdUserSchedulesAction::class);
        $group->get('/gen_rota', Household\RotaGenAction::class);
    });

    //Room Actions
    $app->group('/room', function (Group $group)
    {
        $group->get('', function(Request $request, Response $response) {
            $userId = $request->getAttribute('userId');
            if($userId==0)
                return $response->withHeader('Location', '/')->withStatus(302);

            $renderer = $this->get('renderer');
            $db = $this->get('db');
            $invite = $db->getUserInviteLink($userId);
            $houseRole = $db->getUserHouseAndRole($userId);

            if($houseRole === false)
            {
                $data = ['rooms' => [], 'tasks' => []];
                $data['currentUser'] = ['userId' => $userId, 'homeless' => true];
                $dashboard = "dashboard.php";
            }
            else
            {
                $data = $db->getHouseholdRoomDetails($houseRole[0]);
                $data['currentUser'] = ['userId' => $userId, 'role' => $houseRole[1], 'homeless' => false ];
                $dashboard = ($houseRole[1] == "member") ? "dashboard.php" : "admindashboard.php";
            }

            $data['page'] = 'adminroom.php';
            $data['link'] = $invite;

            return $renderer->render($response, $dashboard, $data);
        });
        $group->post('/create', Room\CreateRoomAction::class);
        $group->post('/update', Room\UpdateRoomAction::class);
        $group->post('/delete', Room\DeleteRoomAction::class);
        $group->get('/data', Room\ListRoomAction::class);
        $group->post('/update_tasks', Room\UpdateTasksAction::class);
    });

    //Task Actions
    $app->group('/task', function (Group $group)
    {
        $group->get('', function(Request $request, Response $response) {
            $userId = $request->getAttribute('userId');
            if($userId==0)
                return $response->withHeader('Location', '/')->withStatus(302);

            $renderer = $this->get('renderer');
            $db = $this->get('db');
            $invite = $db->getUserInviteLink($userId);
            $houseRole = $db->getUserHouseAndRole($userId);

            if($houseRole === false)
            {
                $data = ['rooms' => [], 'tasks' => []];
                $data['currentUser'] = ['userId' => $userId, 'homeless' => true];
                $dashboard = "dashboard.php";
            }
            else
            {
                $data = $db->getHouseholdTaskDetails($houseRole[0]);
                $data['currentUser'] = ['userId' => $userId, 'role' => $houseRole[1], 'homeless' => false ];
                $dashboard = ($houseRole[1] == "member") ? "dashboard.php" : "admindashboard.php";
            }

            $data['page'] = 'adminTasks.php';
            $data['link'] = $invite;

            return $renderer->render($response, $dashboard, $data);
        });
        $group->post('/create', Task\CreateTaskAction::class);
        $group->post('/update', Task\UpdateTaskAction::class);
        $group->post('/delete', Task\DeleteTaskAction::class);
        $group->get('/data', Task\ListTaskAction::class);
        $group->post('/update_rooms', Task\UpdateRoomsAction::class);
    });

    //Rule Actions
    $app->group('/rule', function (Group $group)
    {
        $group->get('', function(Request $request, Response $response) {
            $userId = $request->getAttribute('userId');
            if($userId==0)
                return $response->withHeader('Location', '/')->withStatus(302);

            $renderer = $this->get('renderer');
            $db = $this->get('db');
            $invite = $db->getUserInviteLink($userId);
            $houseRole = $db->getUserHouseAndRole($userId);

            if($houseRole === false)
                return $response->withHeader('Location', '/')->withStatus(302);
            else
            {
                $user = ['userId' => $userId, 'role' => $houseRole[1], 'homeless' => false ];
                $dashboard = ($houseRole[1] == "member") ? "dashboard.php" : "admindashboard.php";
                $rules = $db->getExemptionRules($houseRole[0]);
            }

            $data = ['currentUser' => $user];
            $data['page'] = "rules.php";
            $data['link'] = $invite;
            $data['rules'] = $rules;
            
            return $renderer->render($response, $dashboard, $data);
        });
        $group->group('/create', function (Group $createGroup)
            {

                //TODO: Could this just be part of the rule page?
                $createGroup->get('', function (Request $request, Response $response)
                {
                    $userId = $request->getAttribute('userId');
                    if($userId==0)
                        return $response->withHeader('Location', '/')->withStatus(302);

                    $renderer = $this->get('renderer');
                    $db = $this->get('db');

                    $houseRole = $db->getUserHouseAndRole($userId);
                    if($houseRole === false)
                        return $response->withHeader('Location', '/')->withStatus(302);

                    $houseId = $houseRole[0];

                    $dashboard = ($houseRole[1] == "member") ? "dashboard.php" : "admindashboard.php";
                    $user = ['userId' => $userId, 'role' => $houseRole[1]];
                    $data = ['link' => "No Link", 'page' => "addrule.php", 'currentUser' => $user];
                    $data['rooms'] = $db->getRoomsInHousehold($houseId);
                    $data['tasks'] = $db->getTasksInHousehold($houseId);
                    $data['users'] = $db->getUsersNamesInHousehold($houseId);

                    return $renderer->render($response, 'admindashboard.php', $data);
                });
                $createGroup->post('/room_time', Rule\CreateRoomTimeRuleAction::class); // TODO: @SchedulesOverhaul
                $createGroup->post('/task_time', Rule\CreateTaskTimeRuleAction::class); // TODO: @SchedulesOverhaul
                $createGroup->post('/user_task', Rule\CreateUserTaskRuleAction::class);
                $createGroup->post('/user_room', Rule\CreateUserRoomRuleAction::class);
            });

        $group->group('/toggle', function (Group $toggleGroup)
            {
                $toggleGroup->post('/user_task', Rule\ToggleUserTaskRuleAction::class);
                $toggleGroup->post('/user_room', Rule\ToggleUserRoomRuleAction::class);
            });
        $group->group('/update', function (Group $updateGroup)
            {

            });
        $group->post('/delete', Rule\DeleteRuleAction::class);
        $group->get('/data', Rule\ListRuleAction::class);
    });
};
