<?php
class ModelSearch
{
    function __construct()
    {
    }

    public function searchImportAllPost($data)
    {
        //Объявляем переменную для хранения идентификатора выбранной подкатегории пользователем
        $keySubCat = false;

        //Выполняем запрос на получение категории Импортозамещения и сохраняем ее ID
        $stmt = DB::run("SELECT * FROM sa_category WHERE category = 'Імпортозаміщення'");
        $importCatId = $stmt->fetchAll();
        $id = $importCatId[0]['category_id'];

        //Выполняем запрос на получение всех подкатегорий Импортозамещения
        $allSubCat = StaticStatement::getAllSubCategory($id);

        //Перебираем массив всех подкатегорий Импортозамещения, ищем ту категорию которую выбрал пользователь. Если совпадение найдено перезаписываем переменную $keySubCat
        foreach ($allSubCat as $item)
        {   
            if($item['sub_category'] === $data)
            {
                $keySubCat = $item['sub_category_id'];
            }
        }

        //Если в переменной $keySubCat есть индекс, выполняем поиск всех записей по данному индексу, иначе ищем по всем подкатегориям
        if($keySubCat)
        {
            $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, su.user_name, sco.company_name
                                FROM sa_posts AS sp
                                LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                LEFT JOIN sa_users AS su ON su.user_id = sp.user_id
                                LEFT JOIN sa_company AS sco ON sco.company_id = sp.company_id
                                WHERE sp.status = '1'
                                AND sp.sub_category_id = ?", [$keySubCat]);

            $postList = $stmt->fetchAll();
            return ['status' => true, 'response' => $postList];
        }else{
            $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, su.user_name, sco.company_name
                                FROM sa_posts AS sp
                                LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                LEFT JOIN sa_users AS su ON su.user_id = sp.user_id
                                LEFT JOIN sa_company AS sco ON sco.company_id = sp.company_id
                                WHERE sp.status = '1'
                                AND sp.category_id = ?", [$id]);

            $postList = $stmt->fetchAll();
            return ['status' => true, 'response' => $postList];
        }

    }

    public function keywordSearchImportPost($data)
    {
        //Объявляем переменную для хранения идентификатора выбранной подкатегории пользователем
        $keySubCat = false;

        //Выполняем запрос на получение категории Импортозамещения и сохраняем ее ID
        $stmt = DB::run("SELECT * FROM sa_category WHERE category = 'Імпортозаміщення'");
        $importCatId = $stmt->fetchAll();
        $id = $importCatId[0]['category_id'];

        //Выполняем запрос на получение всех подкатегорий Импортозамещения
        $allSubCat = StaticStatement::getAllSubCategory($id);

        //Перебираем массив всех подкатегорий Импортозамещения, ищем ту категорию которую выбрал пользователь. Если совпадение найдено перезаписываем переменную $keySubCat
        foreach ($allSubCat as $item)
        {   
            if($item['sub_category'] === $data['subcategory'])
            {
                $keySubCat = $item['sub_category_id'];
            }
        }
        //Очистка ключевых слов
        $keyword = explode(' ', $data['keyword']);
        $clenKeyword = preg_replace(['/[[:punct:]]/', '/^([а-яА-яA-Za-zёЁіІїЇЄє-]){0,2}$/'], '', $keyword);
        $newKeyword = implode(' ', array_diff($clenKeyword, ['']));

        if(!$newKeyword)
        {
            return ['status' => false, 'response' => 'Ключові слова з низьким вмістом'];
        }else
        {
            if($keySubCat)
            {
                $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, sco.company_name
                                    FROM sa_posts AS sp
                                    LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                    LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                    LEFT JOIN sa_company AS sco ON sco.company_id = sp.company_id
                                    WHERE MATCH (sp.note) AGAINST (? IN BOOLEAN MODE)
                                    AND sp.status = '1'
                                    AND sp.sub_category_id = ?", [$newKeyword, $keySubCat]);

                $postList = $stmt->fetchAll();
                return ['status' => true, 'response' => $postList];
            }else{
                $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, sco.company_name
                                    FROM sa_posts AS sp
                                    LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                    LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                    LEFT JOIN sa_company AS sco ON sco.company_id = sp.company_id
                                    WHERE MATCH (sp.note) AGAINST (? IN BOOLEAN MODE)
                                    AND sp.status = '1'
                                    AND sp.category_id = ?", [$newKeyword, $id]);

                $postList = $stmt->fetchAll();
                return ['status' => true, 'response' => $postList];
            }
        }
    }

