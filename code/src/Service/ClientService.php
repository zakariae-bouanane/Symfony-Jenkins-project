<?php

namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Save or update a client entity
     *
     * @param Client $client
     */
    public function saveClient(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    /**
     * Delete a client entity
     *
     * @param Client $client
     */
    public function deleteClient(Client $client): void
    {
        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }

    /**
     * Update a client entity (flush changes)
     *
     * @param Client $client
     */
    public function updateClient(Client $client): void
    {
        // Since the client is already being tracked by Doctrine,
        // only flush is needed to persist changes
        $this->entityManager->flush();
    }
}

?>