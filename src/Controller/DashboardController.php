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

        // Transform cities to include hotel count and boundary
        $citiesData = [];
        foreach ($cities as $city) {
            $citiesData[] = [
                'id' => $city->getId(),
                'name' => $city->getName(),
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
                'hotelCount' => count($city->getHotels()),
                'boundary' => $city->getBoundary() ? json_decode($city->getBoundary(), true) : null,
            ];
        }

        return $this->render('dashboard/index.html.twig', [
            'cities' => $citiesData,
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

        // Use Post-Redirect-Get to navigate to the hotel list page
        return $this->redirectToRoute('app_select_hotel_list');
    }

    #[Route('/select-hotel', name: 'app_select_hotel_list', methods: ['GET'])]
    public function selectHotelList(Request $request, HotelRepository $hotelRepository): Response
    {
        $cityId = $request->getSession()->get('selected_city');
        if (!$cityId) {
            return $this->redirectToRoute('app_dashboard');
        }

        $checkInDate = $request->getSession()->get('check_in_date');
        $checkOutDate = $request->getSession()->get('check_out_date');

        if (!$checkInDate || !$checkOutDate) {
            $this->addFlash('error', 'Please select both check-in and check-out dates');
            return $this->redirectToRoute('app_select_city', ['cityId' => $cityId]);
        }

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

        // If online payment, set status to pending, otherwise confirmed
        if ($paymentMethod === 'online') {
            $reservation->setStatus('pending');
        } else {
            $reservation->setStatus('confirmed');
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        // If online payment, redirect to payment page
        if ($paymentMethod === 'online') {
            return $this->redirectToRoute('app_online_payment', ['reservationId' => $reservation->getId()]);
        }

        // Clear session for cash payment
        $request->getSession()->remove('selected_city');
        $request->getSession()->remove('check_in_date');
        $request->getSession()->remove('check_out_date');
        $request->getSession()->remove('selected_hotel');

        $this->addFlash('success', 'Reservation confirmed successfully! You can pay in cash at the hotel.');
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

    #[Route('/online-payment/{reservationId}', name: 'app_online_payment')]
    public function onlinePayment(int $reservationId, EntityManagerInterface $entityManager): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found');
        }

        // Verify the reservation belongs to the current user
        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this reservation');
        }

        return $this->render('dashboard/online_payment.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/process-payment', name: 'app_process_payment', methods: ['POST'])]
    public function processPayment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationId = $request->request->get('reservation_id');
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found');
        }

        // Verify the reservation belongs to the current user
        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this reservation');
        }

        // In a real application, you would process the payment with a payment gateway here
        // For now, we'll just simulate a successful payment

        // Update reservation status to confirmed
        $reservation->setStatus('confirmed');
        $entityManager->flush();

        // Clear session
        $request->getSession()->remove('selected_city');
        $request->getSession()->remove('check_in_date');
        $request->getSession()->remove('check_out_date');
        $request->getSession()->remove('selected_hotel');

        $this->addFlash('success', 'Payment successful! Your reservation has been confirmed.');
        return $this->redirectToRoute('app_my_reservations');
    }

    #[Route('/cancel-payment/{reservationId}', name: 'app_cancel_payment')]
    public function cancelPayment(int $reservationId, EntityManagerInterface $entityManager, Request $request): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found');
        }

        // Verify the reservation belongs to the current user
        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have access to this reservation');
        }

        // Delete the pending reservation
        $entityManager->remove($reservation);
        $entityManager->flush();

        // Clear session
        $request->getSession()->remove('selected_city');
        $request->getSession()->remove('check_in_date');
        $request->getSession()->remove('check_out_date');
        $request->getSession()->remove('selected_hotel');

        $this->addFlash('warning', 'Payment cancelled. Your reservation has been removed.');
        return $this->redirectToRoute('app_dashboard');
    }
}

