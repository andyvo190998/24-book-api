<?php

namespace App\Controller;

use App\Entity\BookName;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $books = $entityManager->getRepository(BookName::class)->findAll();
        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'name' => $book->getName(),
                'author' => $book->getAuthor(),
                'year' => $book->getYear(),
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/book', name: 'create_book', methods: ['POST'])]
    public function createBook(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data['name'] || !$data['year'] || !$data['author']) {
            return new JsonResponse([
                'status' => 'Invalid body',
                400
            ]);
        }
        $new_book = new BookName();
        $new_book->setName($data['name']);
        $new_book->setYear($data['year']);
        $new_book->setAuthor($data['author']);
        $entityManager->persist($new_book);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'created successfully',
            'data' => $data,
        ]);
    }

    #[ROUTE('/book/{id}', name: 'edit_book', methods: ['PUT'])]
    public function editBook(EntityManagerInterface $entityManager, Request $request, int $id, SerializerInterface $serializer): JsonResponse
    {

        if (!$id) {
            return new JsonResponse(['message' => 'id not found'], Response::HTTP_NOT_FOUND);
        }

        $books = $entityManager->getRepository(BookName::class)->find($id);
        if (!$books) {
            return new JsonResponse(['message' => 'book not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();
        try {
            $serializer->deserialize($data, BookName::class, 'json', [
                'object_to_populate' => $books,
            ]);
            $entityManager->flush();
        } catch (ExceptionInterface $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse([
            'message' => 'updated successfully',
            'data' => [
                'id' => $books->getId(),
                'name' => $books->getName(),
                'author' => $books->getAuthor(),
                'year' => $books->getYear(),
            ],
        ], 200);
    }

    #[Route('/book/{id}', name: 'get_book', methods: ['GET'])]
    public function getBook(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$id) {
            return new JsonResponse(['message' => 'id not found'], Response::HTTP_NOT_FOUND);
        }

        $books = $entityManager->getRepository(BookName::class)->find($id);
        if (!$books) {
            return new JsonResponse(['message' => 'book not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'message' => 'get book successfully',
            'data' => [
                'id' => $books->getId(),
                'name' => $books->getName(),
                'author' => $books->getAuthor(),
                'year' => $books->getYear(),
            ]
        ]);
    }

    #[Route('/book/{id}', name: 'delete_book', methods: ['DELETE'])]
    public function deleteBook(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$id) {
            return new JsonResponse(['message' => 'id not found'], Response::HTTP_NOT_FOUND);
        }

        $books = $entityManager->getRepository(BookName::class)->find($id);
        if (!$books) {
            return new JsonResponse(['message' => 'book not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($books);
        $entityManager->flush();
        return new JsonResponse([
            'message' => 'deleted successfully'
        ]);
    }
}
