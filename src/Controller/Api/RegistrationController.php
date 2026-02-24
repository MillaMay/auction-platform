<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Валидация входных данных
        if (!$email || !$password) {
            return new JsonResponse([
                'error' => 'Email and password are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Проверка формата email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'error' => 'Invalid email format',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Проверка длины пароля
        if (strlen($password) < 6) {
            return new JsonResponse([
                'error' => 'Password must be at least 6 characters long',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Проверка, существует ли пользователь
        $existingUser = $userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse([
                'error' => 'User with this email already exists',
            ], Response::HTTP_CONFLICT);
        }

        // Создание нового пользователя
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        // Хеширование пароля
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Валидация entity
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse([
                'error' => 'Validation failed',
                'details' => $errorMessages,
            ], Response::HTTP_BAD_REQUEST);
        }

        // Сохранение пользователя
        $entityManager->persist($user);
        $entityManager->flush();

        // Генерация JWT токена
        $token = $jwtManager->create($user);

        return new JsonResponse([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], Response::HTTP_CREATED);
    }
}
