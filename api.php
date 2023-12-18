<?php
require 'config.php';
require 'vendor/autoload.php';

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API de Cartas de Pokémon",
 *     version="1.0.0"
 * )
 */

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$request = explode('/', trim($requestUri, '/'));

switch ($method) {
    case 'GET':
        if (isset($request[0]) && $request[0] === 'cards') {
            if (isset($request[1])) {
                /**
                 * @OA\Get(
                 *     path="/cards/{id}",
                 *     summary="Obtener una carta específica",
                 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
                 *     @OA\Response(response="200", description="Devuelve una carta específica")
                 * )
                 */
                // Obtener una carta específica por ID
                $cardId = $request[1];
                $stmt = $pdo->prepare("SELECT * FROM cards WHERE id = ?");
                $stmt->execute([$cardId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($result);
            } else {
                /**
                 * @OA\Get(
                 *     path="/cards",
                 *     summary="Obtener todas las cartas",
                 *     @OA\Response(response="200", description="Devuelve todas las cartas")
                 * )
                 */
                // Obtener todas las cartas
                $stmt = $pdo->query("SELECT * FROM cards");
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
        }
        break;
    case 'POST':
        if (isset($request[0]) && $request[0] === 'cards') {
            /**
             * @OA\Post(
             *     path="/cards",
             *     summary="Crear una nueva carta",
             *     @OA\RequestBody(
             *         @OA\MediaType(mediaType="application/json",
             *             @OA\Schema(
             *                 type="object",
             *                 @OA\Property(property="name", type="string"),
             *                 @OA\Property(property="hp", type="integer"),
             *                 @OA\Property(property="first_edition", type="boolean"),
             *                 @OA\Property(property="expansion", type="string"),
             *                 @OA\Property(property="type", type="string"),
             *                 @OA\Property(property="rarity", type="string"),
             *                 @OA\Property(property="price", type="number"),
             *                 @OA\Property(property="image_url", type="string")
             *             )
             *         )
             *     ),
             *     @OA\Response(response="200", description="Carta creada")
             * )
             */
            // Crear una nueva carta
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("INSERT INTO cards (name, hp, first_edition, expansion, type, rarity, price, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['hp'],
                $data['first_edition'],
                $data['expansion'],
                $data['type'],
                $data['rarity'],
                $data['price'],
                $data['image_url']
            ]);
            echo json_encode(['message' => 'Carta creada']);
        }
        break;
    case 'PUT':
        if (isset($request[0]) && $request[0] === 'cards' && isset($request[1])) {
            /**
             * @OA\Put(
             *     path="/cards/{id}",
             *     summary="Actualizar una carta por ID",
             *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
             *     @OA\RequestBody(
             *         @OA\MediaType(mediaType="application/json",
             *             @OA\Schema(
             *                 type="object",
             *                 @OA\Property(property="name", type="string"),
             *                 @OA\Property(property="hp", type="integer"),
             *                 @OA\Property(property="first_edition", type="boolean"),
             *                 @OA\Property(property="expansion", type="string"),
             *                 @OA\Property(property="type", type="string"),
             *                 @OA\Property(property="rarity", type="string"),
             *                 @OA\Property(property="price", type="number"),
             *                 @OA\Property(property="image_url", type="string")
             *             )
             *         )
             *     ),
             *     @OA\Response(response="200", description="Carta actualizada")
             * )
             */
            // Actualizar una carta por ID
            $cardId = $request[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("UPDATE cards SET name=?, hp=?, first_edition=?, expansion=?, type=?, rarity=?, price=?, image_url=? WHERE id = ?");
            $stmt->execute([
                $data['name'],
                $data['hp'],
                $data['first_edition'],
                $data['expansion'],
                $data['type'],
                $data['rarity'],
                $data['price'],
                $data['image_url'],
                $cardId
            ]);
            echo json_encode(['message' => 'Carta actualizada']);
        }
        break;
    case 'DELETE':
        if (isset($request[0]) && $request[0] === 'cards' && isset($request[1])) {
            /**
             * @OA\Delete(
             *     path="/cards/{id}",
             *     summary="Borrar una carta por ID",
             *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
             *     @OA\Response(response="200", description="Carta borrada")
             * )
             */
            // Borrar una carta por ID
            $cardId = $request[1];
            $stmt = $pdo->prepare("DELETE FROM cards WHERE id = ?");
            $stmt->execute([$cardId]);
            echo json_encode(['message' => 'Carta borrada']);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        exit;
}
