<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityCoordinatesFixture extends Fixture
{
    private const TUNISIA_CITIES = [
        ['name' => 'Tunis', 'lat' => '36.8064', 'lng' => '10.1817'],
        ['name' => 'Sfax', 'lat' => '34.7400', 'lng' => '10.7600'],
        ['name' => 'Sousse', 'lat' => '35.8333', 'lng' => '10.6333'],
        ['name' => 'Kairouan', 'lat' => '35.6781', 'lng' => '10.0963'],
        ['name' => 'Bizerte', 'lat' => '37.2744', 'lng' => '9.8739'],
        ['name' => 'Gabès', 'lat' => '33.8815', 'lng' => '10.0982'],
        ['name' => 'Ariana', 'lat' => '36.8625', 'lng' => '10.1956'],
        ['name' => 'Gafsa', 'lat' => '34.4250', 'lng' => '8.7842'],
        ['name' => 'Monastir', 'lat' => '35.7775', 'lng' => '10.8264'],
        ['name' => 'Nabeul', 'lat' => '36.4561', 'lng' => '10.7350'],
        ['name' => 'Hammamet', 'lat' => '36.4000', 'lng' => '10.6167'],
        ['name' => 'Mahdia', 'lat' => '35.5047', 'lng' => '11.0622'],
        ['name' => 'Tozeur', 'lat' => '33.9197', 'lng' => '8.1339'],
        ['name' => 'Djerba', 'lat' => '33.8076', 'lng' => '10.8451'],
        ['name' => 'Jendouba', 'lat' => '36.5019', 'lng' => '8.7747'],
        ['name' => 'Kasserine', 'lat' => '35.1667', 'lng' => '8.8333'],
        ['name' => 'Kébili', 'lat' => '33.7067', 'lng' => '8.9689'],
        ['name' => 'Manouba', 'lat' => '36.8131', 'lng' => '10.1056'],
        ['name' => 'Médenine', 'lat' => '33.3547', 'lng' => '10.5047'],
        ['name' => 'Sidi Bouzid', 'lat' => '35.0333', 'lng' => '9.4833'],
        ['name' => 'Siliana', 'lat' => '36.0833', 'lng' => '9.3667'],
        ['name' => 'Tataouine', 'lat' => '32.9289', 'lng' => '10.4547'],
        ['name' => 'Zaghouan', 'lat' => '36.4028', 'lng' => '10.1428'],
        ['name' => 'Ben Arous', 'lat' => '36.7667', 'lng' => '10.2333'],
    ];

    public function __construct(private CityRepository $cityRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Update existing cities or create new ones
        foreach (self::TUNISIA_CITIES as $cityData) {
            $city = $this->cityRepository->findOneBy(['name' => $cityData['name']]);
            
            if (!$city) {
                $city = new City();
                $city->setName($cityData['name']);
            }
            
            $city->setLatitude($cityData['lat']);
            $city->setLongitude($cityData['lng']);
            
            $manager->persist($city);
        }

        $manager->flush();
    }
}
