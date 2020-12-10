<?php
class ModelLoader
{

    function __construct()
    {
    }

    //метод получение списка подкатегорий Импортозамещения из БД
    public function getSubcatImport()
    {
        $stmt = DB::run("SELECT * FROM sa_category WHERE category= 'Імпортозаміщення'");
        $importCatId = $stmt->fetchAll();

        $id = $importCatId[0]['category_id'];
        $stmt = DB::run("SELECT * FROM sa_sub_category WHERE category_id= ?", [$id]);
        $importSubcatList = $stmt->fetchAll();
        if(!empty($importSubcatList)){
            return ['status' => true, 'response' => ['message' => 'Ok', 'subcategories' => $importSubcatList]];
        }else{
            return ['status' => false, 'response' => ['message' => 'Error! No subcategory list received', 'subcategories' => null]];
        }
    }
    
    //метод получения данных для кабинета администратора
    public function getDataAdmin()
    {    
        $userList = StaticStatement::getAllUsers();
        $companyList = StaticStatement::getAllCompany();
        $forCheckingPost = StaticStatement::getAllPostForVerification();
        return ['status' => true, 'response' => ['message' => 'Successful loading', 'users' => $userList, 'companies' => $companyList , 'checking_posts' => $forCheckingPost]];
    }

    //метод получения данных для кабинета пользователя
    public function getDataUser()
    {
        $profile = StaticStatement::getUserByEmail($_SESSION['userKosmos']['user_email']);
        if(empty($profile)){
            return ['status' => false, 'response' => ['message'=> 'Помилка денних сесії']];
        }
        $profile = $profile[0];
        unset($profile['user_hash_password']);
        $categoryList = StaticStatement::getAllCategory();
        $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                FROM sa_sub_category AS ssc 
                                LEFT JOIN sa_category AS sc 
                                ON sc.category_id = ssc.category_id");
        $subcategoryList = $stmt->fetchAll();
        $companes = DB::run("SELECT company_id, company_name FROM sa_company");
        $companyList = $companes->fetchAll();
        $myPosts = StaticStatement::getPost($_SESSION['userKosmos']['user_id']);
        return ['status' => true, 'response' => ['message' => 'Successful loading', 'profile' => $profile, 'categories' => $categoryList, 'subcategories' => $subcategoryList, 'companies' => $companyList, 'posts' => $myPosts]];
    }

}				