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
        //Check if user is logged in.
        if($this->request->getAttribute('userId') != 0)
            throw new HttpMethodNotAllowedException($this->request, "Already logged in");

        $data = $this->request->getParsedBody();

        // Validation checks
        if (!(isset($data['password'], $data['email'], $data['forename'], $data['surname'], $data['confirmPassword'])))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        //TODO: Enforce more robust passwords
        if (strlen($data['password']) < 8 || $data['password'] !== $data['confirmPassword'])
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        //Pre-database string length validation to give users useful errors
        //TODO: The useful error messages... @ErrorHandling
        //TODO: Maybe insert [blank] if forename/surname not provided to prevent weird interface bugs?
        if(strlen($email)>64 || strlen($data['forename'])>32 || strlen($data['surname'])>32)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        // Check if email unavailable
        $id = $this->db->getUserId($email);
        if ($id != false)
            throw new HttpBadRequestException($this->request, "Cannot create an account with that email address" . $id );

        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        //Insert new user into user table
        $id = $this->db->createUser($data['forename'], $data['surname'], $email, $hashedPassword);

        //TODO: Create Exception for unreachables
        if(!$id)
            return $this->createJsonResponse($this->response, ['message' => 'Registration failed']);

        // Creates Session called Logged In with the value of the userId
        $_SESSION['loggedIn'] = $id;
        // Returns a Json response 
        return $this->createJsonResponse($this->response, ['message' => 'Registration was successful']);
    }
}
