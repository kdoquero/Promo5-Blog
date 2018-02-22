<?php

use Slim\Http\Request;
use Slim\Http\Response;
use simplon\entities\User;
use simplon\entities\Article;
use simplon\dao\DaoUser;
use simplon\dao\DaoArticle;

// Routes


$app->get('/', function (Request $request, Response $response, array $args) {
    //On instancie le dao
    $dao = new DaoUser();
    //On récupère les Persons via la méthode getAll
    $users = $dao->getAll();
    
    //On passe les persons à la vue index.twig
    return $this->view->render($response, 'index.twig', [
        'users' => $users
    ]);
})->setName('index');

$app->post('/', function (Request $request, Response $response, array $args) {
    $dao = new DaoUser();
    $form = $request->getParsedBody();
    $user = $dao->getByEmail($form['email']);
   
    $form['isLogged'] = (!empty($user) && $form['email'] === $user->getEmail() && $form['password'] === $user->getPassword());
    if ($form['isLogged']) {
        $redirectUrl = $this->router->pathFor('userblog',[
            'id' => $user->getId(), 'user' =>$_SESSION['user'], 'name' => $user->getUsername()
            ]);
    //On redirige l'utilisateur sur la page d'accueil
        return $response->withRedirect($redirectUrl);
    } else {
        return $this->view->render($response, 'index.twig', [
           
        ]);
    }

    
    
})->setName('index');

$app->get('/userblog/{id}/{name}', function (Request $request, Response $response, array $args) {
    $daoUser = new DaoUser();
    $daoArticle = new DaoArticle();
    $articles = $daoArticle->getUserArticle($args['id']);

    $user = $daoUser->getById($args['id']);
    $args['name'] = $user->getUsername();
    $_SESSION['user'] = $user;
    
    return $this->view->render($response, 'userblog.twig',[ 'id' => $user->getId(),
        'articles' => $articles , "user" => $user ,'name' => $user->getUsername()
    ]);
})->setName('userblog');

$app->get('/viewblog/{id}', function (Request $request, Response $response, array $args) {
    $daoUser = new DaoUser();
    $daoArticle = new DaoArticle();
    $articles = $daoArticle->getUserArticle($args['id']);

    $user = $daoUser->getById($args['id']);
    return $this->view->render($response, 'viewblog.twig',[
        'articles' => $articles , "user" => $user ,'name' => $user->getUsername()
    ]);
})->setName('viewblog');

$app->post('/userblog/{id}/{name}', function (Request $request, Response $response, array $args) {
    $form = $request->getParsedBody();
    $daoUser = new DaoUser();
    $daoArticle = new DaoArticle();
    $articles = $daoArticle->getUserArticle($args['id']);
    
    $user = $daoUser->getById($args['id']);
    $articles = $daoArticle->getUserArticle($user->getId());
    $newArticle = new Article($form['title'],$user->getId(), $form['content'],new \DateTime('now'));
    
    $daoArticle->add($newArticle);
    //$daoUser->update(new User($form['username'],$user->getEmail(),$form['password']));
    $redirectUrl = $this->router->pathFor('userblog',[
        'id' => $user->getId(), 'name' => $user->getUsername()
        ]);
    return $response->withRedirect($redirectUrl);
    
})->setName('userblog');

$app->get('/register', function (Request $request, Response $response, array $args) {

    return $this->view->render($response, 'register.twig');
})->setName('register');

$app->post('/register', function (Request $request, Response $response, array $args) {

    //On récupère les données du formulaire
    $form = $request->getParsedBody();
    //On crée une Person à partir de ces données
    $newUser = new User($form['username'], $form['email'], $form['password']);
    //On instancie le DAO
    $dao = new DaoUser();
    //On utilise la méthode add du DAO en lui donnant la Person qu'on vient de créer
    $dao->add($newUser);
    $redirectUrl = $this->router->pathFor('index');
    //On redirige l'utilisateur sur la page d'accueil
    return $response->withRedirect($redirectUrl);
})->setName('register');

$app->get('/update/{id}/{name}', function (Request $request, Response $response, array $args) {
    $daoUser = new DaoUser();
    $user = $daoUser->getById($args['id']);


    return $this->view->render($response, 'updateuser.twig', [
        'id' => $user->getId(), 'name' => $user->getUsername()
        ]);
})->setName('update');

$app->post('/update/{id}/{name}', function (Request $request, Response $response, array $args) {
    $form = $request->getParsedBody();
    $daoUser = new DaoUser();
    $daoArticle = new DaoArticle();
    $user = $daoUser->getById($args['id']);
    $user->setUsername($form['username']);
    $user->setPassword($form['password']);
    $daoUser->update($user);
    $redirectUrl = $this->router->pathFor('userblog',[
        'id' => $user->getId(), 'user' =>$_SESSION['user'], 'name' => $user->getUsername()
        ]);
    return $response->withRedirect($redirectUrl);
        
})->setName('update');


