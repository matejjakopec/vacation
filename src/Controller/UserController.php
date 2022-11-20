<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\Persistence\ManagerRegistry;
use MongoDB\Driver\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    private Security $security;

    private Mapper $mapper;


    public function __construct(Security $security, Mapper $mapper)
    {
        $this->security = $security;
        $this->mapper = $mapper;
    }

    #[Route(path: 'user', name: 'user_info')]
    public function info(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        $userMap =$this->mapper->mapUser($user);

        return $this->render('user/index.html.twig',[
            'user' => $userMap,
        ]);
    }

    #[Route(path: 'user/requests', name: 'user_requests')]
    public function requests(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        $requests = $doctrine->getRepository(VacationRequest::class)->findBy(['user' => $user]);
        $requestsMap = [];
        foreach ($requests as $request){
            $requestsMap[] = $this->mapper->mapRequest($request);
        }
        return $this->render('user/user_requests.html.twig', [
            'requests' => $requestsMap,
        ]);
    }



}