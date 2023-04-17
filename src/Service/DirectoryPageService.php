<?php

namespace App\Service;

use App\Entity\DirectoryPage;
use App\Entity\Parameter;
use App\Repository\DirectoryPageRepository;
use App\Repository\ParameterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\File;

class DirectoryPageService
{
    public function __construct(
        private DirectoryPageRepository $directoryPageRepository,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private ParameterRepository $parameterRepository,
    ) {
    }

    public function import(File $file): void
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $paramImport = $this->parameterRepository->findOneBy(['code' => Parameter::DIRECTORY_IMPORT])->getValue();

        for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
            $email = $sheet->getCell('C'.$i)->getValue();

            $directory = $this->directoryPageRepository->findOneBy(['email' => $email]);
            if (null === $directory) {
                $directory = new DirectoryPage();
            } elseif ($paramImport == 2) {
                continue;
            }

            $directory->setFirstname($sheet->getCell('A'.$i)->getValue())
                ->setLastname($sheet->getCell('B'.$i)->getValue())
                ->setEmail($email)
                ->setClass($sheet->getCell('D'.$i)->getValue())
                ->setLinkedinLink($sheet->getCell('E'.$i)->getValue());

            if (null !== $user = $this->userRepository->findOneBy(['email' => $email])) {
                $directory->setUser($user);
            }

            $this->entityManager->persist($directory);
        }

        $this->entityManager->flush();
    }
}
