<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppAuthenticator extends AbstractAuthenticator
{
    private Security $security;
    private UserRepository $userRepository;


    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        return (bool)$request->get('_password');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->get('_username');
        $password = $request->get('_password');
        return new Passport(new UserBadge($username), new PasswordCredentials($password));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $adminResponse = new RedirectResponse('/admin');
        $userResponse = new RedirectResponse('/user');
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $this->userRepository->findOneBy(['username' => $username]);
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $adminResponse;
        }
        return $userResponse;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = new Response();
        $response->setContent('wrong credentials');
        return $response;
    }
}