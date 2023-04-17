<?php

namespace App\Controller\Backend;

use App\Repository\RequestContactRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/administration/contact', name: 'contact_admin')]
#[Security("is_granted('ROLE_ADMIN')")]
class RequestContactController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(RequestContactRepository $requestContactRepository)
    {
        $requestsContact = $requestContactRepository->findBy([], ['id' => 'DESC']);

        return $this->render('backend/request_contact/index.html.twig', [
            'requestsContact' => $requestsContact,
        ]);
    }
}
