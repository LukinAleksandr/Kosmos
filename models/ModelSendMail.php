<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class ModelSendMail
{   
    function __construct()
    {
        require_once ROOT . "/vendor/autoload.php";
    }

    private function mailSender($sendler, $recipients, $file, $subject, $body, $altbody = ""){
        $mail = new PHPMailer(true);
        $mail->clearAddresses();  //очистка от старых адресатов
        $mail->clearAttachments(); //очистка от старых файлов
        // $mail->SMTPDebug = 2;         /*Оставляем как есть*/                       
        $mail->isSMTP();              /*Запускаем настройку SMTP*/
        $mail->Host = 'smtp.gmail.com'; /*Выбираем сервер SMTP*/
        $mail->SMTPAuth = true;        /*Активируем авторизацию на своей почте*/
        $mail->Username = 'multaaas';   /*Имя(логин) от аккаунта почты отправителя */
        $mail->Password = 'flkibokxyetdmrpp';        /*Пароль от аккаунта  почты отправителя */
        $mail->SMTPSecure = 'ssl';            /*Указываем протокол*/
        $mail->Port = 465;			/*Указываем порт*/
        $mail->CharSet = 'UTF-8';/*Выставляем кодировку*/
        $mail->setFrom($sendler['sendler-email'], $sendler['sendler-name']);/*Указываем адрес почты отправителя */
        /*Указываем перечень адресов почты куда отсылаем сообщение*/
        foreach($recipients as $recipient)
        {
            $mail->addAddress($recipient['recipient-email'], $recipient['recipient-name']);  
        } 

        /*Указываем вложения*/
        if (!empty($file['name'][0])){
            for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
                $uploadfile = tempnam(ROOT . '/feedback/' , sha1($file['name'][$ct]));
                $filename = $file['name'][$ct];
                if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                    $mail->addAttachment($uploadfile, $filename);
                    $rfile[] = "Файл $filename прикреплён";
                } else {
                    $rfile[] = "Не удалось прикрепить файл $filename";
                }
            }   
        }
    
        $mail->isHTML(true);      /*формируем html сообщение*/
        $mail->Subject = $subject; /*Заголовок сообщения*/
        $mail->Body = $body;  /* Текст сообщения */
        $mail->AltBody = $altbody;/*Описание сообщения */
        $result = $mail->send();
        return $result;
    }

    //письмо с подробной информацией о записи
    public function sendDetailedPostInfo($post)
    {
        $subject = 'Детальна інформація запису';
        $body = "<b>Підприємство:</b> {$post['company_name']}<br>
                <b>Електронна пошта підприємства:</b> {$post['company_email']}<br>
                <b>Сторінка підприємства:</b> {$post['company_site']}<br>
                <b>Адреса підприємства:</b> {$post['company_address']}<br>
                <b>Телефон пфдприємтва:</b> {$post['company_phone']}<br>
                <b>Контактна особа:</b> {$post['user_name']}<br>
                <b>Телефон для контакта:</b> {$post['user_phone']}<br>
                <b>Посада контактної особи:</b> {$post['user_position']}<br>
                <b>Електронна пошта контактної особи:</b> {$post['user_email']}<br>
                <b>Категорія запису:</b> {$post['category']}<br>
                <b>Підкатегорія запису:</b> {$post['sub_category']}<br>
                <b>Текст запису:</b> {$post['note']}<br>";
        $altbody = 'Прохання та пропозиції користувача';
        $dispatch = $this->mailSender(
            [
                'sendler-email' => 'multaaas@gmail.com', 
                'sendler-name' => 'Ассоциация Космос'
            ], 
            [
                [
                    'recipient-email'=> $_SESSION['userKosmos']['user_email'],
                    'recipient-name'=> $_SESSION['userKosmos']['user_name']
                ]
            ], 
            [], 
            $subject, 
            $body, 
            $altbody);
        if($dispatch)
        {
            return ['status' => true, 'response' => ['message' => 'Відправлено']];
        }else
        {
            return ['status' => false, 'response' => ['message' => 'Помилка, лист не надіслано']];
        }
    }

    // public function sendMailFeedback($sendlerName, $sendlerMail, $text, $files){
    //     $subject = 'Прохання та пропозиції користувача';
    //     $body = $text;
    //     $altbody = 'Прохання та пропозиції користувача';
    //     $dispatch = $this->mailSender(
    //         [
    //             'sendler-email' => $sendlerMail, 
    //             'sendler-name' => $sendlerName
    //         ], 
    //         [
    //             [
    //                 'recipient-email'=> 'multaaas@gmail.com',
    //                 'recipient-name'=> 'Ассоциация Космос'
    //             ]
    //         ], 
    //         $files, 
    //         $subject, 
    //         $body, 
    //         $altbody);
    //     if($dispatch)
    //     {
    //         return ['status' => true, 'response' => ['message' => 'Відправлено']];
    //     }else
    //     {
    //         return ['status' => false, 'response' => ['message' => 'Помилка, лист не надіслано']];
    //     }
    // }

    //метод отправки письма с ссылкой для подтверждения смены пароля
    public function sendMailConfirm($user, $link){
        if($user && $link)
        {
            $subject = 'Підтвердження на зміну пароля';
            $body = "Ми чули, що ви втратили пароль від бази даних Асоціації \"Космос\". Але не хвилюйся! <br> Для скидання пароля ви можете скористатися наступним посиланням: <b>{$link}</b><br> Якщо ви не скористаєтесь цим посиланням протягом 3 годин, воно закінчиться. <br><br>Щоб отримати нове посилання для скидання пароля, відвідайте https://kosmos.filearchive.website/password/page/ . <br> З повагою, Команда Асоціації \"Космос\".";
            $altbody = 'Підтвердження на зміну пароля';
            $dispatch = $this->mailSender(
                [
                    'sendler-email' => 'multaaas@gmail.com', 
                    'sendler-name' => 'Ассоциация Космос'
                ],
                [
                    [
                        'recipient-email'=> $user[0]['user_email'],
                        'recipient-name'=> $user[0]['user_name']
                    ]
                ],
                [

                ], 
                $subject, 
                $body, 
                $altbody
            );
            if($dispatch)
            {
                return ['status' => true, 'response' => ['message' => 'На вашу електронну адресу було відправлено лист з підтвердженням про зміну пароля']];
            }else
            {
                return ['status' => false, 'response' => ['message' => 'Помилка, лист не надіслано']];
            }
        }else
        {
            return ['status' => false, 'response' => ['message' => 'Відсутні необхідні данні для відправки листа']];
        }
    }

    //метод отправки письма с новым паролем
    public function sendNewPass($user, $newpass){
        if($user && $newpass)
        {
            $subject = 'Новий пароль';
            $body = "Ваш новий пароль від особистого кабінету:<br> <h1>{$newpass}</h1><br> З повагою команда Асоціації \"Космос\".";
            $altbody = 'Новий пароль';
            $dispatch = $this->mailSender(
                [
                    'sendler-email'=> 'multaaas@gmail.com',
                    'sendler-name'=> 'Ассоциация Космос'
                ], 
                [
                    [
                        'recipient-email'=> $user['user_email'],
                        'recipient-name'=> $user['user_name']
                    ]
                ], 
                [

                ],
                $subject, $body, $altbody);
            if($dispatch)
            {
                return ['status' => true, 'response' => ['message' => 'На вашу електронну адресу було відправлено лист з новим паролем']];
            }else
            {
                return ['status' => false, 'response' => ['message' => 'Помилка, лист не надіслано']];
            }
        }else
        {
            return ['status' => false, 'response' => ['message' => 'Відсутні необхідні данні для відправки листа']];
        }
    }

    //метод отправки письма новому пользователю
    public function emailToNewUser($email, $name, $pass)
    {
        $subject = 'Особистий кабінет від A-kosmos Database';
        $body = "Шановний {$name} на вашу поштову адресу був створений профіль в базі даних Асоціації \"Космос\". <br> Для авторизації в базі даних перейдіть за посиланням: <a href='https://kosmos.filearchive.website'>https://kosmos.filearchive.website</a>. <br>В якості логіна використовуйте вашу поштову адресу <h3>{$email}</h3> та пароль: <h3>{$pass}</h3>";
        $altbody = 'Особистий кабінет від A-kosmos Database';
        $dispatch = $this->mailSender(
                [
                    'sendler-email' => 'multaaas@gmail.com', 
                    'sendler-name' => 'Ассоциация Космос'
                ],
                [
                    [
                        'recipient-email'=> $email, 
                        'recipient-name'=> $name
                    ],
                ],
                [

                ], 
                $subject, 
                $body, 
                $altbody
            );
        if($dispatch)
        {
            return ['status' => true, 'response' => ['message' => 'Новому користувачу надіслан лист з необхідною інформацією']];
        }else
        {
            return ['status' => false, 'response' => ['message' => 'Помилка, лист не надіслано']];
        }
    }
}