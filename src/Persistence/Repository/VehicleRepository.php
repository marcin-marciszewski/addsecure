<?php

namespace Persistence\Repository;

use PDO;
use App\SQLiteConnection;
use Domain\Entity\Vehicle;
use Domain\Repository\VehicleRepositoryInterface;

class VehicleRepository implements VehicleRepositoryInterface
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = (new SQLiteConnection())->connect();
    }

    public function getList()
    {
        $results = $this->pdo->query('SELECT * FROM vehicles');

        $items = [];
        foreach ($results as $row) {
            $items[] = $this->rowToEntity($row);
        }

        return $items;
    }

    public function getById($id)
    {
        $stmt=$this->pdo->prepare("SELECT * FROM vehicles WHERE id=?");
        $stmt->execute([$id]); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function deleteById($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM vehicles WHERE id=?");
        $stmt->execute([$id]); 
    }

    public function persist(Vehicle $vehicle)
    {
        $parameters = [
            $vehicle->getRegistrationNumber(), 
            $vehicle->getBrand(), 
            $vehicle->getModel(), 
            $vehicle->getType()
        ];

        if($this->getById($vehicle->getId()) && $vehicle->getId() !== 0){
            $stmt = $this->pdo->prepare("UPDATE vehicles SET registration_number=?, brand=?, model=?, type=?, updated_at=? WHERE id=?");
            $stmt->execute(array_merge($parameters,[$vehicle->getUpdatedAt(), $vehicle->getId()]));

            return $vehicle->getId();
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO  vehicles (registration_number, brand, model, type, created_at, updated_at ) VALUES (?,?,?,?,?,?)");
            $stmt->execute(array_merge($parameters,[$vehicle->getCreatedAt(),$vehicle->getUpdatedAt()])); 

            return $this->pdo->lastInsertId();
        }
        
    }

    private function rowToEntity($row)
    {
        return (new Vehicle())
            ->setId($row['id'])
            ->setRegistrationNumber($row['registration_number'])
            ->setBrand($row['brand'])
            ->setModel($row['model'])
            ->setType($row['type'])
            ->setCreatedAt($row['created_at'])
            ->setUpdatedAt($row['updated_at'])
        ;
    }
}
