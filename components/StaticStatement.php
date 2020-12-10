<?php
class StaticStatement
{

    function __construct()
    {
    }

    public static function getAllUsers()
    {
        $stmt = DB::run("SELECT su.user_id, su.user_email, su.user_data_reg, su.user_name, su.user_phone, su.user_position, su.user_role, su.user_status, sco.company_name 
                                FROM sa_users AS su 
                                LEFT JOIN sa_company AS sco ON sco.company_id = su.company_id");
        $users = $stmt->fetchAll();
        return $users;
    }
    public static function getUserByEmail($userEmail)
    {
        $stmt = DB::run("SELECT su.user_id, su.user_email, su.user_hash_password, su.user_name, su.user_phone, su.company_id, su.user_position, su.user_role, su.user_status, sco.company_name, sco.company_address 
                                FROM sa_users AS su
                                LEFT JOIN sa_company AS sco ON sco.company_id = su.company_id 
                                WHERE su.user_email= ?", [$userEmail]);
        $user = $stmt->fetchAll();
        return $user;
    }
    public static function getAllCategory()
    {
        $stmt = DB::run("SELECT * FROM sa_category");
        $category = $stmt->fetchAll();
        return $category;
    }
    public static function getAllSubCategory($catId)
    {
        $stmt = DB::run("SELECT sub_category_id, sub_category FROM sa_sub_category WHERE category_id = ?", [$catId]);
        $subCategory = $stmt->fetchAll();
        return $subCategory;
    }
    public static function getAllCompany()
    {
        $stmt = DB::run("SELECT * FROM sa_company");
        $company = $stmt->fetchAll();
        return $company;
    }
    public static function getPost($userId)
    {
        $stmt = DB::run("SELECT sp.post_id, sp.note, sp.status, sp.user_id, sc.category, ssc.sub_category 
                                FROM sa_posts AS sp
                                LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                                LEFT JOIN sa_sub_category  AS ssc ON ssc.sub_category_id = sp.sub_category_id
                                WHERE sp.user_id = ?", [$userId]);
        $post = $stmt->fetchAll();
        return $post;
    }
    public static function getAllPostForVerification()
    {
        $stmt = DB::run("SELECT sp.post_id, sp.note, sc.category, ssc.sub_category, su.user_name, su.user_email, su.user_phone, su.user_position, sco.company_name 
                            FROM sa_posts AS sp
                            LEFT JOIN sa_category AS sc ON sc.category_id = sp.category_id
                            LEFT JOIN sa_sub_category AS ssc ON ssc.sub_category_id = sp.sub_category_id
                            LEFT JOIN sa_users AS su ON su.user_id = sp.user_id
                            LEFT JOIN sa_company AS sco ON sco.company_id = sp.company_id
                            WHERE status = 0");
        $post = $stmt->fetchAll();
        return $post;
    }
    
}				