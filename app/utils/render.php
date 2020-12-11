<?php

function render($page, $databaseResults = '')
{
  $results = $databaseResults;
  require(__DIR__ . '/../views/' . ucfirst(explode(' ', $page)[0]) . '/' . ucfirst(explode(' ', $page)[1]) . '.phtml');
}
