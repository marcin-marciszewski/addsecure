<?php

namespace App\Controller;

use Domain\Entity\Vehicle;
use Domain\Service\VehiclesReader;
use Domain\Service\VehiclesWriter;
use Domain\Service\VehiclesBuilder;
use Persistence\Repository\VehicleRepository;

use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

class VehicleController extends BaseController
{
    public function index(): Response
    {
        ob_start();
        include __DIR__ . '/../views/index.php';
        return (new Response(ob_get_clean()))->send();
    }

    public function list(): JsonResponse
    {
        $results = (new VehiclesBuilder(new VehicleRepository()))->getList();

        return $this->toJsonResponse(['results' => $results]);
    }

    public function save(int $id, Request $request): JsonResponse
    {
        $content = json_decode($request->getContent());
      
        $newVehicle  = (new VehiclesBuilder(new VehicleRepository()))->contentToDTO($id,$content);
        $newVehicle = (new VehiclesWriter(new VehicleRepository()))->saveVehicle($newVehicle);
     
        
        return $this->toJsonResponse((new VehiclesReader(new VehicleRepository()))->getVehicleById($newVehicle));
    }

    public function delete(int $id): JsonResponse
    {
        (new VehiclesWriter(new VehicleRepository()))->deleteById($id);
        return $this->toJsonResponse(["Vehicle with id {$id} removed."]);
    }
}
