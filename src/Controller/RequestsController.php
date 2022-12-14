<?php

namespace App\Controller;

use App\Controller\Mapper\RequestMapper;
use App\Controller\Mapper\UserMapper;
use App\Entity\User;
use App\Entity\VacationRequest;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RequestsController extends AbstractController
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

    #[Route(path: '/vacation/new', name: 'vacation_request')]
    public function request(ManagerRegistry $doctrine, Request $request, UserRepository $userRepository){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['username' => $username]);
        $form = $this->createFormBuilder()
            ->add('start_date', DateType::class, [
                'data' => new \DateTime(),
                'format' => 'dd-MM-yyyy'
            ])
            ->add('end_date', DateType::class,[
                'format' => 'dd-MM-yyyy',
                'data' => new \DateTime('tomorrow')
            ])
            ->add('save', SubmitType::class, ['label' => 'request'])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $startDate = $form->get('start_date')->getData();
            $endDate = $form->get('end_date')->getData();
            if($endDate < $startDate || date_diff($endDate, $startDate)->d > $user->getVacationDaysLeft()){
                $response = new Response();
                return $response->setContent('bad data');
            }
            $vacationRequest = new VacationRequest();
            $vacationRequest->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setStatus('pending')
                ->setApprovedByTeamLead(false)
                ->setApprovedByProjectLead(false)
                ->setUser($user);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($vacationRequest);
            $entityManager->flush();

            return $this->redirectToRoute('user_info');
        }
        return $this->renderForm('request/index.html.twig', [
            'form' => $form,
            'daysLeft' => $user->getVacationDaysLeft()
        ]);

    }

    #[Route(path: '/vacation/team', name: 'team_requests')]
    public function teamRequests(UserRepository $userRepository){
        return $this->getSupervisorRequests('team', 'ROLE_TEAM_LEAD', 'getTeam', $userRepository);
    }

    #[Route(path: '/vacation/project', name: 'project_requests')]
    public function projectRequest(UserRepository $userRepository){
        return $this->getSupervisorRequests('project', 'ROLE_PROJECT_LEAD', 'getProject', $userRepository);
    }

    private function getSupervisorRequests($supervises, $role, $functionName, UserRepository $userRepository){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['username' => $username]);
        if($user->getRoles()[0] != $role){
            $response = new Response();
            return $response->setContent('not available');
        }
        $users = $userRepository->findBy([$supervises => $user->$functionName()]);
        $requests = [];
        foreach ($users as $u){
            if($u != $user){
                foreach ($u->getRequests() as $request){
                    if($request->getStatus() == 'pending'){
                        $requests[] = $request;
                    }
                }
            }
        }
        $requestMap = [];
        foreach ($requests as $request){
            $requestMap[] = $this->requestMapper->mapRequest($request);
        }
        return $this->render('request/requests_list.html.twig',[
            'requests' => $requestMap
        ]);
    }

    #[Route(path: '/vacation/{id}/approve')]
    public function approveRequest(int $id, ManagerRegistry $doctrine, Request $httpRequest){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        $request = $doctrine->getRepository(VacationRequest::class)->findOneBy(['id' => $id]);
        $roleCall = '';
        if($user->getRoles()[0] != 'ROLE_PROJECT_LEAD' && $user->getRoles()[0] != 'ROLE_TEAM_LEAD'){
            return 'not available';
        }

        if($user->getRoles()[0] == 'ROLE_PROJECT_LEAD'){
            $roleCall = 'getProject';
            $request->setApprovedByProjectLead(true);
        }
        if($user->getRoles()[0] == 'ROLE_TEAM_LEAD'){
            $roleCall = 'getTeam';
            $request->setApprovedByTeamLead(true);
        }

        if($request->getUser()->$roleCall() != $user->$roleCall()){
            return 'not available';
        }

        $request->setStatus('approved');
        $requestUser = $request->getUser();
        $requestUser->setVacationDaysLeft($requestUser->getVacationDaysLeft() - date_diff($request->getEndDate(),  $request->getStartDate())->d);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($request);
        $entityManager->persist($requestUser);
        $entityManager->flush();
        $route = $httpRequest->headers->get('referer');
        return $this->redirect($route);
    }

    #[Route(path: '/vacation/{id}/decline')]
    public function declineRequest(int $id, ManagerRegistry $doctrine, Request $httpRequest){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        $request = $doctrine->getRepository(VacationRequest::class)->findOneBy(['id' => $id]);
        $roleCall = '';
        if($user->getRoles()[0] != 'ROLE_PROJECT_LEAD' && $user->getRoles()[0] != 'ROLE_TEAM_LEAD'){
            return 'not available';
        }

        if($user->getRoles()[0] == 'ROLE_PROJECT_LEAD'){
            $roleCall = 'getProject';
        }
        if($user->getRoles()[0] == 'ROLE_TEAM_LEAD'){
            $roleCall = 'getTeam';
        }

        if($request->getUser()->$roleCall() != $user->$roleCall()){
            return 'not available';
        }

        $request->setStatus('declined');
        $entityManager = $doctrine->getManager();
        $entityManager->persist($request);
        $entityManager->flush();
        $route = $httpRequest->headers->get('referer');
        return $this->redirect($route);
    }



}