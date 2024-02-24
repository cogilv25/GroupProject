<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class LoginMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();

        
            // Validate email and password presence
            if (!isset($data['password'], $data['email'])) {
                return $handler->handle($request);
            }

            // Basic password length check, consider adding more robust validation as needed
            if (strlen($data['password']) < 8) {
                return $handler->handle($request);
            }

            // Validate email format
            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                return $handler->handle($request);
            }

            $container = $request->getAttribute('container');

            $db = $container->get('db');

            if (!$db) {
                return $handler->handle($request);
            }

            $query = $db->prepare("SELECT password, userId FROM user WHERE email = ?");
            $query->bind_param("s", $email);
            $query->execute();
            $query->store_result(); 

            if ($query->num_rows == 0) {
                $query->close();
                return $handler->handle($request);
            }

            $query->bind_result($hashedPassword, $id);
            $query->fetch();
            $query->close();

            // Authenticate the password
            if (!password_verify($data['password'], $hashedPassword)) {
                return $handler->handle($request);
            } else {
                // Creates Session called Logged In with the value of the user email
                $_SESSION['loggedIn'] = $id;
                // Returns a Json response 
                return $this->returnJsonResponse($handler->handle($request), 'Login was successful');
            }
        }
        return $handler->handle($request);
    }
    // Function to create a json return response
    private function returnJsonResponse(Response $response, string $message, int $statusCode = 200): Response
    {
        $responseData = ['message' => $message];
        $response->getBody()->write(json_encode($responseData));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}