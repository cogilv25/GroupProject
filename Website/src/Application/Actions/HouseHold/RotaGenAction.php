<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;

// If you imagine this line as 24h with each '|' being a start/stop time for cleaning:
// ----------|-------------------|-----|-------|-------------------------|-----|-------------------
//          2:30                7:30  9:00   11:00                     17:30 19:00 
//
// This could be a schedule for a room, user or task schedule and would look something like this as a list:
// - [11,31]
// - [37,45]
// - [71,77]
//
// We can combine the room and task schedules for a particular room_has_task (job) into a new list
// which represent the times where a job can be performed if you simply take the largest blocks you
// can which fit inside both schedules.

// Now the problem is essentially a 1-D bin packing problem with some additional constraints ( which
// bin (user schedule) the jobs are able to be packed in).

// Currently we sort the jobs by the number of users who can do that job then we iterate through this
// list and for each job we assign it to the user with the least assigned jobs so far who is not exempt
// from the job until all jobs have been assigned to a user.

// Now we go through the jobs for each user and try to fit them into their schedule, if we can't we give up,
// this is not a final solution just an easy but bad one for now.

// We are probably somewhat there we just need to be able to move things around once they are in a container
// a simple improvement would just be to try putting the job in another users space if we can't fit it in the
// one it was assigned to.. maybe try swap it with one of their jobs. Another thing that would probably help
// is being able to calculate how much space is in each container.

// I think the best solution would involve some sort of fuzzy logic where we imagine that a job is in all the
// places it could be and then move through each conflict resolving it by reducing the number of places a job
// is able to be... badly explained but I'll explain it better as I figure it out.


