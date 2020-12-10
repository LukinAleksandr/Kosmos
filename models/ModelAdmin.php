<?php 
class ModelAdmin
{   
    function __construct()
    {
    }

    //метод создания компании
    public function creatCompany($data)
    {
        //проверка наличия компании по электронному адресу
        $stmt = DB::run("SELECT * FROM sa_company WHERE company_email= ?", [$data['email']]);
        $comapny = $stmt->fetchAll();

        //возврат ошибки если электронный адрес занят
        if(count($comapny) > 0){
            return ['status' => false, 'response' => ['message' => 'Ця електронна адреса вже зайнята']];
        }

        //создание компании в базе данных
        $stmt = DB::prepare("INSERT INTO sa_company (company_name, company_site, company_address, company_email, company_phone, company_payment) VALUES (?, ?, ?, ?, ?, ?)");
        $add = $stmt->execute([$data['name'], $data['site'], $data['address'], $data['email'], $data['phone'], $data['pay']]);

        //отправка обновленного списка предприятий или сообщение об ошибке
        if($add)
        {
            $allCompanies = StaticStatement::getAllCompany();
            return ['status' => true, 'response' => ['message' => 'Підприємство створено', 'companies' => $allCompanies]];
        }else{
            return ['status' => false, 'response' => ['message' => 'Виникла помилка, підприємство не створено']];
        }
    }

