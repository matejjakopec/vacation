<?php

namespace App\Controller;

use App\Controller\Mapper\RequestMapper;
use App\Controller\Mapper\UserMapper;
use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    private Security $security;

    private UserMapper $userMapper;
    private RequestMapper $requestMapper;


    public function __construct(Security $security, UserMapper $userMapper, RequestMapper $requestMapper)
    {
        $this->security = $security;
        $this->userMapper = $userMapper;
        $this->requestMapper = $requestMapper;
    }

    #[Route(path: 'user', name: 'user_info')]
    public function info(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        $userMap =$this->userMapper->mapUser($user);

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
            $requestsMap[] = $this->requestMapper->mapRequest($request);
        }
        return $this->render('user/user_requests.html.twig', [
            'requests' => $requestsMap,
        ]);
    }



}