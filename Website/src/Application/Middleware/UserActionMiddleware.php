<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UserActionMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        //TODO: User feedback
        if($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();

            if(!isset($data['formName']))
                return $handler->handle($request);
            if(!(isset($data['password']) && isset($data['email'])))
                return $handler->handle($request);
            if(strlen($data['password']) < 8)
                return $handler->handle($request);

            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            $pword = $data['password'];

            if($email == null)
                return $handler->handle($request);

            $container = $request->getAttribute('container');

            $db = $container->get('db');

            if($db == null)
                return $handler->handle($request);
            if($data['formName'] == "login")
            {
                //Prepare the query
                $query = $db->prepare("select password, forename from user where email = ?");
                $query->bind_param("s",$email);
                $query->bind_result($hashedPword, $fname);

                //Execute query
                $query->execute();
                $query->fetch();
                $query->close();

                if($hashedPword == null)
                    return $handler->handle($request);

                //Authenticate password
                if(!password_verify($pword, $hashedPword))
                    return $handler->handle($request);

                $_SESSION['user'] = $fname;
                $_SESSION['email'] = $email;
            }
            elseif($data['formName'] == 'register')
            {
                if(!(isset($data['forename'])&&isset($data['surname'])))
                    return $handler->handle($request);

                $fname = $data['forename'];
                $sname = $data['surname'];

                //Check that email is not already in use
                //Prepare the select query
                $selectQuery = $db->prepare("select email from user where email = ?");
                $selectQuery->bind_param("s",$email);
                $selectQuery->bind_result($userEmailIfExists);

                //Execute
                $selectQuery->execute();
                $selectQuery->fetch();
                $selectQuery->fetch(); //Try removing? there used to be a bug, may be fixed (It's still weird but works...)

                if($userEmailIfExists != null)
                    return $handler->handle($request);
                
                //Hash the password
                $pword = password_hash($pword, PASSWORD_DEFAULT);

                //Prepare the insert query
                $insertQuery = $db->prepare("insert into user (forename, surname, email, password) values (?,?,?,?)");
                $insertQuery->bind_param("ssss",$fname,$sname,$email,$pword);

                //Execute the insert query
                $insertQuery->execute();
                $insertQuery->close();

                //Confirm the user was created
                $selectQuery->execute();
                $selectQuery->fetch();

                if($userEmailIfExists == null)
                    return $handler->handle($request);


                $_SESSION['user'] = $fname;
                $_SESSION['email'] = $email;

                
            }
        }


            
        return $handler->handle($request);
    }
}
