<?php

namespace Itb;

use Silex\Application;
use Silex\Provider;
use Symfony\Component\Debug\ErrorHandler;
use Itb\Controllers\ErrorController;
use Itb\Controllers\MainController;
use Itb\Controllers\UserController;
use Itb\Controllers\AdminController;
use Itb\Controllers\LecturerController;
use Itb\Controllers\StudentController;



class WebApplication extends Application
{
    private $myTemplatesPath = __DIR__ . '/../templates';

    public function __construct()
    {
        parent:: __construct();

        // setup Session and Service controller provider
        $this->register(new Provider\SessionServiceProvider());
        $this->register(new Provider\ServiceControllerServiceProvider());

        $this['debug'] = true;
        $this->setupTwig();
        $this->addRoutes();

        $this->handleErrorsAndExceptions();
    }

    public function setupTwig(){
        $this->register(new \Silex\Provider\TwigServiceProvider(),
            [
                'twig.path'=>$this->myTemplatesPath
            ]
        );
    }

    public function addRoutes(){
        //$this->register(new \Silex\Provider\ServiceControllerServiceProvider());

        $this['main.controller'] = function (){return new MainController($this);    };
        $this['user.controller'] = function() { return new UserController($this);   };
        $this['admin.controller'] = function() { return new AdminController($this);   };
        $this['lecturer.controller'] = function() { return new LecturerController($this);   };
        $this['student.controller'] = function() { return new StudentController($this);   };

        $this->get('/', 'main.controller:indexAction');
        $this->get('/list', 'main.controller:listAction');
        $this->get('/show/{id}', 'main.controller:showAction');
        $this->get('/show', 'main.controller:showNoIdAction');
        $this->get('/public', 'main.controller:publicAction');
        $this->get('/about', 'main.controller:aboutAction');
        $this->get('/cartList', 'main.controller:cartListAction');
        $this->get('/cart', 'main.controller:showCartAction');
        $this->get('/cart/{id}', 'main.controller:showCartAction');
        $this->get('/addToCart/{id}', 'main.controller:addToCartAction');
        $this->get('/removeFromCart', 'main.controller:removeFromCartAction');
        $this->get('/emptyCart', 'main.controller:forgetSession');
        $this->get('/refs', 'main.controller:refsAction');
        $this->post('/newRefs', 'main.controller:newRefsAction');
        $this->get('/newRefs', 'main.controller:newRefsAction');
        $this->get('/lecturerBibs', 'main.controller:lecturerBibsAction');
        $this->get('/tags', 'main.controller:tagsAction');
        $this->get('/tags/{id}', 'main.controller:showTagAction');
        $this->post('/tags', 'main.controller:votesAction');

        // ------ login routes GET and POST ------------
        $this->get('/login', 'user.controller:loginAction');
        $this->post('/login', 'user.controller:processLoginAction');

        // ------ logout route GET ------------
        $this->get('/logout', 'user.controller:logoutAction');

        // ------ SECURE PAGES ----------
        $this->get('/admin',  'admin.controller:indexAction');
        $this->get('/admin/codes',  'admin.controller:codesAction');
        $this->get('/lecturer',  'lecturer.controller:indexAction');
        $this->get('/lecturer/codes',  'lecturer.controller:codesAction');
        $this->get('/student',  'student.controller:indexAction');
        $this->get('/student/codes',  'student.controller:codesAction');
    }


    public function handleErrorsAndExceptions()
    {
        ErrorHandler::register();

        //register an error handler
        $this->error(function (\Exception $e) {
            $errorController = new ErrorController($this);
            return $errorController->errorAction($e);
        });
    }
}
