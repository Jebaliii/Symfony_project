<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\CityRepository;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'cities' => $cities,
        ]);
    }

    #[Route('/select-city/{cityId}', name: 'app_select_city')]
    public function selectCity(int $cityId, CityRepository $cityRepository, Request $request): Response
    {
        $city = $cityRepository->find($cityId);
        if (!$city) {
            throw $this->createNotFoundException('City not found');
        }

        // Store city in session
        $request->getSession()->set('selected_city', $cityId);

        return $this->render('dashboard/select_date.html.twig', [
            'city' => $city,
        ]);
    }

    #[Route('/select-date', name: 'app_select_date', methods: ['POST'])]
    public function selectDate(Request $request, HotelRepository $hotelRepository): Response
    {
        $cityId = $request->getSession()->get('selected_city');
        if (!$cityId) {
            return $this->redirectToRoute('app_dashboard');
        }

        $checkInDate = $request->request->get('check_in_date');
        $checkOutDate = $request->request->get('check_out_date');

        if (!$checkInDate || !$checkOutDate) {
            $this->addFlash('error', 'Please select both check-in and check-out dates');
            return $this->redirectToRoute('app_select_city', ['cityId' => $cityId]);
        }

        // Store dates in session
        $request->getSession()->set('check_in_date', $checkInDate);
        $request->getSession()->set('check_out_date', $checkOutDate);

        // Get available hotels for the selected city
        $hotels = $hotelRepository->findByCity($cityId);

        return $this->render('dashboard/select_hotel.html.twig', [
            'hotels' => $hotels,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
        ]);
    }

    #[Route('/select-hotel/{hotelId}', name: 'app_select_hotel')]
    public function selectHotel(int $hotelId, HotelRepository $hotelRepository, Request $request): Response
    {
        $hotel = $hotelRepository->find($hotelId);
        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }

        // Store hotel in session
        $request->getSession()->set('selected_hotel', $hotelId);

        $checkInDate = $request->getSession()->get('check_in_date');
        $checkOutDate = $request->getSession()->get('check_out_date');

        return $this->render('dashboard/select_payment.html.twig', [
            'hotel' => $hotel,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
        ]);
    }

    #[Route('/confirm-reservation', name: 'app_confirm_reservation', methods: ['POST'])]
    public function confirmReservation(
        Request $request,
        HotelRepository $hotelRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $hotelId = $request->getSession()->get('selected_hotel');
        $checkInDate = $request->getSession()->get('check_in_date');
        $checkOutDate = $request->getSession()->get('check_out_date');
        $paymentMethod = $request->request->get('payment_method');

        if (!$hotelId || !$checkInDate || !$checkOutDate || !$paymentMethod) {
            $this->addFlash('error', 'Invalid reservation data');
            return $this->redirectToRoute('app_dashboard');
        }

        $hotel = $hotelRepository->find($hotelId);
        if (!$hotel) {
            throw $this->createNotFoundException('Hotel not found');
        }

        // Create reservation
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setHotel($hotel);
        $reservation->setCheckInDate(new \DateTime($checkInDate));
        $reservation->setCheckOutDate(new \DateTime($checkOutDate));
        $reservation->setPaymentMethod($paymentMethod);
        $reservation->setStatus('confirmed');

        $entityManager->persist($reservation);
        $entityManager->flush();

        // Clear session
        $request->getSession()->remove('selected_city');
        $request->getSession()->remove('check_in_date');
        $request->getSession()->remove('check_out_date');
        $request->getSession()->remove('selected_hotel');

        $this->addFlash('success', 'Reservation confirmed successfully!');
        return $this->redirectToRoute('app_my_reservations');
    }

    #[Route('/my-reservations', name: 'app_my_reservations')]
    public function myReservations(): Response
    {
        $user = $this->getUser();
        $reservations = $user->getReservations();

        return $this->render('dashboard/my_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}