    //изменение анкетных данных компании
    public function chengeCompany($data)
    {
        //проверка наличия компании по электронному адресу
        $stmt = DB::run("SELECT * FROM sa_company WHERE company_email= ?", [$data['email']]);
        $company = $stmt->fetchAll();
        //возврат ошибки в случае отсутствия совпадения
        if(count($company) != 1){
            return ['status' => false, 'response' => ['message' => 'Такого підприємства не існує в базі']];
        }
        //изменение анкетных данных компании
        $stmt = DB::prepare("UPDATE sa_company SET company_name = ?, company_site = ?, company_address = ?, company_phone = ?, company_payment = ? WHERE company_email = ?");
        $stmt->execute([$data['name'], $data['site'], $data['address'], $data['phone'], $data['pay'], $data['email']]);

        //отправка обновленного списка предприятий или сообщение об ошибке
        if($stmt)
        {
            $allCompanies = StaticStatement::getAllCompany();
            return ['status' => true, 'response' => ['message' => 'Підприємство змінено', 'companies' => $allCompanies]];
        }else{
            return ['status' => false, 'response' => ['message' => 'Виникла помилка']];
        }

    }
    //метод создания пользователей в программном комплексе
    public function creatUser($data)
    {
        //проверка занятости электронного адреса пользователя
        $stmt = DB::run("SELECT * FROM `sa_users` WHERE `user_email`= ?", [$data['email']]);
        $user = $stmt->fetchAll();

        //возврат ошибки в случае совпадения
        if(count($user) > 0){
            return ['status' => false, 'response' => ['message' => 'Ця електронна адреса вже зайнята']];
        }
        //получение данных предприятия по названию к на которое заводится новый пользователь
        $stmt = DB::run("SELECT * FROM `sa_company` WHERE `company_name`= ?", [$data['company']]);
        $company = $stmt->fetchAll();

        //возврат ошибки если такой компании нет в базе
        if(count($company) < 1)
        {
            return ['status' => false, 'response' => ['message' => 'Такого підприємства не існує в базі']];
        }else
        {
            //подключение модуля работы с паролями
            include_once ROOT . '/models/ModelPassword.php';
            $modPass = new ModelPassword();

            //создание случайного пароля для нового пользователя
            $randomPass = $modPass->randomPassword();

            //хеширование пароля для нового пользователя
            $hashPass = password_hash($randomPass, PASSWORD_BCRYPT);

            //создание нового пользователя в программном комплексе
            $stmt = DB::prepare("INSERT INTO sa_users (user_email, user_hash_password, user_name, user_phone, company_id, user_position, user_role, user_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $add = $stmt->execute([$data['email'], $hashPass, $data['name'], $data['phone'], $company[0]['company_id'], $data['position'], $data['role'], $data['status']]);

            //в случае успешного создания пользователя - отправка ему письма с приветствием и паролем или возврат ошибки
            if($add)
            {
                include_once ROOT . "/models/ModelSendMail.php";
                $modMail = new ModelSendMail();
                $sending = $modMail->emailToNewUser($data['email'], $data['name'], $randomPass);
                $allUsers = StaticStatement::getAllUsers();
                return ['status' => false, 'response' => ['message' => $sending['response']['message'], 'users' => $allUsers]];
            }else{
                return ['status' => false, 'response' => ['message' => 'Виникла помилка, користувач не створений']];
            }
        }
    }

    //метод создания новой категории
    public function creatCategory($data)
    {
        //проверка наличия такой категории в базе
        $stmt = DB::run("SELECT * FROM sa_category WHERE category= ?", [$data['categoryName']]);
        $category = $stmt->fetchAll();

        //возврпт ошибки в случае совпадения
        if(count($category))
        {
            return ['status' => false, 'response' => ['message' => 'Така категорія вже існує']];
        }
        //создание новой категории
        $stmt = DB::prepare("INSERT INTO sa_category (category) VALUES (?)");
        $add = $stmt->execute([$data['categoryName']]);

        //в случае успешного создания категории создаем подкатегории
        if($add){
            //получаем id новой категории
            $stmt = DB::run("SELECT category_id FROM sa_category WHERE category= ?", [$data['categoryName']]);
            $category = $stmt->fetchAll();

            //добавляем в базу все подкатегории привязывая их к id новой категории
            foreach($data['subcategories'] as $i)
            {
                $stmt = DB::prepare("INSERT INTO sa_sub_category (category_id, sub_category) VALUES (?, ?)");
                $add = $stmt->execute([$category[0]['category_id'], $i]);
            }
            //получение и возпрат обновленного списка категорий
            $categoryList = StaticStatement::getAllCategory();
            $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                    FROM sa_sub_category AS ssc 
                                    LEFT JOIN sa_category AS sc 
                                    ON sc.category_id = ssc.category_id");
            $subcategoryList = $stmt->fetchAll();
            return ['status' => true, 'response' => ['message' => 'Категорія створена', 'categories' => $categoryList, 'subcategories' => $subcategoryList]];
        }else
        {
            return ['status' => false, 'response' => ['message' => 'Виникла помилка, категорія не створена']];
        }
    }

    //изменение анкетных данных пользователя
    public function chengeUser($data)
    {
        //поиск пользователя по электроннному адресу
        $stmt = DB::run("SELECT * FROM `sa_users` WHERE `user_email`= ?", [$data['email']]);
        $user = $stmt->fetchAll();
        //возврпт ошибки в случае отсутствия совпадения
        if(count($user) != 1){
            return ['status' => false, 'response' => ['message' => "Користувача з поштою {$data['email']} не існує"]];
        }
        //получение анкетных данных выбраной компании
        $stmt = DB::run("SELECT * FROM `sa_company` WHERE `company_name`= ?", [$data['company']]);
        $company = $stmt->fetchAll();
        //возврат ошибки в случае отсутствия совпадения
        if(count($company) < 1)
        {
            return ['status' => false, 'response' => ['message' => 'Такого підприємства не існує в базі']];
        }else
        {
            //обновление анкетных данных пользоваля 
            $stmt = DB::prepare("UPDATE sa_users SET user_name = ?, user_phone = ?, company_id = ?, user_position = ?, user_role = ?, user_status = ? WHERE `user_email` = ?");
            $stmt->execute([$data['name'], $data['phone'], $company[0]['company_id'], $data['position'], $data['role'], $data['status'], $data['email']]);
            if($stmt)
            {
                //возврат обновленного списка пользователей
                $allUsers = StaticStatement::getAllUsers();
                return ['status' => true, 'response' => ['message' => 'Користувача змінено', 'users' => $allUsers]];
            }else{
                return ['status' => false, 'response' => ['message' => 'Виникла помилка']];
            }
        }
    }

    //метод изменения категории
    public function chengeCategory($data)
    {
        $stmt = DB::run("SELECT * FROM sa_category WHERE category_id = ? AND category = ?", [$data['key'], $data['oldName']]);
        $category = $stmt->fetchAll();

        if(count($category) > 0)
        {
            $stmt = DB::prepare("UPDATE sa_category SET category = ? WHERE category_id = ?");
            $stmt->execute([$data['newName'], $data['key']]); 

            if($stmt)
            {
                $categoryList = StaticStatement::getAllCategory();
                $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                        FROM sa_sub_category AS ssc 
                                        LEFT JOIN sa_category AS sc 
                                        ON sc.category_id = ssc.category_id");
                $subcategoryList = $stmt->fetchAll();
                return ['status' => true, 'response' => ['message' => 'Категорія змінена', 'categories' => $categoryList, 'subcategories' => $subcategoryList]];
            }else{
                return ['status' => false, 'response' => ['message' => 'Виникла помилка']];
            }

        }else
        {
            return ['status' => false, 'response' => ['message' => 'Такої категорії не існує']];
        }
    }
    //метод изменения подкатегории категории
    public function chengeSubcategory($data)
    {
        $stmt = DB::run("SELECT * FROM sa_category WHERE category = ?", [$data['category']]);
        $category = $stmt->fetchAll();

        if(count($category) == 0)
        {
            return ['status' => false, 'response' => ['message' => 'Такої категорії не існує']];
        }

        $stmt = DB::run("SELECT * FROM sa_sub_category WHERE category_id = ? AND sub_category_id = ? AND sub_category = ?", [$category[0]['category_id'], $data['subcategoryKey'], $data['oldName']]);
        $subcategory = $stmt->fetchAll();

        if(count($subcategory) > 0)
        {
            $stmt = DB::prepare("UPDATE sa_sub_category SET sub_category = ? WHERE category_id = ? AND sub_category_id = ?");
            $stmt->execute([$data['newName'], $category[0]['category_id'], $data['subcategoryKey']]); 

            if($stmt)
            {
                $categoryList = StaticStatement::getAllCategory();
                $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                        FROM sa_sub_category AS ssc 
                                        LEFT JOIN sa_category AS sc 
                                        ON sc.category_id = ssc.category_id");
                $subcategoryList = $stmt->fetchAll();
                return ['status' => true, 'response' => ['message' => 'Підкатегорія змінена', 'categories' => $categoryList, 'subcategories' => $subcategoryList]];
            }else{
                return ['status' => false, 'response' => ['message' => 'Виникла помилка']];
            }

        }else
        {
            $stmt = DB::prepare("INSERT INTO sa_sub_category (category_id, sub_category) VALUES (?, ?)");
            $stmt->execute([$category[0]['category_id'], $data['newName']]);

            if($stmt)
            {
                $categoryList = StaticStatement::getAllCategory();
                $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                        FROM sa_sub_category AS ssc 
                                        LEFT JOIN sa_category AS sc 
                                        ON sc.category_id = ssc.category_id");
                $subcategoryList = $stmt->fetchAll();
                return ['status' => true, 'response' => ['message' => 'Підкатегорія створена', 'categories' => $categoryList, 'subcategories' => $subcategoryList]];
            }else{
                return ['status' => false, 'response' => ['message' => 'Виникла помилка']];
            }
        }
    }
    //метод удаления категории
    public function categoryDeletion($data)
    {
        $stmt = DB::run("SELECT * FROM sa_category WHERE category_id = ? AND category = ?", [$data['id'], $data['name']]);
        $category = $stmt->fetchAll();

        if(count($category) == 0){
            return ['status' => false, 'response' => ['message' => 'Такої категорії не існує']];
        }else{
            $stmt = DB::prepare("DELETE FROM sa_category WHERE category_id = ? AND category = ?");
            $result = $stmt->execute([$data['id'], $data['name']]);
            $stmt = DB::prepare("DELETE FROM sa_sub_category WHERE category_id = ?");
            $result = $stmt->execute([$data['id']]);
    
            $stmt = DB::run("SELECT category_id FROM sa_category WHERE category = ?", ['Загальна']);
            $defaultCat = $stmt->fetchAll();
    
            $stmt = DB::run("SELECT sub_category_id FROM sa_sub_category WHERE category_id = ?", [$defaultCat[0]['category_id']]);
            $defaultSubcat = $stmt->fetchAll();
    
            $stmt = DB::prepare("UPDATE sa_posts SET category_id = ?, sub_category_id = ? WHERE category_id = ?");
            $stmt->execute([$defaultCat[0]['category_id'], $defaultSubcat[0]['sub_category_id'], $data['id']]);
    
            $categoryList = StaticStatement::getAllCategory();
            $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                    FROM sa_sub_category AS ssc 
                                    LEFT JOIN sa_category AS sc 
                                    ON sc.category_id = ssc.category_id");
            $subcategoryList = $stmt->fetchAll();
            $myPosts = StaticStatement::getPost($_SESSION['userKosmos']['user_id']);
            return ['status' => true, 'response' => ['message' => 'Категорія видалена', 'categories' => $categoryList, 'subcategories' => $subcategoryList, 'posts' => $myPosts]];
        }
    }
    //метод удаления подкатегории
    public function subcategoryDeletion($data)
    {
        $stmt = DB::run("SELECT * FROM sa_category WHERE `category` = ?", [$data['categoryName']]);
        $categories = $stmt->fetchAll();

        if(count($categories) == 0)
        {
            return ['status' => false, 'response' => ['message' => 'Такої категорії не існує']];
        }else
        {
            $stmt = DB::run("SELECT * FROM sa_sub_category WHERE `category_id` = ?", [$categories[0]['category_id']]);
            $subcategories = $stmt->fetchAll();

            if(count($subcategories) == 1)
            {
                return ['status' => false, 'response' => ['message' => 'Не можна видалити останню підкатегорію']];
            }else{
                $stmt = DB::prepare("DELETE FROM sa_sub_category WHERE sub_category_id = ? AND category_id = ? AND sub_category = ?");
                $result = $stmt->execute([$data['id'], $categories[0]['category_id'], $data['name']]);
                
                $stmt = DB::run("SELECT category_id FROM sa_category WHERE category = ?", ['Загальна']);
                $defaultCat = $stmt->fetchAll();
        
                $stmt = DB::run("SELECT sub_category_id FROM sa_sub_category WHERE category_id = ?", [$defaultCat[0]['category_id']]);
                $defaultSubcat = $stmt->fetchAll();
        
                $stmt = DB::prepare("UPDATE sa_posts SET category_id = ?, sub_category_id = ? WHERE sub_category_id = ?");
                $stmt->execute([$defaultCat[0]['category_id'], $defaultSubcat[0]['sub_category_id'], $data['id']]);
        
                $categoryList = StaticStatement::getAllCategory();
                $stmt = DB::run("SELECT ssc.sub_category_id, sc.category, ssc.sub_category 
                                        FROM sa_sub_category AS ssc 
                                        LEFT JOIN sa_category AS sc 
                                        ON sc.category_id = ssc.category_id");
                $subcategoryList = $stmt->fetchAll();
                $myPosts = StaticStatement::getPost($_SESSION['userKosmos']['user_id']);
                return ['status' => true, 'response' => ['message' => 'Підкатегорія видалена', 'categories' => $categoryList, 'subcategories' => $subcategoryList, 'posts' => $myPosts]];
            }
        }
    }
    //метод удаления запии на проверке у администратора
    public function postDeletion($data)
    {
        $stmt = DB::run("SELECT * FROM sa_posts WHERE post_id = ? AND `status` = ?", [$data['id'], 0]);
        $post = $stmt->fetchAll();

        if(count($post) === 0)
        {
            return ['status' => false, 'response' => ['message' => 'Такого запису не існує']];
        }else
        {
            $stmt = DB::prepare("DELETE FROM sa_posts WHERE post_id = ? AND `status` = ?");
            $result = $stmt->execute([$data['id'], 0]);

            $forCheckingPost = StaticStatement::getAllPostForVerification();
            return ['status' => true, 'response' => ['message' => 'Запис видален', 'checking_posts' => $forCheckingPost]];
        }
    }
    //метод подтверждения записи на проверке у администратора
    public function postConfirm($data)
    {
        $stmt = DB::run("SELECT * FROM sa_posts WHERE post_id = ? AND `status` = ?", [$data['id'], 0]);
        $post = $stmt->fetchAll();

        if(count($post) === 0)
        {
            return ['status' => false, 'response' => ['message' => 'Такого запису не існує']];
        }else
        {
            $stmt = DB::prepare("UPDATE sa_posts SET `status` = ? WHERE post_id = ?");
            $stmt->execute([1, $data['id']]);

            $forCheckingPost = StaticStatement::getAllPostForVerification();
            return ['status' => true, 'response' => ['message' => 'Запис схвален', 'checking_posts' => $forCheckingPost]];
        }
    }
}