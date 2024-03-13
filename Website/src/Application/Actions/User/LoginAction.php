<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class LoginAction extends Action
{

    protected function action(): Response
    {
        //Check if user is already logged in.
        if($this->request->getAttribute('userId') != 0)
            throw new HttpMethodNotAllowedException($this->request, "Already logged in");

        $data = $this->request->getParsedBody();

        // Validate email and password presence
        if (!isset($data['password'], $data['email']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        // Basic password length check, consider adding more robust validation as needed
        if (strlen($data['password']) < 8)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        // Validate email format
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$email)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        // Check user exists
        $user = $this->db->getUserIdAndPasswordHash($email);
        if ($user == false)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        //Authenticate Password
        if(!password_verify($data['password'], $user['passwordHash']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        // Creates Session called Logged In with the value of the userId
        $_SESSION['loggedIn'] = $user['id'];
        // Returns a Json response 
        return $this->createJsonResponse($this->response, 'Login was successful');
    }
}
