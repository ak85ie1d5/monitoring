<?php

namespace App\Controller;

use App\Entity\Website;
use App\Form\WebsiteType;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(WebsiteRepository $websiteRepository): Response
    {
        $websites = $websiteRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'websites' => $websites
        ]);
    }

    /**
     * @Route("/admin/new", name="admin_new")
     */
    public function new(Request $request, EntityManagerInterface $manager)
    {
        $website = new Website();
        $form = $this->createForm(WebsiteType::class, $website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($website);
            $manager->flush();
            $this->addFlash('success', 'Site web ajouté avec succès');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{id}/remove", name="admin_remove")
     * @param EntityManagerInterface $entityManager
     * @param Website $website
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(EntityManagerInterface $entityManager, Website $website)
    {
        $entityManager->remove($website);
        $entityManager->flush();
        $this->addFlash('warning', 'Site web supprimé avec succès');

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/admin/{id}/edit", name="admin_edit")
     * @param Website $website
     * @return Response
     */
    public function edit(Website $website, EntityManagerInterface $entityManager, Request $request)
    {
        $form = $this->createForm(WebsiteType::class, $website);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($website);
            $entityManager->flush();
            $this->addFlash('success', 'Site web modifié avec succès');
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('/admin/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
