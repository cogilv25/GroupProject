<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class RegisterAction extends Action
{

    protected function action(): Response
    {
        //Check if user is logged in and if so throw an Exception
        $loggedIn = $this->request->getAttribute('loggedIn');
        if($loggedIn != false)
            throw new HttpMethodNotAllowedException($this->request, "Already logged in");

        $data = $this->request->getParsedBody();

        // Validation checks
        if (!(isset($data['password'], $data['email'], $data['forename'], $data['surname'], $data['confirmPassword'])))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        if (strlen($data['password']) < 8 || $data['password'] !== $data['confirmPassword'])
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $db = $this->container->get('db');

        // Check if email unavailable
        $id = $db->getUserId($email);
        if ($id != false)
            throw new HttpBadRequestException($this->request, "Cannot create an account with that email address" . $id );

        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        //Insert new user into user table
        $id = $db->createUser($data['forename'], $data['surname'], $email, $hashedPassword);

        //TODO: Create Exception for unreachables
        if(!$id)
            return $this->createJsonResponse($this->response, ['message' => 'Registration failed']);

        // Creates Session called Logged In with the value of the userId
        $_SESSION['loggedIn'] = $id;
        // Returns a Json response 
        return $this->createJsonResponse($this->response, ['message' => 'Registration was successful']);
    }
}