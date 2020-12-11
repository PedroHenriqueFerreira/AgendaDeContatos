<?php
class Connection
{
  static function connect()
  {
    try {
      $host = 'localhost';
      $db = 'agenda';
      $user = 'root';
      $password = '';

      return new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $password);
    } catch (Exception $e) {
      return die('Database error');
    }
  }
}
