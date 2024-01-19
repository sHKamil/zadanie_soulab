<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Validator\MessageValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('/api/message', name: 'app_api', methods: ['GET'])]
    public function fetchAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Messages::class);
        $allMessages = $repository->findAll();
        $result = [];
        foreach ($allMessages as $message) {
            $result[] = [
                'id' => $message->getId(),
                'name' => $message->getName(),
                'last_name' => $message->getLastName(),
                'email'=> $message->getEmail(),
                'message' => $message->getMessage()
            ];
        }

        return $this->json($result);
    }

    #[Route('/api/message', name: 'app_api_message', methods: ['POST'])]
    public function submitMessage(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $messages = new Messages;

        $data = json_decode($request->getContent(), true);
        $messange_validator = new MessageValidator;
        $validation_result = $messange_validator->Validate($data);
        if($validation_result === true) {
            $messages->setName($data['name']);
            $messages->setLastName($data['last_name']);
            $messages->setEmail($data['email']);
            $messages->setMessage($data['message']);

            $entityManager->beginTransaction();
            try {
                $entityManager->persist($messages);
                $entityManager->flush();
                $entityManager->commit();
            } catch (\Exception $exception) {
                $entityManager->rollback();
                return new JsonResponse([
                    'status' => 'Faliure',
                    'message' => $exception->getMessage()
                ]);
            }
            return $this->json([
                'status' => '201',
                'message' => 'Success'
            ]);
        } else {
            return new JsonResponse([
                'status' => '400',
                'message' => 'Validation error',
                'errors' => $validation_result
            ], 400);
        }
    }

    #[Route('/api/message/{id}', name: 'app_api_delete', methods: ['DELETE'])]
    public function deleteById(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $repository = $entityManager->getRepository(Messages::class);
        $entity = $repository->find($id);
        if (!$entity) {
            return new JsonResponse([
                'status' => '400',
                'message' => 'Message not found',
            ], 400);
        }
        $entityManager->remove($entity);
        $entityManager->flush();

        return new JsonResponse([
            'status' => '200',
            'message' => 'Message deleted',
        ], 200);
    }
}
