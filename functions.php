<?php
function dd($value)
  {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
  }
  function isUrl($value)
  {
    return $_SERVER['REQUEST_URI']===$value ; 
  }// Updated at Sun Mar 15 11:47:50 PM EET 2026
// Updated at Sun Mar 15 11:51:36 PM EET 2026
function Authorize($condition,$status=Response::FORBIDDEN)  {
  if(!$condition)
    {
      abort($status);
    }
}