<?php 
class ControllerAdmin
{   
    function __construct()
    {
        include_once ROOT . '/models/ModelAdmin.php';
        $this->modAdmin = new ModelAdmin();
    }
    //проверка наличия ссессионных данных пользователя
    private function adminVerification()
    {
        if(empty($_SESSION['userKosmos']) || $_SESSION['userKosmos']['user_role'] !== 'Адміністратор'){
            return false;
        }
        return true;
    }
    //метод перебора экшенов на создание
    public function creationSwitcher()
    {
        if($this->adminVerification()){
            $data = json_decode(file_get_contents('php://input'), true); 
            switch ($data['action']){
                case 'user':
                    $result = $this->userСreator($data);
                    break;
                case 'company':
                    $result = $this->companyCreator($data);
                    break;
                case 'category':
                    $result = $this->categoryCreator($data);
                    break;
                default:
                    $result = ['status' => false, 'response' => ['message' => 'Не існуючий action']];
            }
            echo json_encode($result);
        }else{
            echo json_encode(['status' => false, 'response' => ['message' => 'You are not an administrator']]);
        }
    }
     //метод перебора экшенов на изменение
    public function chengeSwitcher()
    {
        if($this->adminVerification()){
            $data = json_decode(file_get_contents('php://input'), true); 
            switch ($data['action']){
                case 'user':
                    $result = $this->userСhanger($data);
                    break;
                case 'company':
                    $result = $this->companyСhanger($data);
                    break;
                case 'category':
                    $result = $this->categoryСhanger($data);
                    break;
                case 'subcategory':
                    $result = $this->subcategoryСhanger($data);
                    break;
                case 'post':
                    $result = $this->modAdmin->postConfirm($data);
                    break;   
                default:
                    $result = ['status' => false, 'response' => ['message' => 'Не існуючий action']];
            }
            echo json_encode($result);
        }else{
            echo json_encode(['status' => false, 'response' => ['message' => 'You are not an administrator']]);
        }
    }
    //метод перебора экшенов на удаление
    public function deletionSwitcher()
    {
        if($this->adminVerification()){
            $data = json_decode(file_get_contents('php://input'), true); 
            switch ($data['action']){
                case 'category':
                    $result = $this->modAdmin->categoryDeletion($data);
                    break;
                case 'subcategory':
                    $result = $this->modAdmin->subcategoryDeletion($data);
                    break;
                case 'post':
                    $result = $this->modAdmin->postDeletion($data);
                    break;
                default:
                    $result = ['status' => false, 'response' => ['message' => 'Не існуючий action']];
            }
            echo json_encode($result);
        }else{
            echo json_encode(['status' => false, 'response' => ['message' => 'You are not an administrator']]);
        }
    }

    private function userСreator($data)
    {
        $isValid = true;
        $errorMsg = "";
        //проверка формата почты пользователя
        if(!Validator::isEmail($data['email'])){
            $isValid = false;
            $errorMsg .= "Невірний формат пошти користувача\n";
        }
        //проверка формата роли для пользователя
        if(!Validator::isRole($data['role'])){
            $isValid = false;
            $errorMsg .= "Невірний формат ролі користувача\n";
        }
        //проверка формата статуса пользователя
        if(!Validator::isStatus($data['status'])){
            $isValid = false;
            $errorMsg .= "Невірний формат статуса користувача\n";
        }

        if($isValid){
            $creatResult = $this->modAdmin->creatUser($data);
            return $creatResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }
    private function companyCreator($data)
    {
        $isValid = true;
        $errorMsg = "";
        //проверка формата почты пользователя
        if(!Validator::isEmail($data['email'])){
            $isValid = false;
            $errorMsg .= "Невірний формат пошти для нового підприємства\n";
        }

        if($isValid){
            $creatResult = $this->modAdmin->creatCompany($data);
            return $creatResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }
    private function categoryCreator($data)
    {
        $isValid = true;
        $errorMsg = "";
        //проверка формата почты пользователя
        if(!Validator::isName($data['categoryName']))
        {
            $isValid = false;
            $errorMsg .= "Невірний формат категорії!\n";
        }
        $data['subcategories'] = array_unique($data['subcategories']);

        foreach($data['subcategories'] as $i)
        {
            if(!Validator::isName($i))
            {
                $isValid = false;
                $errorMsg .= "Невірний формат підкатегорії\n";
                break;
            }
        }

        if($isValid)
        {
            $creatResult = $this->modAdmin->creatCategory($data);
            return $creatResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }  
    private function companyСhanger($data)
    {
        $isValid = true;
        $errorMsg = "";
        //проверка формата почты пользователя
        if(!Validator::isEmail($data['email'])){
            $isValid = false;
            $errorMsg .= "Невірний формат пошти підприємства\n";
        }
        if($isValid){
            $creatResult = $this->modAdmin->chengeCompany($data);
            return $creatResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }
    private function userСhanger($data)
    {
        $isValid = true;
        $errorMsg = "";
        //проверка формата почты пользователя
        if(!Validator::isEmail($data['email'])){
            $isValid = false;
            $errorMsg .= "Невірний формат пошти для нового користувача\n";
        }
        //проверка формата роли для пользователя
        if(!Validator::isRole($data['role'])){
            $isValid = false;
            $errorMsg .= "Невірний формат ролі для нового користувача\n";
        }
        //проверка формата статуса пользователя
        if(!Validator::isStatus($data['status'])){
            $isValid = false;
            $errorMsg .= "Невірний формат статуса для нового користувача\n";
        }

        if($isValid){
            $creatResult = $this->modAdmin->chengeUser($data);
            return $creatResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }
    private function categoryСhanger($data)
    {
        $isValid = true;
        $errorMsg = "";
        if(!Validator::isName($data['newName']))
        {
            $isValid = false;
            $errorMsg = "Невірний формат категорії";
        }

        if($isValid){
            $chengeResult = $this->modAdmin->chengeCategory($data);
            return $chengeResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }
    private function subcategoryСhanger($data)
    {
        $isValid = true;
        $errorMsg = "";
        if(!Validator::isName($data['newName']))
        {
            $isValid = false;
            $errorMsg = "Невірний формат підкатегорії";
        }

        if($isValid){
            $chengeResult = $this->modAdmin->chengeSubcategory($data);
            return $chengeResult;
        }else
        {
            return ['status' => false, 'response' => ['message' => $errorMsg]];
        }
    }
}
				

