<?php

namespace App\Controller;

use App\Repository\CarDataRepository;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Model\DataObject\Car;
use Pimcore\Model\DataObject\Car\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/models')]
class CarController extends AbstractController
{
    #[Route('/{id}', name: 'api_cars', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(string $id, ApplicationLogger $logger): JsonResponse
    {
        try {
                $car = Car::getById($id);

                if (!$car) {
                    $logger->error('Car not found', [
                        'relatedObject' => get_class($this)
                    ]);
                    return $this->json(['error' => 'Car not found'], 404);
                }

                return $this->json([
                    'status' => 'success',
                    'data' => CarDataRepository::getCar($car),
                ]);

        } catch (\Exception $e) {
            $logger->error('Error fetching car data' . $e->getMessage(), [
                'relatedObject' => get_class($this),
            ]);
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }

    #[Route('/search', name: 'api_cars_search', methods: ['GET'])]
    public function search(Request $request, ApplicationLogger $logger): JsonResponse
    {
        try {
            $query = $request->query->get('query', '');

            if (empty($query)) {
                $logger->warning('Search query is empty', [
                    'relatedObject' => get_class($this),
                ]);
                return $this->json(['error' => 'Query parameter is required'], 400);
            }

            $carListing = new Listing();
            $carListing->setCondition('Model LIKE ?', ["%{$query}%"]);
            $carListing->setUnpublished(true);
            $cars = $carListing->load();

            return $this->json([
                'status' => 'success',
                'data' => CarDataRepository::getCars($cars),
            ]);

        } catch (\Exception $e) {
            $logger->error('Error during search operation: ' . $e->getMessage(), [
                'relatedObject' => get_class($this),
            ]);
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }
}