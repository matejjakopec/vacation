<?php

namespace App\Controller;

class Mapper
{

    public function mapUser($user){
        return [
            'username' => $user->getUsername(),
            'vacationDaysLeft' => $user->getVacationDaysLeft(),
            'team' => $user->getTeam()->getName(),
            'teamId' => $user->getTeam()->getId(),
            'project' => $user->getProject()->getName(),
            'projectId' => $user->getProject()->getId(),
            'role' => $user->getRoles()[0]
        ];
    }

    public function mapRequest($request){
        return [
            'id' => $request->getId(),
            'username' => $request->getUser()->getUsername(),
            'start_date' => $request->getStartDate()->format('d-m-y'),
            'end_date' => $request->getEndDate()->format('d-m-y'),
            'status' => $request->getStatus(),
        ];
    }

}