    public function searchAllPost($data)
    {
        //Выполняем запрос на получение данных выбраной компании пользователем
        $stmt = DB::run("SELECT * FROM sa_company WHERE company_name = ?", [$data['company']]);
        $compData = $stmt->fetchAll();
        count($compData) ? $compId = "AND sp.company_id = {$compData[0]['company_id']}" : $compId = "";


        //Выполняем запрос на получение данных выбраной категории пользователем
        $stmt = DB::run("SELECT * FROM sa_category WHERE category = ?", [$data['category']]);
        $catData = $stmt->fetchAll();
        count($catData) ? $catId = "AND sp.category_id = {$catData[0]['category_id']}" : $catId = "";

        $idSubCat = "";
        //если пользователь выбирал категорию для поиска, работаем с подкатегориями
        if($catId){
            //Выполняем запрос на получение всех нужных подкатегорий
            $allSubCat = StaticStatement::getAllSubCategory($catData[0]['category_id']);
            //Перебираем массив всех подкатегорий, ищем ту категорию которую выбрал пользователь.
            foreach ($allSubCat as $item)
            {   
                if($item['sub_category'] === $data['subcategory'])
                {
                    $idSubCat = "AND sp.sub_category_id = {$item['sub_category_id']}";
                }
            }
        }
        //выполняем запрос на поиск подходящих записей
        $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, su.user_name, sco.company_name
                            FROM sa_posts AS sp
                            LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                            LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                            LEFT JOIN sa_users AS su ON su.user_id = sp.user_id
                            LEFT JOIN sa_company AS sco ON sco.company_id = su.company_id
                            WHERE sp.status = '1'
                            {$compId}
                            {$catId}
                            {$idSubCat}");

        $postList = $stmt->fetchAll();
        return ['status' => true, 'response' => $postList];
    }
    public function keywordSearchPost($data)
    {
        //Очистка ключевых слов
        $keyword = explode(' ', $data['keyword']);
        $clenKeyword = preg_replace(['/[[:punct:]]/', '/^([а-яА-яA-Za-zёЁіІїЇЄє-]){0,2}$/'], '', $keyword);
        $newKeyword = implode(' ', array_diff($clenKeyword, ['']));

        if(!$newKeyword)
        {   //возврат ошибки
            return ['status' => false, 'response' => 'Ключові слова з низьким вмістом'];
        }else
        {
            //Выполняем запрос на получение данных выбраной компании пользователем
            $stmt = DB::run("SELECT * FROM sa_company WHERE company_name = ?", [$data['company']]);
            $compData = $stmt->fetchAll();
            count($compData) ? $compId = "AND sp.company_id = {$compData[0]['company_id']}" : $compId = "";

            //Выполняем запрос на получение данных выбраной категории пользователем
            $stmt = DB::run("SELECT * FROM sa_category WHERE category = ?", [$data['category']]);
            $catData = $stmt->fetchAll();
            count($catData) ? $catId = "AND sp.category_id = {$catData[0]['category_id']}" : $catId = "";

            $idSubCat = "";
            if($catId){ //если пользователь выбирал категорию для поиска, работаем с подкатегориями
                //Выполняем запрос на получение всех нужных подкатегорий
                $allSubCat = StaticStatement::getAllSubCategory($catData[0]['category_id']);
                //Перебираем массив всех подкатегорий, ищем ту категорию которую выбрал пользователь.
                foreach ($allSubCat as $item)
                {   
                    if($item['sub_category'] === $data['subcategory'])
                    {
                        //если совпадение найдено, формируем часть строки для поиска по базе
                        $idSubCat = "AND sp.sub_category_id = {$item['sub_category_id']}";
                    }
                }
            }
            //выполняем запрос на поиск подходящих записей
            $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, su.user_name, sco.company_name
                                FROM sa_posts AS sp
                                LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                LEFT JOIN sa_users AS su ON su.user_id = sp.user_id
                                LEFT JOIN sa_company AS sco ON sco.company_id = su.company_id
                                WHERE MATCH (sp.note) AGAINST (? IN BOOLEAN MODE)
                                AND sp.status = '1'
                                {$compId}
                                {$catId}
                                {$idSubCat}", [$newKeyword]);

            $postList = $stmt->fetchAll();
            return ['status' => true, 'response' => $postList];
        }
    }
}			