<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\UserDetails;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * @Route("/api")
 */

class UserController extends AbstractController
{
    private $security;

    public function __construct(Security $security, private ManagerRegistry $doctrine, private UserPasswordHasherInterface $passwordHasher)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/login", name="api_login", methods={"POST"})
     */
    public function index(#[CurrentUser] ?User $user): Response
    {
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
        // if (!$this->passwordHasher->isPasswordValid($user, $parameters['password'])) {
        //     return $this->json([
        //         'error' => 'mot de passe incorrect!!',
        //     ], Response::HTTP_UNAUTHORIZED);
        // }
    }

    /**
     * @Route("/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $entityManager = $this->doctrine->getManager();

        $user = new User();
        $user->setUserName($parameters['username']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $parameters['password']
        );
        $user->setPassword($hashedPassword);

        $userDetails = new UserDetails();
        $userDetails->setUserId($user);
        $userDetails->setFirstName($parameters['firstName']);
        $userDetails->setLastName($parameters['lastName']);
        $userDetails->setPhone($parameters['phone']);
        $userDetails->setBirthdate(\DateTime::createFromFormat('Y-m-d', $parameters['birthdate']));
        $userDetails->setEmail($parameters['email']);


        $entityManager->persist($user);
        $entityManager->persist($userDetails);
        $entityManager->flush();
        return $this->json(['message' => 'category added'], 200);
    }
}
