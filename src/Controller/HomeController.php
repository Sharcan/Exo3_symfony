<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Entity\Note;
use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {

        $matiereRepository = $this->getDoctrine()->getRepository(Matiere::class);
        $allMatieres = $matiereRepository->findAll();

        $note = new Note();

        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);

        $noteRepository = $this->getDoctrine()->getRepository(Note::class);
        $allNotes = $noteRepository->findAll();

        $moyenne = 0;
        $totalCoeff = 0;
        $total = 0;
        if($allNotes){
            foreach($allNotes as $note){
                $noteBdd = $note->getNote();
                $coefficient = $note->getMatiere()->getCoefficient();
                $moyenne = $moyenne + ($noteBdd * $coefficient);
                $totalCoeff = $totalCoeff + $coefficient;
            }
            $total = $moyenne / $totalCoeff;
        }
        else {
            $total = 0;
        }

        if($noteForm->isSubmitted() && $noteForm->isValid()){

            $note = $noteForm->getData();

            $note->setAdded(new \DateTime());
            $matiereID = $matiereRepository->find($request->request->get('matiereId'));
            $note->setMatiere($matiereID);


            $entityManager->persist($note);
            $entityManager->flush();

            $this->redirectToRoute('home');
        }

        dump($allMatieres, $allNotes);

        return $this->render('home/index.html.twig', [
            'matieres' => $allMatieres,
            'noteForm' => $noteForm->createView(),
            'notes' => $allNotes,
            'moyenne' => $total
        ]); 
    }
}
