<?php 
class ControllerLoader
{   
    function __construct()
    {
        //подключение модели лоадера
        include_once ROOT . "/models/ModelLoader.php";
        $this->modLoader = new ModelLoader();
    }

    //метод загрузки списка подкатегорий импортозамещения
    public function actionLoadSubcategoryList(){
        $subcat = $this->modLoader->getSubcatImport();
        echo json_encode($subcat);
    }
    
    //метод загрузки личного кабинета
    public function actionUserLoad(){
        if(empty($_SESSION['userKosmos'])){
            echo json_encode(['status' => false, 'response' => ['message'=> 'You are not a user']]);
        }else{
            $userPageData = $this->modLoader->getDataUser();
            echo json_encode($userPageData);
        }
        
    }   

    public function actionAdminLoad(){
        if(empty($_SESSION['userKosmos']) || $_SESSION['userKosmos']['user_role'] !== "Адміністратор"){
            echo json_encode(['status' => false, 'response' => ['message'=> 'You are not an administrator']]);
        }else{
            $adminPageData = $this->modLoader->getDataAdmin();
            echo json_encode($adminPageData);
        }
    }   
}
				

