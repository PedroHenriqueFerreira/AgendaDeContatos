<?php

class UserController
{
  public $userModel;

  function __construct()
  {
    require(__DIR__ . '/../models/UserModel.php');
    $this->userModel = new UserModel();
  }

  function login()
  {
    if (!isset($_SESSION['id'])) {
      render('user login');
    } else {
      header("location:javascript://history.go(-1)");
    }
  }

  function login_request()
  {
    $result = $this->userModel->login_request();
    print_r($result);
  }

  function register()
  {
    if (!isset($_SESSION['id'])) {
      render('user register');
    } else {
      header("location:javascript://history.go(-1)");
    }
  }

  function register_request()
  {
    $result = $this->userModel->register_request();
    print_r($result);
  }

  function logout()
  {
    session_destroy();
  }
}
