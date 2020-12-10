<?php
class ModelAuthorization
{

    function __construct()
    {
    }

    //метод авторизации пользователя в ПК
    public function userAuth($userData)
    {
        //сохранения логина и пароля в переменных
        $userEmail = $userData["email"];
        $userPassword = $userData["password"];

        //получение пользователя из БД по email
        $user = StaticStatement::getUserByEmail($userEmail);

        if(!$user){
            //возврат ошики если пользователя с таким email не существует
            return ['status' => false, 'response' => ['message' => 'Такого користувача не існує']];
        }

        //сравнение хешей переданного и действующего паролей пользователя
        $passVerify = password_verify($userPassword, $user[0]['user_hash_password']);
        
        if($passVerify)
        {   //если пароли совпадают создаем сессию для пользователя
            $_SESSION['userKosmos'] = [
                'user_id' => $user[0]['user_id'],
                'user_email' => $user[0]['user_email'],
                'user_name' => $user[0]['user_name'],
                'user_phone' => $user[0]['user_phone'],
                'user_company' => $user[0]['company_name'],
                'company_id' => $user[0]['company_id'],
                'company_address' => $user[0]['company_address'],
                'user_position' => $user[0]['user_position'],
                'user_role' => $user[0]['user_role'],
                'user_status' => $user[0]['user_status'],
            ];
            //возврат данных об успешной авторизации пользователя
            return ['status' => true, 'response' => ['message' => 'Successful authorization', 'session' => $_SESSION['userKosmos']]];
        }else{
            //возврат ошибки
            return ['status' => false, 'response' => ['message' => 'Невірний пароль']];
        }
    }
}				