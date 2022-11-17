<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    private Security $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(path: 'user', name: 'user_info')]
    public function info(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $userMap = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        $user = [
            'username' => $userMap->getUsername(),
            'vacationDaysLeft' => $userMap->getVacationDaysLeft(),
            'team' => $userMap->getTeam()->getName(),
            'teamId' => $userMap->getTeam()->getId(),
            'project' => $userMap->getProject()->getName(),
            'projectId' => $userMap->getProject()->getId(),
            'role' => $userMap->getRoles()[0]
        ];

        return $this->render('user/index.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route(path: 'user/requests', name: 'user_requests')]
    public function requests(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        $requestMap = $doctrine->getRepository(VacationRequest::class)->findBy(['user' => $user]);
        $requests = [];
        foreach ($requestMap as $request){
            $requests[] = [
                'start_date' => $request->getStartDate()->format('d-m-Y'),
                'end_date' => (string)$request->getEndDate()->format('d-m-Y'),
                'status' => $request->getStatus()
            ];
        }
        return $this->render('user/user_requests.html.twig', [
            'requests' => $requests,
        ]);
    }

}