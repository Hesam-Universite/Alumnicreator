<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Resume;
use Dompdf\Dompdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ApplicationService
{
    public function __construct(
        private UploaderHelper $uploaderHelper,
        private ParameterBagInterface $parameterBag,
        private Environment $twig,
    ) {
    }

    public function addToZip(\ZipArchive &$zip, Application $application, ?Resume $resume = null): void
    {
        $dir = $application->getUser()->getId().'-'.$application->getUser();
        $zip->addEmptyDir($dir);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->twig->render('frontend/PDF/application.html.twig', [
            'application' => $application,
        ]));
        $dompdf->render();
        $tmpname = tempnam(sys_get_temp_dir(), 'dompdf');
        file_put_contents($tmpname, $dompdf->output());
        $zip->addFile($tmpname, $dir.'/fiche-candidat.pdf');

        if (null !== $resume) {
            $zip->addFile($this->parameterBag->get('kernel.project_dir').'/public/'.$this->uploaderHelper->asset($resume, 'resume'), $dir.'/'.$resume->getResumeName());
        }

        if (null !== $application->getAdditionalFileName()) {
            $zip->addFile($this->parameterBag->get('kernel.project_dir').'/public/'.$this->uploaderHelper->asset($application, 'additionalFile'), $dir.'/'.$application->getAdditionalFileName());
        }
    }
}
