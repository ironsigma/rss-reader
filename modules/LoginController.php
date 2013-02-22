<?php
/**
 * User Login controller
 * @package com\izylab\reader
 */
class LoginController {
    public function handleRequest($args) {
        if ( isset($args['logout']) ) {
            UserSession::logout();
            header("Location: /login?msg=4");
            return;
        }

        $template = new Template('login.php');
        $template->reg = isset($args['register']);

        $template->message = null;
        if( isset($args['msg']) ) {
            switch ( $args['msg'] ) {
            case 0: $template->message = 'Registered successful'; break;
            case 1: $template->message = 'Passwords do not match'; break;
            case 2: $template->message = 'Username too long'; break;
            case 3: $template->message = 'Invalid username or password'; break;
            case 4: $template->message = 'User logged out successfuly'; break;
            }
        }

        $template->display();
    }
    public function handlePostRequest($args) {
        $log = LogFacility::getLogger('LoginController');
        //$log->debug('args'. print_r($args, true));
        //$log->debug('post'. print_r($_POST, true));

        // register
        if ( isset($args['register']) ) {
            if ( $_POST['password'] !== $_POST['password2'] ) {
                header("Location: /login?msg=1");
                return;
            }
            if ( strlen($_POST['username']) > 30 ) {
                header("Location: /login?msg=2");
                return;
            }
            $salt = $this->createSalt();
            $hash = hash('sha256', $salt . $_POST['password']);
            UserDao::insert(new User($_POST['username'], $hash, $salt));
            header("Location: /login?msg=0");
            return;
        }

        // login
        $user = UserDao::findByUsername($_POST['username']);
        if( $user === null ) {
            header("Location: /login?msg=3");
            return;
        }
        $hash = hash('sha256', $user->salt . $_POST['password']);
        if ( $user->password !== $hash ) {
            header("Location: /login?msg=3");
            return;
        }
        UserSession::validate($user->id);
        header("Location: /");
    }
    protected function createSalt() {
        return substr(md5(uniqid(rand(), true)), 0, 3);
    }
}
