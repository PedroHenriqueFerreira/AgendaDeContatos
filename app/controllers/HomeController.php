<?php
class HomeController
{
  public $homeModel;

  function __construct()
  {
    require(__DIR__ . '/../models/HomeModel.php');
    $this->homeModel = new HomeModel();
  }

  function index()
  {
    $result = $this->homeModel->index();
    render('home index', $result);
  }

  function error()
  {
    render('home error');
  }
}
