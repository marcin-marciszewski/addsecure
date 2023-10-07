<?php

namespace Domain\Service;

use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehiclesWriter
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository)
    {
    }

    public function saveVehicle(VehicleDTO $vehicleDTO)
    {
       return $this->vehicleRepository->persist($this->dtoToEntity($vehicleDTO));
    }

    public function deleteById($id)
    {
        return $this->vehicleRepository->deleteById($id);

    }

    private function dtoToEntity($dto){
        $vehicle = new Vehicle();
        $vehicle->setId($dto->id);
        $vehicle->setRegistrationNumber($dto->registrationNumber);
        $vehicle->setBrand( $dto->brand);
        $vehicle->setModel( $dto->model);
        $vehicle->setType($dto->type );
        $vehicle->setCreatedAt($dto->createdAt);
        $vehicle->setUpdatedAt($dto->updatedAt);

        return $vehicle;
    }
}
