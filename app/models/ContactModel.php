<?php
class ContactModel
{
  function edit()
  {
    $_POST['id'] = $_POST['id'] ? $_POST['id'] : '';

    $stmt = Connection::connect()->prepare('SELECT contacts.* FROM contacts INNER JOIN users ON users.id = contacts.user_id WHERE contacts.id = ? AND users.id = ?');
    $stmt->bindValue(1, $_POST['id']);
    $stmt->bindValue(2, $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    $stmt2 = Connection::connect()->prepare('SELECT telephones.number FROM telephones INNER JOIN contacts ON telephones.contact_id = contacts.id WHERE contacts.id = ?');
    $stmt2->bindValue(1, $_POST['id']);
    $stmt2->execute();
    $telephones_only = $stmt2->fetchAll(PDO::FETCH_OBJ);

    foreach ($telephones_only as $telephones) {
      $result->telephones[] = $telephones->number;
    }

    if (!$result) {
      header("location:javascript://history.go(-1)");
      $errors = 'Esse contato não existe';
      return $errors;
    }

    return $result;
  }

  function delete()
  {
    $_POST['id'] = $_POST['id'] ? $_POST['id'] : '';

    $contacts = Connection::connect()->prepare('SELECT * FROM contacts WHERE id = ? AND user_id = ?');
    $contacts->bindValue(1, $_POST['id']);
    $contacts->bindValue(2, $_SESSION['id']);
    $contacts->execute();

    $results = $contacts->fetch(PDO::FETCH_OBJ);

    if (!$results) {
      $errors = 'Requisição inválida!';
      return $errors;
    }
    if ($results->photo) {
      unlink(__DIR__ . '/../uploads/' . $results->photo);
    }

    $stmt2 = Connection::connect()->prepare('DELETE FROM telephones WHERE contact_id = ?');
    $stmt2->bindValue(1, $_POST['id']);
    $stmt2->execute();

    $stmt3 = Connection::connect()->prepare('DELETE FROM contacts WHERE id = ? AND user_id = ?');

    $stmt3->bindValue(1, $_POST['id']);
    $stmt3->bindValue(2, $_SESSION['id']);
    $stmt3->execute();

    $success = '200';
    return $success;
  }

  function add_edit_request()
  {
    $_POST['name'] = $_POST['name'] ? $_POST['name'] : '';
    $_POST['email'] = $_POST['email'] ? $_POST['email'] : '';
    $_POST['address'] = $_POST['address'] ? $_POST['address'] : '';
    $_POST['is_new'] = $_POST['is_new'] ? $_POST['is_new'] : '';
    $_POST['id'] = $_POST['id'] ? $_POST['id'] : '';
    $filename = '';
    $numbers = [];

    foreach ($_POST as $key => $value) {
      if (filter_var($key, FILTER_SANITIZE_NUMBER_INT)) {
        if (strlen($value) !== 19 || !strrpos($value, '-') || !strrpos($value, '(') || !strrpos($value, ')')) {
          $errors = 'Número inválido';
          return $errors;
        }

        $numbers[] = $value;
      }
    }

    $repeted_nums = array_unique( array_diff_assoc( $numbers, array_unique( $numbers ) ) );

    if($repeted_nums) {
      $errors = 'Cada número tem que ser único';
      return $errors;
    }

    if($this->findRepetedNums($numbers)) {
      $errors = 'Cada número tem que ser único';
      return $errors;
    }

    if (!$_POST['email'] || !$_POST['name']) {
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

    if (!isset($_POST['photo'])) {
      $filename;
      $extension = explode('.', $_FILES['photo']['name'])[sizeof(explode('.', $_FILES['photo']['name'])) - 1];

      $stmt = Connection::connect()->prepare('SELECT photo FROM contacts WHERE id = ? AND user_id = ?');
      $stmt->bindValue(1, $_POST['id']);
      $stmt->bindValue(2, $_SESSION['id']);
      $stmt->execute();
      $filename = $stmt->fetch(PDO::FETCH_OBJ);

      if (!$filename || !isset($filename->photo) || $filename->photo == '') {
        $filename = date("Ymdhis") . rand(10000, 99999) . '.' . $extension;
      } else {
        unlink(__DIR__ . '/../uploads/' . $filename->photo);
        $filename = date("Ymdhis") . rand(10000, 99999) . '.' . $extension;
      }

      $stmt2 = Connection::connect()->prepare('UPDATE contacts SET photo = ? WHERE id = ? AND user_id = ?');
      $stmt2->bindValue(1, $filename);
      $stmt2->bindValue(2, $_POST['id']);
      $stmt2->bindValue(3, $_SESSION['id']);
      $stmt2->execute();
      
      move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../uploads/' . $filename);
    }

    $stm3 = Connection::connect()->prepare('SELECT * FROM contacts WHERE (name = ? OR email = ?) AND user_id = ?');
    $stm3->bindValue(1, $_POST['name']);
    $stm3->bindValue(2, $_POST['email']);
    $stm3->bindValue(3, $_SESSION['id']);
    $stm3->execute();
    $contacts = $stm3->fetch(PDO::FETCH_OBJ);

    if ($_POST['is_new'] == 'true') {
      if ($contacts) {
        $errors = 'Esse contato já existe';
        return $errors;
      }
      $stmt4 = Connection::connect()->prepare('INSERT INTO contacts (name, email, address, photo, user_id) VALUES (?, ?, ?, ?, ?)');
    } else {
      if (!$contacts) {
        $errors = 'Esse contato ainda não existe';
        return $errors;
      }
      $stmt4 = Connection::connect()->prepare('UPDATE contacts SET name = ?, email = ?, address = ? WHERE id = ? AND user_id = ?');
    }

    $stmt4->bindValue(1, $_POST['name']);
    $stmt4->bindValue(2, $_POST['email']);
    $stmt4->bindValue(3, $_POST['address']);

    if ($_POST['is_new'] == 'true') {
      $stmt4->bindValue(4, $filename);
    } else {
      $stmt4->bindValue(4, $_POST['id']);
    }
    $stmt4->bindValue(5, $_SESSION['id']);

    $stmt4->execute();

    if ($_POST['is_new'] == 'false') {
      $id = $_POST['id'];
      $stmt5 = Connection::connect()->prepare('DELETE FROM telephones WHERE contact_id = ?');
      $stmt5->bindValue(1, $id);
      $stmt5->execute();
    } else {
      $stmt5 = Connection::connect()->prepare('SELECT id FROM contacts ORDER BY id DESC LIMIT 1');

      $stmt5->execute();

      $id = $stmt5->fetch(PDO::FETCH_OBJ)->id;
    }

    foreach ($numbers as $number) {
      $stmt6 = Connection::connect()->prepare('INSERT INTO telephones (number, contact_id) VALUES (?, ?)');

      $stmt6->bindValue(1, $number);
      $stmt6->bindValue(2, $id);
      $stmt6->execute();
    }


    $success = '200';
    return $success;
  }

  function findRepetedNums($numbers) {
    $findAllContacts = Connection::connect()->prepare('SELECT * FROM contacts WHERE user_id = ?');
    $findAllContacts->bindValue(1, $_SESSION['id']);
    $findAllContacts->execute();
    $allContacts = $findAllContacts->fetchAll(PDO::FETCH_OBJ);

    $myNumbers = [];

    foreach($allContacts as $contact) {
      $findAllNumbers = Connection::connect()->prepare('SELECT number FROM telephones WHERE contact_id = ?');
      $findAllNumbers->bindValue(1, $contact->id);
      $findAllNumbers->execute();
      $allNumbers = $findAllNumbers->fetchAll(PDO::FETCH_OBJ);
      foreach($allNumbers as $number) {
         if($_POST['is_new'] == 'false') {
           if($contact->id == $_POST['id']) {
             if(in_array($number->number, $numbers)) {
               $number->number = date("Ymdhis") . rand(10000, 99999);
             }
           }
         }
        $myNumbers[] = $number->number;
      }
    }

    foreach($numbers as $number) {
      $myNumbers[] = $number;
    }

    return array_unique( array_diff_assoc( $myNumbers, array_unique( $myNumbers ) ) );
  }
}
