<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GiveVacationDaysCommand extends Command
{

    protected static $defaultName = 'app:give:vacation:days';

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Gives every employee 20 extra days of vacation');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->doctrine->getRepository(User::class)->findAll();
        $entityManager = $this->doctrine->getManager();
        foreach ($users as $user){
            if($user->getRoles()[0] != 'ROLE_ADMIN'){
                $user->setVacationDaysLeft($user->getVacationDaysLeft() + 20);
                $entityManager->persist($user);
            }
        }
        $entityManager->flush();
        $io->success(sprintf('Added 20 days to every user'));

        return 0;

    }

}