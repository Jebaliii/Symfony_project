<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\Hotel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed:cities',
    description: 'Seed the database with Tunisian cities and hotels',
)]
class SeedCitiesCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Seeding cities and hotels...');

        // List of 24 Tunisian cities
        $cities = [
            'Tunis', 'Sfax', 'Sousse', 'Kairouan', 'Gabès', 'Gafsa', 'Jendouba', 'Kasserine',
            'Kébili', 'Mahdia', 'Manouba', 'Médenine', 'Monastir', 'Nabeul', 'Sidi Bouzid',
            'Siliana', 'Tataouine', 'Tozeur', 'Zaghouan', 'Ariana', 'Ben Arous', 'Bizerte',
            'Djerba', 'Hammamet'
        ];

        foreach ($cities as $cityName) {
            $city = new City();
            $city->setName($cityName);

            // Add 3-5 hotels per city
            $hotelCount = rand(3, 5);
            for ($i = 1; $i <= $hotelCount; $i++) {
                $hotel = new Hotel();
                $hotel->setName("Hotel $cityName $i");
                $hotel->setDescription("A beautiful hotel in $cityName with excellent amenities and services.");
                $hotel->setAddress("Street $i, $cityName, Tunisia");
                $hotel->setPricePerNight((string)rand(50, 200));
                $hotel->setAvailableRooms(rand(5, 20));
                $hotel->setCity($city);

                $city->addHotel($hotel);
            }

            $this->entityManager->persist($city);
        }

        $this->entityManager->flush();

        $output->writeln('<info>Successfully seeded 24 cities with hotels!</info>');
        return Command::SUCCESS;
    }
}

