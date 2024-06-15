<?php
namespace App\Controller\API;

use App\Entity\User;
use App\Service\MailService;
use App\Service\SecurityService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/api/sign_in', name: 'api_sign_in', methods: ['POST'], stateless: false)]
    public function signIn(Request $request, SecurityService $securityService, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        try 
        {
            $securityService->validateUniqueness($data['login'], $data['email']);
            $securityService->validatePasswordStrength($data['password']);
            $token = TokenGeneratorService::generateToken();

            $this->em->getRepository(User::class)->createUser(
                $this->em,
                $userPasswordHasher,
                $data['login'],
                $data['email'],
                $data['password'],
                $token
            );

            MailService::activationAccount($data['email'], $token);

            return $this->json(
                ["message" => "Veuillez confirmer votre adresse e-mail pour activer votre compte."], 
                201);
        }
        catch (\InvalidArgumentException $exception) {
            return $this->json(
                ["message" => $exception->getMessage()], 
                500);
        }  
    }

    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        return $this->json($user, 200, [], ['groups' => 'user.index']);
    }

    #[Route('/api/user/activate', name: 'api_user_activate', methods: ['PATCH'])]
    public function userActivate(Request $request): JsonResponse
    {
        $activationToken = $request->query->get('token');
    
        if(!empty($activationToken)) {
            $user = $this->em->getRepository(User::class)->findOneBy(['accountValidationToken' => $activationToken]);

            if ($user) {
                $user->setActive(true);
                $user->setAccountValidationToken(null);

                $this->em->persist($user);
                $this->em->flush();
                
                return $this->json(["Message" => "Votre compte a été activé avec succès."], 200);
            }
        }
        return $this->json(["Message" => "Une erreur est survenue"], 500);
    }

    #[Route('/api/user/deactivate', name: 'api_user_deactivate', methods: ['PATCH'])]
    public function userDeactivate(): JsonResponse
    {
        $user = $this->getUser();
        $user->setActive(false);
        $this->em->flush();

        return $this->json("", 200, []);
    }

    #[Route('/api/user/delete', name: 'api_user_delete', methods: ['DELETE'])]
    public function userDelete(): JsonResponse
    {
        $user = $this->getUser();
        $this->em->remove($user);
        $this->em->flush();

        return $this->json("", 200, []);
    }
    
}
