<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/reservations')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'admin_reservation_index')]
    public function index(ReservationRepository $repository): Response
    {
        $reservations = $repository->findAll();
        return $this->render('admin/reservation/index.html.twig', ['reservations' => $reservations]);
    }

    #[Route('/new', name: 'admin_reservation_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Reservation created');
            return $this->redirectToRoute('admin_reservation_index');
        }

        return $this->render('admin/reservation/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}', name: 'admin_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservation/show.html.twig', ['reservation' => $reservation]);
    }

    #[Route('/{id}/edit', name: 'admin_reservation_edit')]
    public function edit(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Reservation updated');
            return $this->redirectToRoute('admin_reservation_index');
        }

        return $this->render('admin/reservation/edit.html.twig', ['form' => $form->createView(), 'reservation' => $reservation]);
    }

    #[Route('/{id}/delete', name: 'admin_reservation_delete', methods: ['POST'])]
    public function delete(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-reservation-'.$reservation->getId(), $request->request->get('_token'))) {
            $em->remove($reservation);
            $em->flush();
            $this->addFlash('success', 'Reservation deleted');
        }

        return $this->redirectToRoute('admin_reservation_index');
    }
}
