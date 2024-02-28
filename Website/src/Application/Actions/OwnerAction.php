<?php
namespace App\Application\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

//TODO: Implement, currently there is no difference between Owner and Admin
//An action that can be only be performed by the owner of a house
abstract class OwnerAction extends AdminAction
{
    abstract protected function action(): Response;
}