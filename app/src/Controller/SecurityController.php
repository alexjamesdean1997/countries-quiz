<?php

namespace App\Controller;

use App\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $countries    = $this->doctrine->getRepository(Country::class)->findAll();
        shuffle($countries);
        $user = $this->getUser();

        if (null !== $user) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('login/index.html.twig', [
            'countries'     => $countries,
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}