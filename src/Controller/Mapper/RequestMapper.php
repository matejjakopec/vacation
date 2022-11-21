<?php

namespace App\Controller\Mapper;

class RequestMapper
{
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