<?php 
class ControllerUser
{   
    function __construct()
    {
        include_once ROOT . '/models/ModelUser.php';
        $this->modUser = new ModelUser();
    }
    //проверка наличия ссессионных данных пользователя
    private function sessionVerification()
    {
        if($_SESSION['userKosmos']){
            return true;
        }else{
            header('Location:' . SITE);
            return false;
        } 
    }

    //метод перебора экшенов на изменения
    public function chengeSwitcher(){
        if($this->sessionVerification()){
            $action = $_GET['action'];
            switch ($action){
                case 'profile':
                    $result = $this->profileEditing($_GET['changer'], $_GET['value']);
                    break;
                default:
                    $result = ['status' => false, 'response' => ['message' => 'Не існуючий action']];
            }
            echo json_encode($result);
        }
    }

    //метод перебора єкшенов на удаление
    public function deletionSwitcher(){
        if($this->sessionVerification()){
            $action = $_GET['action'];
            switch ($action){
                case 'card':
                    $result = $this->modUser->cardDeletion($_GET['key']);
                    break;
                default:
                    $result = ['status' => false, 'response' => 'Не існуючий action'];
            }
            echo json_encode($result);
        }
    }

     //метод перебора єкшенов на создание
    public function creationSwitcher(){
        if($this->sessionVerification()){
            $cardData = json_decode(file_get_contents('php://input'), true); 
            switch ($_GET['action']){
                case 'card':
                    $result = $this->creatCard($cardData);
                    break;
                default:
                    $result = ['status' => false, 'response' => 'Не існуючий action'];
            }
            echo json_encode($result);
        }
    }

    public function detailedPostInfo(){
        if($this->sessionVerification()){
            $id = $_GET['id'];
            $result = $this->modUser->detailedPostInfo($id);
            echo json_encode($result);
        }
    }

    //метод создания записи в личном кабинете пользоваетля
    private function creatCard($cardData)
    {
        if(empty($cardData['category']) || empty($cardData['subcategory']) || empty($cardData['note']))
        {
            return ['status' => false, 'response' => ['message' => 'Недостатньо даних для створення карти!']];
        }else
        {   //запуск модели для занесения записи в базу данных, и получение списка всех записей пользователя
            $result = $this->modUser->actionCreatCard($cardData);
            return $result;
        }
    }
    

    //запуск нужной функции на изменение профиля пользователя
    private function profileEditing($changer, $value){
        switch ($changer){
            case 'user_position':
                $editResult = $this->modUser->actionEditPosition($value);
                break;
            case 'user_name':
                $editResult = $this->modUser->actionEditName($value);
                break;
            case 'user_phone':
                $editResult = $this->modUser->actionEditPhone($value);
                break;
            default:
                $editResult = ['status' => false, 'response' => ['message' => 'Не існуючий action']];
        }
        return $editResult;
    }
}
				

