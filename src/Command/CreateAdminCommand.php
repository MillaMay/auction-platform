<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates an admin user interactively'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // --- Запрашиваем email ---
        $questionEmail = new Question('Введите email администратора: ');
        $email = trim($helper->ask($input, $output, $questionEmail));

        // Проверка на пустой email
        if (empty($email)) {
            $io->error('Email не может быть пустым!');
            return Command::FAILURE;
        }

        // Валидация формата email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Неверный формат email!');
            return Command::FAILURE;
        }

        // Проверка существующего пользователя
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Пользователь с таким email уже существует: ' . $email);
            return Command::FAILURE;
        }

        // --- Запрашиваем пароль ---
        $questionPassword = new Question('Введите пароль администратора: ');
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $questionPassword);

        // Проверка на пустой пароль
        if (empty($password)) {
            $io->error('Пароль не может быть пустым!');
            return Command::FAILURE;
        }

        // Проверка минимальной длины пароля
        if (strlen($password) < 6) {
            $io->error('Пароль должен быть не менее 6 символов!');
            return Command::FAILURE;
        }

        // --- Создаём пользователя ---
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Администратор создан: ' . $email);

        return Command::SUCCESS;
    }
}
