<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;


class RegistrationMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();

            // Validation checks
            if (!(isset($data['password'], $data['email'], $data['forename'], $data['surname'], $data['confirmPassword']))) {
                return $handler->handle($request);
            }

            if (strlen($data['password']) < 8 || $data['password'] !== $data['confirmPassword']) {
                return $handler->handle($request);
            }

            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                return $handler->handle($request);
            }

            $container = $request->getAttribute('container');
            $db = $container->get('db');

            if (!$db) {
                return $handler->handle($request);
            }

            $selectQuery = $db->prepare("SELECT email FROM user WHERE email = ?");
            $selectQuery->bind_param("s", $email);
            $selectQuery->execute();
            $selectQuery->store_result(); 
            if ($selectQuery->num_rows > 0) {
                $selectQuery->close();
                return $handler->handle($request);
            }

            $selectQuery->close();

            // Hash the password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert new user
            $insertQuery = $db->prepare("INSERT INTO user (forename, surname, email, password) VALUES (?, ?, ?, ?)");
            $insertQuery->bind_param("ssss", $data['forename'], $data['surname'], $email, $hashedPassword);
            $insertQuery->execute();

            if ($insertQuery->affected_rows === 0) {
                // Insert failed, handle error
                $insertQuery->close();
                return $handler->handle($request);
            }  else {
                $insertQuery->close();
                // Creates Session called Logged In with the value of the user email
                $_SESSION['loggedIn'] = $email;
                // Returns a Json response 
                return $this->returnJsonResponse($handler->handle($request), 'Signup was successful');
            }
            return $handler->handle($request);
    }
}
    private function returnJsonResponse(Response $response, string $message, int $statusCode = 200): Response
    {
        $responseData = ['message' => $message];
        $response->getBody()->write(json_encode($responseData));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}