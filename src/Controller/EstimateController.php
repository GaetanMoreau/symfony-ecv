<?php

namespace App\Controller;

use App\Entity\Estimate;
use App\Form\EstimateFormType;
use App\Repository\EstimateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EstimateController extends AbstractController
{
    #[Route('/estimate', name: 'app_estimate')]
    public function index(): Response
    {
        $user = $this->getUser();
        $estimates = $user->getEstimates();

        return $this->render('estimate/index.html.twig', [
            'estimates' => $estimates
        ]);
    }

    #[Route('/estimate/add', name: 'app_estimate_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $estimate = new Estimate();

        $form = $this->createForm(EstimateFormType::class, $estimate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $estimate = $form->getData();
            $estimate->setUser($this->getUser());
            $estimate->setCreatedAt(new \DateTimeImmutable());
            $em->persist($estimate);
            $em->flush();
            // dd($estimate);
            return $this->redirectToRoute('app_estimate');
        }

        return $this->render('estimate/add.html.twig', [
            'controller_name' => 'EstimateController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/estimate/{id}', name: 'app_estimate_show')]
    public function show(EstimateRepository $er, Request $request): Response
    {
        $estimateId = $request->get('id');
        $estimate = $er->find($estimateId);

        return $this->render('estimate/show.html.twig', [
            'estimate' => $estimate,
        ]);
    }

    #[Route('/estimate/{id}/edit', name: 'app_estimate_edit')]
    public function edit(Estimate $invoice, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EstimateFormType::class, $invoice);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $estimate = $form->getData();
            $estimate->setUser($this->getUser());
            $estimate->setCreatedAt(new \DateTimeImmutable());
            $em->persist($estimate);
            $em->flush();
            // dd($invoice);
            return $this->redirectToRoute('app_estimate');
        }
        
        return $this->render('estimate/edit.html.twig', [
            'controller_name' => 'EstimateController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/estimate/{id}/delete', name: 'app_estimate_delete')]
    public function delete(EstimateRepository $er, Request $request, EntityManagerInterface $em): Response
    {
        $estimateId = $request->get('id');
        $estimate = $er->find($estimateId);

        $em->remove($estimate);
        $em->flush();

        return $this->redirectToRoute('app_estimate');
    }

}