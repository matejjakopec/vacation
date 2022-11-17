<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VacationRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Collection;

class RequestsController extends AbstractController
{
    private Security $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(path: '/user/vacation', name: 'vacation_request')]
    public function request(ManagerRegistry $doctrine, Request $request){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        $form = $this->createFormBuilder()
            ->add('start_date', DateType::class)
            ->add('end_date', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'request'])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $startDate = $form->get('start_date')->getData();
            $endDate = $form->get('end_date')->getData();
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
    public function teamRequests(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        $users = $doctrine->getRepository(User::class)->findBy(['team' => $user->getTeam()]);
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
            $requestMap[] = [
                'username' => $request->getUser()->getUsername(),
                'start_date' => $request->getStartDate()->format('d-m-y'),
                'end_date' => $request->getEndDate()->format('d-m-y'),
                'status' => $request->getStatus(),
            ];
        }
        return $this->render('request/requests_list.html.twig',[
            'requests' => $requestMap
        ]);
    }


}