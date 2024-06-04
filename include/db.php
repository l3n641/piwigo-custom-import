<?php

class DB
{

    public static function get_site_id($galleries_url)
    {
        $query = sprintf("select * from %s where galleries_url='%s' limit 1", SITES_TABLE, $galleries_url);
        $result = pwg_db_fetch_assoc(pwg_query($query));
        if ($result) {
            return $result["id"];
        }
        return null;
    }

    public static function create_site($galleries, $dir = ""): int
    {
        $create_sql = "insert into %s (galleries_url) values('%s')";
        $query = sprintf($create_sql, SITES_TABLE, $galleries);
        pwg_query($query);
        return pwg_db_insert_id();

    }

    public static function get_category($site_id, $dir_name, $id_uppercat = "")
    {

        $sql = "select * from %s where dir='%s' and site_id=%d ";
        if ($id_uppercat) {
            $sql = $sql . " and id_uppercat= {$id_uppercat} ";
        }
        $sql = $sql . " limit 1";

        $query = sprintf($sql, CATEGORIES_TABLE, $dir_name, $site_id);
        $result = pwg_db_fetch_assoc(pwg_query($query));
        if ($result) {
            return $result["id"];
        }
        return null;
    }


    public static function get_category_by_name($site_id, $name, $id_uppercat = "")

    {
        $sql = "select * from %s where name='%s' and site_id=%d ";
        if ($id_uppercat) {
            $sql = $sql . " and id_uppercat= {$id_uppercat} ";
        }
        $sql = $sql . "limit 1";


        $query = sprintf($sql, CATEGORIES_TABLE, $name, $site_id);
        return pwg_db_fetch_assoc(pwg_query($query));
    }

    public static function create_category($site_id, $dir_name, $id_uppercat, $name = null): int
    {
        if ($name === null) {
            $name = $dir_name;
        }
        if (empty($id_uppercat)) {
            $id_uppercat = "null";
        }

        $create_sql = "insert into %s (name,dir,site_id,id_uppercat) values('%s','%s','%d',%s)";
        $query = sprintf($create_sql, CATEGORIES_TABLE, $name, $dir_name, $site_id, $id_uppercat);
        pwg_query($query);
        return pwg_db_insert_id();
    }

    public static function update_category_uppercats($id, $uppercats)
    {
        $create_sql = "update  %s set uppercats='%s' where id =%d";
        $query = sprintf($create_sql, CATEGORIES_TABLE, $uppercats, $id);
        pwg_query($query);
    }

    public static function get_image_by_path($path)
    {
        $sql = "select * from %s where path='%s' limit 1";

        $query = sprintf($sql, IMAGES_TABLE, $path);
        return pwg_db_fetch_assoc(pwg_query($query));
    }

    public static function sava_image($path, $storage_category_id, $name = "", $comment = "")
    {
        $file = basename($path);
        $result = self::get_image_by_path($path);
        if ($result) {
            $image_id = $result["id"];
            $create_sql = "update %s set  name='%s',comment='%s',storage_category_id='%d' where path='%s'";
            $query = sprintf($create_sql, IMAGES_TABLE, $name, $comment, $storage_category_id, $path);
            pwg_query($query);

        } else {
            $create_sql = "insert into %s (file,name,comment,path,storage_category_id) values('%s','%s','%s','%s',%d)";
            $query = sprintf($create_sql, IMAGES_TABLE, $file, $name, $comment, $path, $storage_category_id);
            pwg_query($query);
            $image_id = pwg_db_insert_id();
        }

        self::set_image_category_id($image_id, $storage_category_id);
        return $image_id;
    }

    public static function set_image_category_id($image_id, $category_id, $rank = 1)
    {
        $sql = "select * from %s where image_id =%d and category_id=%d  limit 1";
        $query = sprintf($sql, IMAGE_CATEGORY_TABLE, $image_id, $category_id);
        $result = pwg_db_fetch_assoc(pwg_query($query));
        if (!$result) {
            $create_sql = "insert into %s (image_id,category_id,rank) values('%d','%d','%d')";
            $query = sprintf($create_sql, IMAGE_CATEGORY_TABLE, $image_id, $category_id, $rank,);
            pwg_query($query);
        }
    }

    public static function update_category_representative_picture($category_id, $representative_picture_id)
    {
        $create_sql = "update %s set  representative_picture_id=%d where id= %d";
        $query = sprintf($create_sql, CATEGORIES_TABLE, $representative_picture_id, $category_id);
        pwg_query($query);
    }

}
