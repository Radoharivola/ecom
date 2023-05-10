<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

/**
 * @Route("/api", name="api_")
 */

class UserController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/login", name="api_login", methods={"POST"})
     */
    public function index(): Response
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return $this->json([
                'error' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        return $this->json([
            'message' => 'Welcome ' . $user->getUserIdentifier(),
            'username'  => $user->getUsername(),
            'roles' => $user->getRoles(),
            'id' => $user->getId()  
        ]);
        
    }
}
