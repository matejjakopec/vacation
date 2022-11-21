<?php

namespace App\Controller\Mapper;

class UserMapper
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



}