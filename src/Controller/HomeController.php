<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Website;
use App\Repository\StatusRepository;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $mailer;
    private $colors;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->colors = array(
            '0' => 'danger',
            '200' => 'success',
            '301' => 'info',
            '302' => 'info'
        );
    }

    /**
     * @Route("/", name="home")
     */
    public function index(WebsiteRepository $websiteRepository, StatusRepository $statusRepository): Response
    {
        $websites = $websiteRepository->findAll();
        $ipAddress = $websiteRepository->findIp();
        $count = count($websites);
        $status = $statusRepository->getLastStatus($count);

        return $this->render('home/index.html.twig', [
            'websites' => $websites,
            'ipAddress' => $ipAddress,
            'status' => $status,
            'colors' => $this->colors
        ]);
    }

    /**
     * @Route("/website/clean", name="clean")
     */
    public function clean(StatusRepository $statusRepository)
    {
        // Supprimer tous nos status
        $statusRepository->cleanStatusHistory();

        $this->addFlash('warning', 'L\'historique des status a bien été effacé');
        // Redirection vers la HP
        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/websites/analyze", name="analyze")
     */
    public function analyze(WebsiteRepository $repository, EntityManagerInterface $manager)
    {
        // Récupèrer tous nos sites
        $websites = $repository->findAll();

        // Récupère le status actuel de chacun
        foreach ($websites as $key => $site) {
            $url = $site->getUrl();
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($handle);
            $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);

            // Créer une nouvelle entité Status et l'enregistrer
            $status = new Status();
            $status->setCode($code)
                ->setReportedAt(new \DateTime())
                ->setWebsite($site);
            $manager->persist($status);

            // envoi d'un email si le statu est égale à 0
            if ($status->getCode() === 0) {
                $message = (new \Swift_Message('Server Monitor'))
                    ->setFrom('info@monitoring.com')
                    ->setTo('contact@monitoring.com')
                    ->setBody(
                        $this->renderView('admin/email.html.twig', compact('status', 'site')), 'text/html'
                    );

                $this->mailer->send($message);
            }
        }
        $manager->flush();

        $this->addFlash('success', 'Le diagnostique a bien été effectué.');
        // Se rediriger vers notre route "home"
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/website/{id}", name="website_show")
     * @param $id int
     * @return Response
     */
    public function show(Website $website)
    {
        return $this->render('home/show.html.twig', [
            'website' => $website
        ]);
    }
}
