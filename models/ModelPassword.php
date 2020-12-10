<?php
class ModelPassword
{
    function __construct()
    {
        include_once ROOT . "/models/ModelSendMail.php";
        $this->modMail = new ModelSendMail();
    }

    //метод подготовки письма о подтверждении смены пароля к отправке
    public function preparationEmail($email, $randStr)
    {
        //получение данных о пользователе из БД по email
        $user = StaticStatement::getUserByEmail($email);
        if($user)
        {
            //сгенерировать линк на подтверждение
            $link = SITE . "/password/confirm?link=" . $randStr;
            
            //удалить старые записи из бд
            $stmt = DB::prepare("DELETE FROM sa_temp WHERE user_email= ?");
            $stmt->execute([$email]);

            //сохранить линк в бд
            $stmt = DB::prepare("INSERT INTO sa_temp (user_email, user_name, rand_str) VALUES (?, ?, ?)");
            $add = $stmt->execute([$user[0]['user_email'], $user[0]['user_name'], $randStr]);
            
            //отправить письмо
            $sendMailResult = $this->modMail->sendMailConfirm($user, $link);

            return $sendMailResult;
        }else
        {
            return ['status' => false, 'response' => ['message'=> 'Користувача з такою поштою не існує']];
        }
    }

    //метод генерации нового пароля для пользователя
    public function newPasGen($link)
    {
        //ищем ссылку пользователя на получение нового пароля в БД
        $stmt = DB::run("SELECT * FROM sa_temp WHERE rand_str = ? AND is_active = '1'", [$link]);
        $result = $stmt->fetchAll();

        if($result)
        {
            //деактивируем ссылку пользователя на получение нового пароля в БД
            $stmt  = DB::prepare("UPDATE sa_temp SET is_active = '0' WHERE `user_email` = ?");
            $stmt->execute([$result[0]['user_email']]);

            //генерируем новый пароль для пользователя
            $randomPass = $this->randomPassword();

            //отправка письма пользователю с новым паролем
            $sendMail = $this->modMail->sendNewPass($result[0], $randomPass);
            if($sendMail['status'])
            {
                //хеширование нового пароля и сохранение его в БД для указанного пользователя
                $hashPass = password_hash($randomPass, PASSWORD_BCRYPT);
                $stmt  = DB::prepare("UPDATE sa_users SET user_hash_password = ? WHERE `user_email` = ?");
                $stmt->execute([$hashPass, $result[0]['user_email']]);
            }
            return $sendMail;
        }else{
            return ['status' => false, 'response' => ['message' => 'Такого посилання не існує']];
        }
    }

    //метод генерации случайного пароля
    public function randomPassword()
    {
        $lowLitter = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','p','r','s','t','u','v','x','y','z'];
        $highLitter = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','P','R','S','T','U','V','X','Y','Z'];
        $num = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
        $randPass = '';

        for($i = 0; $i < 3; $i++)
        {
            $randPass .= $lowLitter[rand(0, count($lowLitter) - 1)];
            $randPass .= $highLitter[rand(0, count($highLitter) - 1)];
            $randPass .= $num[rand(0, count($num) - 1)];
        }
        $randPass = str_split($randPass);
        shuffle($randPass);
        $randPass = implode($randPass);
        return $randPass;
    }

    //метод смены пароля пользователя в личном кабинете
    public function chengeUserPass($data)
    {
        $user = StaticStatement::getUserByEmail($_SESSION['userKosmos']['user_email']);
        if(!$user){
            return ['status' => false, 'response' => ['message' => 'Помилка, спробуйте пізніше']];
        }
        $passVerify = password_verify($data['old'], $user[0]['user_hash_password']);
        if($passVerify)
        {
            $hpass= password_hash($data['new'], PASSWORD_BCRYPT);

            $stmt  = DB::prepare("UPDATE `sa_users` SET `user_hash_password`= ? WHERE `user_email`= ?");
            $stmt->execute([$hpass, $_SESSION['userKosmos']['user_email']]);
            return ['status' => true, 'response' => ['message' => 'Пароль змінений']];
        }else
        {
            return ['status' => false, 'response' => ['message' => 'Чинний пароль невірний']];
        }
    }

}				