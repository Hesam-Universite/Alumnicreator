<?php

namespace App\Service;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private ParameterRepository $parameterRepository,
        private TransportInterface $transport,
        private MailerInterface $mailer,
    ) {
        $user = $this->parameterRepository->findOneBy(['code' => Parameter::SMTP_USER]);
        $password = $this->parameterRepository->findOneBy(['code' => Parameter::SMTP_PASSWORD]);
        $server = $this->parameterRepository->findOneBy(['code' => Parameter::SMTP_SERVER]);
        $port = $this->parameterRepository->findOneBy(['code' => Parameter::SMTP_PORT]);

        $this->transport = Transport::fromDsn('smtp://'.((!empty($user->getValue()) && !empty($password->getValue())) ? urlencode($user->getValue()).':'.urlencode($password->getValue()).'@' : null).$server->getValue().':'.$port->getValue());
        $this->mailer = new Mailer($this->transport);
    }

    /**
     * Email the administrator when the lost from sight form has been submitted.
     *
     * @param array $data
     *
     * @return void
     *
     * @throws TransportExceptionInterface
     */
    public function perduDeVue(array $data): void
    {
        if (null !== $adminEmail = $this->parameterRepository->findOneBy(['code' => Parameter::ADMIN_EMAIL])->getValue()) {
            $notification = (new Email())
                ->from($this->parameterRepository->findOneBy(['code' => Parameter::SMTP_FROM])->getValue())
                ->to($adminEmail)
                ->subject('Nouvel demande d\'étudiant perdu de vue')
                ->html('
                <p>Bonjour,</p>
                <p>Une personne souhaiterait retrouver cet étudiant perdu de vue :</p>
                <p>
                    Nom : '.$data['name'].'<br>
                    Prénom : '.$data['firstname'].'<br>
                    Promotion : '.$data['class'].'
                </p>
            ');

            $this->mailer->send($notification);
        }
    }
}
