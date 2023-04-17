<?php

namespace App\Controller\API\Internal;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends AbstractController
{
    /**
     * Updates the student's roles.
     *
     * @param User                   $student       - The student to update
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return array
     */
    #[Rest\Post('/api/internal/students/{id}/change-role', name: 'api_internal_student_change_role')]
    #[Rest\View(statusCode: Response::HTTP_OK)]
    public function changeRole(User $student, Request $request, EntityManagerInterface $entityManager)
    {
        $role = $request->request->get('role');

        if ($role === 'Alumni') {
            $student->setRoles(['ROLE_STUDENT', 'ROLE_ALUMNI', 'ROLE_RECRUITER']);
        } else {
            $student->setRoles(['ROLE_STUDENT', 'ROLE_CURRENT_STUDENT']);
        }

        $entityManager->flush();

        return ['success' => true, 'role' => $role];
    }
}
