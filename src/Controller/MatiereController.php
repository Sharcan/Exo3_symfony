<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatiereController extends AbstractController
{
    /**
     * @Route("/matieres", name="matieres")
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {

        $matiere = new Matiere();

        $formMatiere = $this->createForm(MatiereType::class, $matiere);
        $formMatiere->handleRequest($request);

        $matiereRepository = $this->getDoctrine()->getRepository(Matiere::class);
        $allMatieres = $matiereRepository->findAll();

        if($formMatiere->isSubmitted() && $formMatiere->isValid()){

            $matiere = $formMatiere->getData();

            $entityManager->persist($matiere);
            $entityManager->flush();

            return $this->redirectToRoute('matieres');
        }

        return $this->render('matiere/index.html.twig', [
            'matiereForm' => $formMatiere->createView(),
            'matieres' => $allMatieres
        ]);
    }

    /**
     * @Route("/matiere/{id}", name="selectedMatiere")
     */
    public function selectedMatiere($id, EntityManagerInterface $entityManager, Request $request)
    {
        $matiere = $this->getDoctrine()->getRepository(Matiere::class)->find($id);

        $updateMatiere = $this->createForm(MatiereType::class, $matiere);
        $updateMatiere->handleRequest($request);

        if($updateMatiere->isSubmitted() && $updateMatiere->isValid()){

            $matiere = $updateMatiere->getData();

            $entityManager->persist($matiere);
            $entityManager->flush();

            return $this->redirectToRoute('matieres');

        }

        return $this->render('matiere/matiere.html.twig', [
            'updateMatiere' => $updateMatiere->createView(),
            'matiere' => $matiere
        ]);
    }

    /**
     * @Route("/removematiere/{id}", name="removeMatiere")
     */
    public function removeMatiere($id, EntityManagerInterface $entityManager, Request $request)
    {
        $matiere = $this->getDoctrine()->getRepository(Matiere::class)->find($id);

        $entityManager->remove($matiere);
        $entityManager->flush();

        return $this->redirectToRoute('matieres');
    }
}
