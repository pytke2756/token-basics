<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Petrik\Loginapp\User;
use Petrik\Loginapp\Token;
use Slim\Routing\RouteCollectorProxy;

return function(App $app){
    $app->get('/', function (Request $request, Response $response, $args) {
        $response->getBody()->write("Hello world!");
        return $response;
    });

    $app->post('/register', function(Request $request, Response $response, $args){
        $userData = json_decode($request->getBody(), true);
        $user = new User();
        $user->email = $userData['email'];
        $user->password = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->save();
        $response->getBody()->write($user->toJson());
        return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
    });

    $app->post('/login', function(Request $request, Response $response, $args){
        $loginData = json_decode($request->getBody(), true);
        //loginData validáció
        $email = $loginData['email'];
        $password = $loginData['password'];
        $user = User::where('email', $email)->firstOrFail();
        if (!password_verify($password, $user->password)) {
            throw new Exception('Hibás email vagy jelszó');
        }
        $token = new Token();
        $token->user_id = $user->id;
        $token->token = bin2hex(random_bytes(64));
        //check, hogy a token nem létezik a db-ben, pl. lehetne unique stb.
        $token->save();
        $response->getBody()->write(json_encode([
            "email" => $user->email,
            "token" => $token->token,
        ]));
        return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    });
    $app->group("/api", function(RouteCollectorProxy $group){
        $group->get('/hello', function(Request $request, Response $response, $args){
            $response->getBody()->write(json_encode([
                'Hello' => 'Wolrld',
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
        });
    });

};