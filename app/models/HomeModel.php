<?php
class HomeModel
{

  function index()
  {
    if (isset($_SESSION['id'])) {
      $stmt = Connection::connect()->prepare('SELECT contacts.* FROM contacts INNER JOIN users ON users.id = contacts.user_id WHERE users.id = ?');
      $stmt->bindValue(1, $_SESSION['id']);
      $stmt->execute();
      $contacts_only = $stmt->fetchAll(PDO::FETCH_OBJ);

      $contacts = [];

      foreach ($contacts_only as $key => $contact_only) {
        $stmt2 = Connection::connect()->prepare('SELECT telephones.number FROM telephones INNER JOIN contacts ON telephones.contact_id = contacts.id WHERE contacts.id = ?');
        $stmt2->bindValue(1, $contact_only->id);
        $stmt2->execute();
        $telephones_only = $stmt2->fetchAll(PDO::FETCH_OBJ);
        foreach ($telephones_only as $telephones) {
          $contact_only->telephones[] = $telephones->number;
        }

        if (!$contact_only->address) {
          $contact_only->address = '(Vazio)';
        }

        $contacts[] = $contact_only;
      }

      return $contacts;
    }

    return '';
  }
}
