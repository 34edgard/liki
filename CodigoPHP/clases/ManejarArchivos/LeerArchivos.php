<?php

function LoadData($file) {
  // Read file lines
  $lines = file($file);
  $data = array();
  foreach ($lines as $line)
  $data[] = explode(';', trim($line));
  return $data;
}