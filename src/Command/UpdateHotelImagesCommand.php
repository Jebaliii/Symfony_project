<?php

namespace App\Command;

use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update:hotel-images',
    description: 'Update existing hotels with placeholder images',
)]
class UpdateHotelImagesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HotelRepository $hotelRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Updating hotel images...');

        $hotels = $this->hotelRepository->findAll();
        $updated = 0;

        foreach ($hotels as $hotel) {
            // Generate a unique seed based on hotel ID and name
            $randomSeed = md5($hotel->getName() . $hotel->getId());
            $imageUrl = "https://source.unsplash.com/800x600/?hotel,luxury,resort&sig={$randomSeed}";
            
            $hotel->setImageUrl($imageUrl);
            $updated++;
        }

        $this->entityManager->flush();

        $io->success("Successfully updated {$updated} hotels with images!");
        return Command::SUCCESS;
    }
}

