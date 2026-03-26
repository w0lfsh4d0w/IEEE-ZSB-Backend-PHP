<?php
use core\Response;
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
function abort($code=404) {
    http_response_code($code);
        require base_path("views/response{$code}.php");
        die();
}
function Authorize($condition,$status=Response::FORBIDDEN)  {
  if(!$condition)
    {
      abort($status);
    }
}
function base_path($path)
{
  return BASE_BATH . $path;
}
function view($path,$attributes=[])  {
  extract($attributes);
  require base_path('views/'.$path);
}