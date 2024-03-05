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
        $userRoomTasks = $this->db->getValidUserRoomTaskCombinations($this->houseId);

        $userSchedules = $this->db->getUserSchedulesFlat($this->houseId);

        //Ignore multiple days, just choose one for now
        foreach($userSchedules as $row)
        {
            if($row[1] == "Monday")
            {
                $mondaySchedules[] = [$row[0],$row[2],$row[3]];
                $capacityUserSchedules[$row[0]][] = [$row[2], $row[3]-$row[2]];
            }
        }

        //TODO: Job Schedules don't make sense.. split into room and task schedules
        //        you will never have a collision as there is only ever 1 of each job.
        foreach ($userRoomTasks as $row)
        {
            $job = $row[1]."-".$row[2];
            $jobToRoomTask[$job] = [$row[1],$row[2]];
            $jobEligibleUsers[$job][] = $row[0];
            //TODO: Get real Job(RoomTime) Schedules
            $capacityJobSchedule[$job][] = [0,95];
        }

        $rota = [];
        //This is awful but works to sort the rota without losing the userId's
        foreach ($capacityUserSchedules as $key => $value)
        {
            $rota[] = [$key => []];
        }

        $status['status'] = 'success';

        foreach ($jobEligibleUsers as $job => $users) 
        {
            //Foreach on users from least jobs to most jobs
                //Try to assign job checking user and job schedules
                //On fail skip to next user
                //On success reduce user and job schedule capacities

            $jobAssigned = false;
            //This is awful but works to sort the rota without losing the userId's
            //TODO: We could probably use usort which would look 10% better haha!
            //TODO: check out uasort as a possible solution to the weirdness...
            array_multisort(array_map(function($a){foreach($a as $tab){return count($tab);}},$rota),SORT_ASC,SORT_NUMERIC,$rota);
            foreach ($rota as $key => $userIdTab)
            {
                $uId = array_key_first($userIdTab);
                if(array_search($uId, $users)===false) continue;
                foreach($capacityUserSchedules[$uId] as $ukey => $urow)
                {
                    if($urow[1] < 4) continue; //Job can't fit in this schedule jump to next one

                    foreach($capacityJobSchedule[$job] as $jkey => $jrow)
                    {
                        if($jrow[1] < 4) continue; //Job can't fit in this schedule jump to next one

                        //If the job can fit at the beginning of the user and job schedules we can just
                        //    decrement the capacity for that schedule row otherwise..
                        //If we can find a spot where it fits in both the user and job schedules then
                        //    where it is not at the beginning of a schedule row we must split the row
                        //    at that point creating a new row where it is at the start....

                        $ustart = $urow[0]; $ucap = $urow[1]; $uend = $ustart + $ucap;
                        $jstart = $jrow[0]; $jcap = $jrow[1]; $jend = $jstart + $jcap;
                        $ostart = max($ustart, $jstart); $oend = min($uend, $jend); $ocap = $oend - $ostart;

                        //Will be negative if the schedules do not overlap otherwise will be
                        //    the capacity of the overlapping region.
                        if($ocap < 4) continue; //Job can't fit in this schedule jump to next one

                        //We now know it fits at ostart now to update the 2 schedules
                        $room = $jobToRoomTask[$job][0];
                        $task = $jobToRoomTask[$job][1];

                        if($ostart == $ustart) // Simplest possibility
                        {
                            $capacityUserSchedules[$uId][$ukey][1]-=4;
                            $capacityUserSchedules[$uId][$ukey][0]+=4;
                        }
                        else
                        {
                            //This is where we could create small gaps in the
                            //    schedule that can't be filled by any job.
                            $capacityUserSchedules[$uId][] = [$ostart,$uend-$ostart];
                            $capacityUserSchedules[$uId][$ukey][1] = $ostart-$ustart;
                        }

                        if($ostart == $jstart) // Simplest possibility
                        {
                            $capacityJobSchedule[$job][$jkey][1]-=4;
                            $capacityJobSchedule[$job][$jkey][0]+=4;
                        }
                        else
                        {
                            //This is where we could create small gaps in the
                            //    schedule that can't be filled by any job.
                            $capacityJobSchedule[$job][] = [$ostart,$jend-$ostart];
                            $capacityJobSchedule[$job][$jkey][1] = $ostart-$jstart;
                        }

                        //Finally create a new row in the rota!
                        $rota[$key][$uId][] = [$room,$task,$ostart, $ostart + 4];
                        $jobAssigned = true;
                        break;
                    }
                    if($jobAssigned) break;
                }
                if($jobAssigned) break;
            }
            if($jobAssigned) continue;

            //We couldn't assign the job to anyone!!
            $room = $jobToRoomTask[$job][0];
            $task = $jobToRoomTask[$job][1];
            $status['status'] = 'error';
            $status['messages'][] = "Couldn't assign task: ".$task." in room: " . $room. " to any user";
        }

        $status['rota'] = $rota;

        return $this->createJsonResponse($this->response, $status);
    }
}
