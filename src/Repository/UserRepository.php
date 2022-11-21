<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository
{

    public ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }

    public function findOneBy(array $criteria){
        return $this->doctrine->getRepository(User::class)->findOneBy($criteria);
    }

    public function find(int $id){
        return $this->doctrine->getRepository(User::class)->find($id);
    }

    public function findAll(){
        return $this->doctrine->getRepository(User::class)->findAll();
    }

}