class RotaGenAction extends AdminAction
{
    protected function action(): Response
    {
        // userID, roomId, taskId
        $userRoomTasks = $this->db->getValidUserRoomTaskCombinations($this->houseId);

        $rawuserSchedules = $this->db->getUserSchedulesFlat($this->houseId);

        $rota = $this->db->getUsersNamesInHousehold($this->houseId);
        $roomNames = $this->db->getRoomsInHousehold($this->houseId);
        $taskDetails = $this->db->getTasksInHousehold($this->houseId);

        // Ignore multiple days, just look at Monday for now

        $dayNames = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        foreach($rawuserSchedules as $row)
        {
            $day = 0;
            switch($row[1])
            {
                case "Tuesday": $day = 1; break;
                case "Wednesday": $day = 2; break;
                case "Thursday": $day = 3; break;
                case "Friday": $day = 4; break;
                case "Saturday": $day = 5; break;
                case "Sunday": $day = 6; break;
                default: $day = 0;
            }

            $userSchedules[$row[0]][$day][] = [(int)$row[2], (int)$row[3]];
        }   

        $jobList = [];
        $usersEligibleJob = [];

        // $row == [0=>userId,1=>roomId,2=>taskId]
        foreach ($userRoomTasks as $row)
        {
            $found = false;
            foreach($jobList as $index => $job)
            {
                if($job['room'] == $row[1] && $job['task'] == $row[2])
                {
                    $usersEligibleJob[$index][] = $row[0];
                    $found = true;
                    break;
                }
            }
            
            if(!$found)
            {
                // TODO: Real duration and period values, period unused atm..
                $jobList[] = ['room' => $row[1], 'task' => $row[2], 'duration' => 4, 'period' => -1 ];
                $usersEligibleJob[count($jobList)-1][] = $row[0];
            }
            $job = $row[1]."-".$row[2];
            $jobToRoomTask[$job] = [$row[1],$row[2]];
            $jobEligibleUsers[$job][] = $row[0];
            for ($day=0; $day < 7; $day++) 
            { 
                // TODO: Get real Room and Time Schedules, we are currently
                //         assuming rooms and tasks can be used/done anytime.
                $roomSchedules[$row[1]][$day][0] = [0,95];
                //TODO: Incorporate task schedules, currently we assume that everyone can do a
                //        specific task concurrently, which while true for some tasks is not for
                //        others for example there may only be 1 hoover. 
                $taskSchedules[$row[2]][$day][0] = [0,95];
            }
            $capacityJobSchedule[$job][0] = [0,95];
        }

        $success = true;
        $errors = [];


        // Sort the jobList and userEligibleJob arrays by the number of users eligible for the 
        //   specific job to get the jobs in order from the hardest to allocate to the easiest,
        //   thus making it easier to evenly distribute the jobs.
        array_multisort(array_map('count',$usersEligibleJob), SORT_ASC, SORT_NUMERIC, $usersEligibleJob, $jobList);

        foreach($dayNames as $day => $nameOfDay)
        {

            // Loop through the jobs that need assigned in ascending order based on
            //   the number of users who are not exempt from the job.
            foreach ($jobList as $jobIndex => $job)
            {
                $jobAssigned = false;
                // Sort the list of user rotas by the number of jobs assigned to each rota.
                // This gives us a list of user rotas with the user who has been assigned
                //   the fewest jobs at the top.
                // We do this each time a job is assigned as the user with the most jobs
                //   may change.
                uasort($rota,function($a,$b){ return count($a)-count($b);});

                

                // Loop through the users in ascending order based on jobs assigned.
                foreach ($rota as $user => $userRota)
                {
                    // Skip the user if they are exempt from the job
                    if(array_search($user, $usersEligibleJob[$jobIndex]) === false) continue;

                        // Loop through the rows in the users schedule
                        foreach ($userSchedules[$user][$day] as $userScheduleRowIndex => $userScheduleRow )
                        {
                            // Variables are just for readability.
                            $ubegin = $userScheduleRow[0]; $uend = $userScheduleRow[1];
                            // If there is no room in this row of this users schedule
                            //   the job doesn't fit and we can exit early.
                            if($uend - $ubegin < $job['duration']) continue;

                            // Loop through the rows in the room schedule that this job
                            //   will take place within.
                            foreach ($roomSchedules[$job['room']][$day] as $roomScheduleRowIndex => $roomScheduleRow )
                            {
                                // Variables are just for readability.
                                $rbegin = $roomScheduleRow[0]; $rend = $roomScheduleRow[1];
                                // If there is no room in this row of the room's schedule
                                //  the job doesn't fit and we can exit early.
                                if($rend - $rbegin < $job['duration']) continue;

                                //TODO: Task schedule checking would go here..

                                // Get the inner range which is the maximum range of the user and room
                                //   schedule rows combined.
                                $ibegin = max($rbegin, $ubegin); $iend = min($rend, $uend);

                                // The calculated range $iend - $ibegin will be negative if the schedules
                                //   do not intersect so we don't need to explicitly check for this.

                                // If there is no room in the combined user,room schedule row
                                //   the job doesn't fit and we can exit early.
                                if($iend - $ibegin < $job['duration']) continue;

                                // This is the point where we know the job can fit in the current
                                //   user's schedule and the job's room's schedule. We just have to
                                //   insert it into the user's rota and remove the range from the
                                //   user's schedule and the job's room's schedule.

                                //Calculate the begin and end for the new rota entry
                                $ebegin = $ibegin; $eend = $ebegin + $job['duration'];

                                //Remove the range from the user's schedule

                                // If the start of the range == the start of the users schedule
                                //   row this is the easiest case where we shorten or remove the
                                //   row from the users schedule,
                                if($ebegin == $ubegin)
                                {
                                    // If the range exactly matches the user's schedule row
                                    //   remove the row from the schedule, otherwise, set
                                    //   the begin field of the schedule row to the end of
                                    //   the range.
                                    if($eend == $uend)
                                        unset($userSchedules[$user][$day][$userScheduleRowIndex]);
                                    else
                                        $userSchedules[$user][$day][$userScheduleRowIndex][0] = $eend;
                                }
                                else
                                {
                                    // TODO: Moving things around we could get rid of a line of code
                                    //         it requires rewording the comments though.
                                    // Otherwise, if the end of the range == the end of the users
                                    //   schedule row range then we just shorten the row.
                                    if($eend == $uend)
                                        $userSchedules[$user][$day][$userScheduleRowIndex][1] = $ebegin;
                                    // Finally if the range is somewhere in the middle of
                                    //   the user's schedule row then we need to split the
                                    //   row into 2 seperate rows in their schedule.
                                    else
                                    {
                                        $userSchedules[$user][$day][] = [$eend, $uend];
                                        $userSchedules[$user][$day][$userScheduleRowIndex][1] = $ebegin;
                                    }
                                }

                                //Remove the range from the room's schedule

                                // This is the same process as for the user so I will omit
                                //   the detailed comments.
                                if($ebegin == $rbegin)
                                {
                                    if($eend == $rend)
                                        unset($roomSchedules[$job['room']][$day][$roomScheduleRowIndex]);
                                    else
                                        $roomSchedules[$job['room']][$day][$roomScheduleRowIndex][0] = $eend;
                                }
                                else
                                {
                                    //More compact does the same thing.
                                    if($eend != $rend)
                                        $roomSchedules[$job['room']][$day][] = [$eend, $rend];

                                    $roomSchedules[$job['room']][$day][$roomScheduleRowIndex][1] = $ebegin;
                                }

                                // Finally we can insert the new row into the rota and set the flag
                                //   that breaks the loops to get to the next job.
                                $row = ['room' => $roomNames[$job['room']]['name']];
                                $row['task'] = $taskDetails[$job['task']]['name'];
                                $row['begin'] = $ebegin;
                                $row['end'] = $eend;
                                $row['day'] = $nameOfDay;

                                $rota[$user]['jobs'][] = $row;
                                $jobAssigned = true;
                                break;
                            }
                            if($jobAssigned) break;
                        }
                        if($jobAssigned) break;
                }
                //If we got the job assigned to someone continue      
                if($jobAssigned) continue;

                //If we didn't get the job assigned to someone then we can have a good moan!
                $errors[] = "Couldn't assign task: " . $taskDetails[$job['task']]['name'] . " in room: " . $roomNames[$job['room']]['name'] . " on day " . $nameOfDay . " to any user";
            }
        }

        // $errors can either be an array of strings or false to indicate there are no errors.
        if(count($errors) < 1 )
            $errors = false;

        return $this->createJsonDataResponse($this->response, $rota, $errors);
    }
}
