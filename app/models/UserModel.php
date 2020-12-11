<?php
class UserModel
{

  function login_request()
  {
    $_POST['email'] = $_POST['email'] ? $_POST['email'] : '';
    $_POST['password'] = $_POST['password'] ? $_POST['password'] : '';
    if (!$_POST['email'] || !$_POST['password']) {
      $errors = 'Dados incompletos';
      return $errors;
    }

    if (!strrpos($_POST['email'], '@') || !strrpos($_POST['email'], '.com')) {
      $errors = 'Email inválido';
      return $errors;
    }

    if (strlen($_POST['password']) < 5 || strlen($_POST['password']) > 32) {
      $errors = 'A senha precisa ter entre 3 e 32 caracteres';
      return $errors;
    }

    $stmt = Connection::connect()->prepare('SELECT * FROM users WHERE email = ? AND password = ?');

    $stmt->bindValue(1, $_POST['email']);
    $stmt->bindValue(2, md5($_POST['password']));

    $stmt->execute();

    $results = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$results) {
      $errors = 'Usuário não encontrado';
      return $errors;
    } else {
      $_SESSION['id'] = $results->id;
      $_SESSION['name'] = $results->name;
      $success = '200';
      return $success;
    }
  }

  function register_request()
  {
    $_POST['name'] = $_POST['name'] ? $_POST['name'] : '';
    $_POST['email'] = $_POST['email'] ? $_POST['email'] : '';
    $_POST['password'] = $_POST['password'] ? $_POST['password'] : '';
    $_POST['confirm_password'] = $_POST['confirm_password'] ? $_POST['confirm_password'] : '';

    if (!$_POST['email'] || !$_POST['password'] || !$_POST['name'] || !$_POST['confirm_password']) {
      $errors = 'Dados incompletos';
      return $errors;
    }

    if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 50) {
      $errors = 'O nome precisa ter entre 3 e 50 caracteres';
      return $errors;
    }

    if (!strrpos($_POST['email'], '@') || !strrpos($_POST['email'], '.com')) {
      $errors = 'Email inválido';
      return $errors;
    }

    if (strlen($_POST['password']) < 5 || strlen($_POST['password']) > 32) {
      $errors = 'A senha precisa ter entre 3 e 32 caracteres';
      return $errors;
    }

    if ($_POST['confirm_password'] !== $_POST['password']) {
      $errors = 'As senhas não conferem';
      return $errors;
    }

    $stmt = Connection::connect()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bindValue(1, $_POST['email']);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if ($result) {
      $errors = 'O email já está em uso';
      return $errors;
    }

    $stmt2 = Connection::connect()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');

    $stmt2->bindValue(1, $_POST['name']);
    $stmt2->bindValue(2, $_POST['email']);
    $stmt2->bindValue(3, md5($_POST['password']));

    $stmt2->execute();

    $success = '200';
    return $success;
  }
}
