<?php
$myRoutes = [
  '*' => ['home', 'error'],
  '/' => ['home', 'index'],
  '/login' => ['user', 'login'],
  '/register' => ['user', 'register'],
  '/contact/add' => ['contact', 'add'],
  '/contact/:id/edit' => ['contact', 'edit'],
  '/login/request' => ['user', 'login_request'],
  '/register/request' => ['user', 'register_request'],
  '/logout' => ['user', 'logout'],
  '/contact/:id/delete' => ['contact', 'delete'],
  '/contact/add/request' => ['contact', 'add_edit_request']
];
