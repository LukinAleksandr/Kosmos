<?php 
class Validator
{   
  function __construct()
  {
  }

  public static function isEmail($str)
  {
    return preg_match("/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/", $str);
  }
  public static function isPassword($str)
  {
    return preg_match("/((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15})/", $str);
  }
  public static function isRole($str)
  {
    return preg_match("/^(Адміністратор|Користувач)$/", $str);
  }
  public static function isStatus($str)
  {
    return preg_match("/^(1|2|3)$/", $str);
  }
  public static function isName($str)
  {
    return preg_match("/(.+){3,}/", $str);
  }
  public static function isKeyword($str)
  {
    return preg_match("/^(.){0,100}$/", $str);
  }
}