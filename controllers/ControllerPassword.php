<?php 
class ControllerPassword
{   
    function __construct()
    {
        include_once ROOT . "/models/ModelPassword.php";
        $this->modPass = new ModelPassword();
    }

    //метод загрузки страницы восстановления пароля
    public function passwordPage()
    {
        // if($passPageMsg === 'password/page'){
        //     $passPageMsg = "";
        // }
        include_once ROOT. "/dist/pass/index.html";
    }
    //метод обработки данных от пользователя на получение нового пароля
    public function passwordRequest()
    {
        //запись данных от пользователя в переменную
        $userData = json_decode(file_get_contents("php://input"), true);
        $errorMsg = '';
        $isCorrectData = true;

        if($userData)
        {
            //проверка на валидность почты
            if(!preg_match ("/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/" , $userData['email'])){
                $isCorrectData = false;
                $errorMsg = "Невірний формат пошти";
            }
            //генерация случайной строки и передача данных в модуль для подготовки и отправки письма
            if($isCorrectData)
            {
                $randStr = $this->getRandomStr();
                $prepMail = $this->modPass->preparationEmail($userData['email'], $randStr); 
                echo json_encode($prepMail); 
            }else
            {
                echo json_encode(['status' => false, 'response' => ['message'=> $errorMsg]]);
            }
        }else
        {
            $this->passwordPage("Server received empty data");
            return false;
        }
    }
    //метод проверки строки на смену пароля
    public function passwordConfirm()
    {
        if($_GET["link"]){
            $link = $_GET["link"];
            $time = time();
            //проверка жизнеспособности сроки на смену пароля (ели ей более 3х часов, ссылка не действительна)
            if(($time - substr($link, 0, 10)) < 10800){
                //запуск модуля на генерация нового пароля
                $newPass = $this->modPass->newPasGen($link);
                //отправка ответа пользователю на основании полученного от модуля результата
                $passPageMsg = $newPass['response']['message'];
            }else{
                $passPageMsg = 'Посилання більше не дійсне';
            }
        }else{
            $passPageMsg = 'Недійсне посилання';
            
        }
        $this->passwordPage($passPageMsg);
    }
    //метод получения случайной строки
    private function getRandomStr()
    {
        $str = "";
        for ($i=0;$i<10;$i++)
        {
            $n = rand(48,122);
            if ($n>47 && $n<57 || $n>64 && $n<91 || $n>96 && $n<123)
            {
                $str .= chr($n);
            }else
            {
                $i--;
            }

        }
        return time() . $str;
    }
    //метод обработки данных от пользователя на смену пароля из личного кабинета
    public function passwordChange()
    {
        $userData = json_decode(file_get_contents("php://input"), true);
        $errorMsg = '';
        $isCorrectData = true;

        if($userData)
        {
            if(!preg_match ("/((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15})/" , $userData['old'])){
                $isCorrectData = false;
                $errorMsg = "Невірний формат чинного пароля";
            }
            if(!preg_match ("/((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15})/" , $userData['new'])){
                $isCorrectData = false;
                $errorMsg = "Невірний формат нового пароля";
            }
            if($userData['new'] !== $userData['newRep']){
                $isCorrectData = false;
                $errorMsg = "Паролі не співпадають";
            }

            if($isCorrectData)
            {
                $result = $this->modPass->chengeUserPass($userData); 
                echo json_encode($result); 
            }else
            {
                echo json_encode(['status' => false, 'response' => ['message' => $errorMsg]]);
            }
        }else
        {
            $errorMsg = "Server received empty data\r\n";
            echo json_encode(['status' => false, 'response' =>  ['message' => $errorMsg]]);
            return false;
        }
    }
}