<?php

namespace App\Controller\Backend;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Service\DeleteGroupService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security('is_granted("ROLE_ADMIN")')]
#[Route('/administration/groupes', name: 'admin_group_')]
class GroupController extends AbstractController
{
    /**
     * display groups in backoffice.
     *
     * @param GroupRepository $groupRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'index')]
    public function index(GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->findAll();

        return $this->render('backend/group/index.html.twig', [
            'groups' => $groups,
        ]);
    }

    /**
     * Approve or disapprove a group.
     *
     * @param Group                  $group
     * @param EntityManagerInterface $entityManager
     *
     * @return Response - admin_group_index
     */
    #[Route('/approuver/{id}', name: 'approve')]
    public function approveCompanies(Group $group, EntityManagerInterface $entityManager): Response
    {
        $group->setIsApproved(!$group->isApproved());
        $entityManager->persist($group);
        $entityManager->flush();

        return $this->redirectToRoute('admin_group_index');
    }

    /**
     * to delete a group.
     *
     * @param Group $group
     *
     * @return Response - admin_group_index
     */
    #[Route('/supprimer/{id<\d+>}', name: 'delete')]
    public function delete(Group $group, DeleteGroupService $deleteGroupService): Response
    {
        $deleteGroupService->deleteGroup($group);

        $this->addFlash(
            'success',
            'Groupe supprimé avec succès.'
        );

        return $this->redirectToRoute('admin_group_index');
    }
}
