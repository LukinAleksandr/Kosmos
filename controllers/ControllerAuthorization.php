<?php 
class ControllerAuthorization
{   
    function __construct()
    {
        //подкоючение модели авторизации
        include_once ROOT . "/models/ModelAuthorization.php";
        $this->modAuthorization = new ModelAuthorization();
    }

    //метод авторизации пользователя в ПК
    public function actionUserAuthorization()
    {   
        //получение данных в формате json от пользователя
        $userData = json_decode(file_get_contents("php://input"), true);
        $errorMsg = '';
        $isCorrectData = true;

        if($userData) //проверка наличия данных пользователя
        {
            //проверка формата почты пользователя
            if(!preg_match ("/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/" , $userData['email'])){
                $isCorrectData = false;
                $errorMsg .= "Невірний формат пошти\n";
            }
            //проверка формата пароля пользователя
            if(!preg_match ("/((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15})/" , $userData['password'])){
                $isCorrectData = false;
                $errorMsg .= "Невірний формат паролю\r\n";
            }

            if($isCorrectData)
            {
                //Выпролнение метода авторизации пользователя в системе
                $result = $this->modAuthorization->userAuth($userData);
                echo json_encode($result);
                return true;
            }else
            {   
                //возврат ошибки если данные не валидны
                echo json_encode(['status' => false, 'response' => ['message' => $errorMsg]]);
                return false;
            }
        }else
        {
            //переадресация на главную страницу в случае получения неполных данных от пользователя
            header('Location:'. SITE);
            return false;
        }
    }

    //метод удаления сессии пользователя
    public function actionUserSessionDestroy()
    {
        unset($_SESSION['userKosmos']);
        session_unset();
        session_destroy();
        echo json_encode(['status' => false, 'response' => ['message' => 'Session destroy']]);
        exit;
    }      
}