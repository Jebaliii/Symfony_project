<?php

namespace App\Controller\Admin;

use App\Entity\Hotel;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/hotels')]
class HotelController extends AbstractController
{
    #[Route('/', name: 'admin_hotel_index')]
    public function index(HotelRepository $hotelRepository): Response
    {
        $hotels = $hotelRepository->findAll();

        return $this->render('admin/hotel/index.html.twig', ['hotels' => $hotels]);
    }

    #[Route('/new', name: 'admin_hotel_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($hotel);
            $em->flush();

            $this->addFlash('success', 'Hotel created successfully');
            return $this->redirectToRoute('admin_hotel_index');
        }

        return $this->render('admin/hotel/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}', name: 'admin_hotel_show')]
    public function show(Hotel $hotel): Response
    {
        return $this->render('admin/hotel/show.html.twig', ['hotel' => $hotel]);
    }

    #[Route('/{id}/edit', name: 'admin_hotel_edit')]
    public function edit(Hotel $hotel, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Hotel updated successfully');
            return $this->redirectToRoute('admin_hotel_index');
        }

        return $this->render('admin/hotel/edit.html.twig', ['form' => $form->createView(), 'hotel' => $hotel]);
    }

    #[Route('/{id}/delete', name: 'admin_hotel_delete', methods: ['POST'])]
    public function delete(Hotel $hotel, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-hotel-'.$hotel->getId(), $request->request->get('_token'))) {
            $em->remove($hotel);
            $em->flush();
            $this->addFlash('success', 'Hotel deleted');
        }

        return $this->redirectToRoute('admin_hotel_index');
    }
}
