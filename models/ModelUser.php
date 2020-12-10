<?php 
class ModelUser
{   
    function __construct()
    {
        include_once ROOT . "/models/ModelSendMail.php";
        $this->modMail = new ModelSendMail();
    }
    //метод изменения должности пользователя
    public function actionEditPosition($value)
    {
        $stmt = DB::prepare("UPDATE sa_users SET user_position = ? WHERE `user_email` = ?");
        $stmt->execute([$value, $_SESSION['userKosmos']['user_email']]);
        if($stmt){
            $_SESSION['userKosmos']['user_position'] = $value;
            return ['status' => true, 'response' => ['message' => 'Посада змінена']];
        }else{
            return ['status' => false, 'response' => ['message' => 'Помилка']];
        }
    }
    //метод изменения имени пользователя
    public function actionEditName($value)
    {
        $stmt = DB::prepare("UPDATE sa_users SET user_name = ? WHERE `user_email` = ?");
        $stmt->execute([$value, $_SESSION['userKosmos']['user_email']]);
        if($stmt){
            $_SESSION['userKosmos']['user_name'] = $value;
            return ['status' => true, 'response' => ['message' => 'П.І.П змінено']];
        }else{
            return ['status' => false, 'response' => ['message' => 'Помилка']];
        }
    }
    //метод изменения телефона пользователя
    public function actionEditPhone($value)
    {
        $stmt = DB::prepare("UPDATE sa_users SET user_phone = ? WHERE `user_email` = ?");
        $stmt->execute([$value, $_SESSION['userKosmos']['user_email']]);
        if($stmt){
            $_SESSION['userKosmos']['user_phone'] = $value;
            return ['status' => true, 'response' => ['message' => 'Телефон змінений']];
        }else{
            return ['status' => false, 'response' => ['message' => 'Помилка']];
        }
    }
    //метод создания записей пользователя с последующим возврптом всех имеющихся записей пользователя
    public function actionCreatCard($cardData)
    {
        $stmt = DB::run("SELECT * FROM `sa_category` WHERE `category`= ?", [$cardData['category']]);
        $categories = $stmt->fetchAll();
        $categoryID = 0;
        $subcategoryID = false;
        if(count($categories) != 0)
        {
            $categoryID = $categories[0]['category_id'];
            $subcategories = StaticStatement::getAllSubCategory($categoryID);
            if(count($subcategories) != 0)
            {
                foreach($subcategories as $subcategory)
                {
                    if($subcategory['sub_category'] == $cardData['subcategory']){
                        $subcategoryID = $subcategory['sub_category_id'];
                        break;
                    }
                }

                if($subcategoryID)
                {
                    $stmt = DB::prepare("INSERT INTO sa_posts (category_id, sub_category_id, user_id, company_id, note) VALUES (?, ?, ?, ?, ?)");
                    $add = $stmt->execute([$categoryID, $subcategoryID, $_SESSION['userKosmos']['user_id'], $_SESSION['userKosmos']['company_id'], $cardData['note']]);
    
                    $allPost = StaticStatement::getPost($_SESSION['userKosmos']['user_id']);
                    return ['status' => true, 'response' => ['message' => 'Запис створено', 'posts' => $allPost]];
                }else
                {
                    return ['status' => false, 'response' => ['message' => 'Такої підкатегорії не існує']];
                }
 
            }else{
                return ['status' => false, 'response' =>  ['message' => 'Список підкатегорій порожній']];
            }
        }else{
            return ['status' => false, 'response' =>  ['message' => 'Такої категорії не існує']];
        }

    }
    //метод удаления записи пользователя
    public function cardDeletion($id)
    {
        $stmt = DB::prepare("DELETE FROM `sa_posts` WHERE post_id = ? AND user_id = ?");
        $result = $stmt->execute([$id, $_SESSION['userKosmos']['user_id']]);
        if($result){
            $allPost = StaticStatement::getPost($_SESSION['userKosmos']['user_id']);
            return ['status' => true, 'response' => ['message' => 'Запис видален', 'post_list' => $allPost]];
        }else{
            return ['status' => false, 'response' => ['message' => 'Такого запису не існує']];
        } 
    }

    public function detailedPostInfo($id)
    {
        $stmt = DB::run("SELECT sp.note, sc.category, ssc.sub_category, su.user_email, su.user_name, su.user_position, su.user_phone, sco.company_name, sco.company_site, sco.company_address, sco.company_email, sco.company_phone
                                FROM sa_posts AS sp
                                LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                LEFT JOIN sa_users AS su ON su.user_id = sp.user_id
                                LEFT JOIN sa_company AS sco ON sco.company_id = sp.company_id
                                WHERE sp.post_id = ?", [$id]);
        $post = $stmt->fetchAll();

        $sendResult = $this->modMail->sendDetailedPostInfo($post[0]);
        return $sendResult;
    }
}