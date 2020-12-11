<?php
class ContactController
{
  public $contactModel;

  function __construct()
  {
    require(__DIR__ . '/../models/ContactModel.php');
    $this->contactModel = new ContactModel();
  }

  function add()
  {
    if (isset($_SESSION['id'])) {
      render('contact add');
    } else {
      header("location:javascript://history.go(-1)");
    }
  }

  function add_edit_request()
  {
    if (isset($_SESSION['id'])) {
      $result = $this->contactModel->add_edit_request();
      print_r($result);
    } else {
      header("location:javascript://history.go(-1)");
    }
  }

  function edit()
  {
    if (isset($_SESSION['id'])) {
      $result = $this->contactModel->edit();
      render('contact edit', $result);
    } else {
      header("location:javascript://history.go(-1)");
    }
  }

  function delete()
  {
    if (isset($_SESSION['id'])) {
      $result = $this->contactModel->delete();
      print_r($result);
    } else {
      header("location:javascript://history.go(-1)");
    }
  }
}
