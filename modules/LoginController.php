<?php
/**
 * User Login controller
 * @package com\izylab\reader
 */
class LoginController {
    public function handleRequest($args) {
        $mobi = isset($args['mobi']) ? '&mobi' : '';
        if ( isset($args['logout']) ) {
            UserSession::logout();
            header("Location: /login?msg=4".$mobi);
            return;
        }

        $template = new Template((isset($args['mobi'])?'mobile_':'').'login.php');
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
        $mobi = isset($args['mobi']) ? '&mobi' : '';
        // register
        if ( isset($args['register']) ) {
            if ( $_POST['password'] !== $_POST['password2'] ) {
                header("Location: /login?msg=1".$mobi);
                return;
            }
            if ( strlen($_POST['username']) > 30 ) {
                header("Location: /login?msg=2".$mobi);
                return;
            }
            $salt = $this->createSalt();
            $hash = hash('sha256', $salt . $_POST['password']);
            UserDao::insert(new User(array(
                'username' => $_POST['username'],
                'password' => $hash,
                'salt' => $salt
            )));
            header("Location: /login?msg=0".$mobi);
            return;
        }

        // login
        $user = UserDao::findByUsername($_POST['username']);
        if( $user === null ) {
            header("Location: /login?msg=3".$mobi);
            return;
        }
        $hash = hash('sha256', $user->salt . $_POST['password']);
        if ( $user->password !== $hash ) {
            header("Location: /login?msg=3".$mobi);
            return;
        }
        UserSession::validate($user->id);
        header("Location: /?ok".$mobi);
        return;
    }
    protected function createSalt() {
        return substr(md5(uniqid(rand(), true)), 0, 3);
    }
}
