<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehiclesBuilder
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function getList()
    {
        $items = $this->vehicleRepository->getList();

        return array_map([$this, 'entityToDTO'], $items);
    }

    private function entityToDTO(Vehicle $vehicle)
    {
        $vehicleDTO = new VehicleDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->registrationNumber = $vehicle->getRegistrationNumber();
        $vehicleDTO->brand = $vehicle->getBrand();
        $vehicleDTO->model = $vehicle->getModel();
        $vehicleDTO->type = $vehicle->getType();
        $vehicleDTO->createdAt = $vehicle->getCreatedAt();
        $vehicleDTO->updatedAt = $vehicle->getUpdatedAt();

        return $vehicleDTO;
    }

    public function contentToDTO($id, $content)
    {
      
        $vehicleDTO = new VehicleDTO();
        $vehicleDTO->id = $id;
        $vehicleDTO->registrationNumber = $content->registrationNumber;
        $vehicleDTO->brand = $content->brand;
        $vehicleDTO->model = $content->model;
        $vehicleDTO->type = $content->type;
        $vehicleDTO->updatedAt =$content->updatedAt;
        $vehicleDTO->createdAt = $content->createdAt;

        return $vehicleDTO;
    }
    

}
