<?php

namespace App\Controller\Backend;

use App\Entity\FooterColumn;
use App\Form\FooterColumnType;
use App\Repository\FooterColumnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_ADMIN')")]
#[Route('administration/footer', name: 'admin_footer_')]
class FooterController extends AbstractController
{
    /**
     * Index every footer columns.
     *
     * @param FooterColumnRepository $footerColumnRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response - backend/footer_columns/index.html.twig
     */
    #[Route('/index', name: 'index')]
    public function index(FooterColumnRepository $footerColumnRepository)
    {
        $footerColumns = $footerColumnRepository->findBy([], ['position' => 'DESC']);

        if (count($footerColumns) >= 4) {
            $isAbleToCreateNewColumn = false;
        } else {
            $isAbleToCreateNewColumn = true;
        }

        return $this->render('backend/footer_columns/index.html.twig', [
            'columns' => $footerColumns,
            'isAbleToCreateNewColumn' => $isAbleToCreateNewColumn,
        ]);
    }

    /**
     * To create / edit a new footer column.
     *
     * @param FooterColumn|null      $footerColumn
     * @param Request                $request
     * @param FooterColumnRepository $footerColumnRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response - backend/footer_columns/handle.html.twig
     */
    #[Route('/nouvelle-colonne', name: 'new_column')]
    #[Route('/modifier-colonne/{id<\d+>}', name: 'edit_column')]
    public function handle(FooterColumn $footerColumn = null, Request $request, FooterColumnRepository $footerColumnRepository, EntityManagerInterface $entityManager)
    {
        if (null === $footerColumn) {
            $footerColumn = new FooterColumn();
        }

        $columns = $footerColumnRepository->findAll();

        $form = $this->createForm(FooterColumnType::class, $footerColumn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = [];
            $hasFooterPages = true;
            $numberOfFooterPages = 1;
            while ($hasFooterPages) {
                if (null !== $request->request->get('name-'.$numberOfFooterPages)) {
                    $content[] = [$request->request->get('name-'.$numberOfFooterPages), $request->request->get('url-'.$numberOfFooterPages)];
                    $numberOfFooterPages++;
                } else {
                    $hasFooterPages = false;
                }
            }
            $footerColumn->setContent(serialize($content));

            $entityManager->persist($footerColumn);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Colonne enregistrée avec succès'
            );

            return $this->redirectToRoute('admin_footer_index');
        }

        return $this->render('backend/footer_columns/handle.html.twig', [
            'form' => $form->createView(),
            'columnName' => $footerColumn->getName(),
            'columnContent' => unserialize($footerColumn->getContent()),
            'currentColumn' => count($columns) + 1,
        ]);
    }

    /**
     * delete a footer column.
     *
     * @param FooterColumn           $footerColumn
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/supprimer-colonne/{id<\d+>}', name: 'delete_column')]
    public function delete(FooterColumn $footerColumn, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($footerColumn);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Colonne supprimée avec succès',
        );

        return $this->redirectToRoute('admin_footer_index');
    }
}
