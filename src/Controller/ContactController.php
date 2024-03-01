<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            // Valider le reCAPTCHA en envoyant une requête POST à Google
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => '6LdZ_oUpAAAAAERzzCFLxHsvzMj550REfzdJ-Ff5',
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->getClientIp()
                ],
            ]);

            // Récupérer la réponse de Google reCAPTCHA
            $responseData = $response->toArray();
            if ($responseData['success']){

                $nomComplet = $form->get('nomComplet')->getData();
                $email = $form->get('email')->getData();
                $telephone = $form->get('telephone')->getData();
                $typeAide = $form->get('typeAide')->getData();
                $message = $form->get('message')->getData();

                // Envoyer l'e-mail
                $email = (new Email())
                    ->from($email)
                    ->to('rodriguet802@gmail.com')
                    ->subject('Nouveau message de contact')
                    ->text('Nom: '.$nomComplet."\n".
                        'Email: '.$email."\n".
                        'Téléphone: '.$telephone."\n".
                        'Type d\'aide: '.$typeAide."\n".
                        'Message: '.$message);

                $mailer->send($email);
                $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            }else{
                $this->addFlash('error', 'Le reCAPTCHA n\'a pas été validé. Veuillez réessayer.');
            }

        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
