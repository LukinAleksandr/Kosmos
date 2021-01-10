<?php 
class ControllerSearch
{   
    function __construct()
    {
        include_once ROOT . "/models/ModelSearch.php";
        $this->modSearch = new ModelSearch();
    }

    //метод поиска по базе импортозамещения
    public function actionSearchImport()
    {
        //запись переданных критериев поиска в переменную
        $userData = json_decode(file_get_contents("php://input"), true); 
        $errorMsg = '';
        $isCorrectData = true;
        if($userData)
        {
            //проверка на длинну ключевых слов
            if(!Validator::isKeyword($userData['keyword'])){
                $isCorrectData = false;
                $errorMsg .= "Максимальна довжина ключових слів 100 символів\r\n";
            }
            if($isCorrectData)
            {
                if($userData['keyword'] === '')
                {//запрос на поиск по базе в случае отсутствия ключевых слов
                    $result = $this->modSearch->searchImportAllPost($userData["subcategory"]);
                    echo json_encode($result);
                }else 
                {//запрос на поиск по базе по ключевым словам
                    $result = $this->modSearch->keywordSearchImportPost($userData);
                    echo json_encode($result);
                }
                
            }else
            {   
                //Возврат ошибки
                echo json_encode(['status' => false, 'response' => $errorMsg]);
                return false;
            }
        }else
        {
            //переадресация на главную страницу в случае получения неполных данных от пользователя
            header('Location:'. SITE);
            return false;
        }
    }
    //метод поиска по всей базе в личном кабинеете пользователей
    public function actionSearch()
    {
        //запись переданных критериев поиска в переменную
        $userData = json_decode(file_get_contents("php://input"), true); 
        $errorMsg = '';
        $isCorrectData = true;
        if($userData)
        {
            //проверка на длинну ключевых слов
            if(!Validator::isKeyword($userData['keyword'])){
                $isCorrectData = false;
                $errorMsg .= "Максимальна довжина ключових слів 100 символів\r\n";
            }
            if($isCorrectData)
            {
                if($userData['keyword'] === '')
                {//запрос на поиск по базе в случае отсутствия ключевых слов
                    $result = $this->modSearch->searchAllPost($userData);
                    echo json_encode($result);
                }else 
                {//запрос на поиск по базе по ключевым словам
                    $result = $this->modSearch->keywordSearchPost($userData);
                    echo json_encode($result);
                }
                
            }else
            {
                 //Возврат ошибки
                echo json_encode(['status' => false, 'response' => $errorMsg]);
                return false;
            }
        }else
        {
            //переадресация на главную страницу в случае получения неполных данных от пользователя
            header('Location:'. SITE);
            return false;
        }
    }   
}