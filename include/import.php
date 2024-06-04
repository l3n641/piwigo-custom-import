<?php
include_once(CUSTOM_IMPORT_PATH . 'include/db.php');

class Import
{

    private $data_file;
    private $galleries_url;
    private $site_id;

    public function __construct($data_file, $galleries_url)
    {
        $this->data_file = $data_file;
        $this->galleries_url = $galleries_url;
        $this->site_id = $this->get_site_id();
    }


    public function read_json(): array
    {
        $products = [];
        $json_content = file_get_contents($this->data_file);
        $data = json_decode($json_content, true);
        // 检查解析是否成功
        if (json_last_error() === JSON_ERROR_NONE) {
            foreach ($data["RECORDS"] as $item) {
                $products[] = new Product($item);
            }
        }
        return $products;
    }

    public function get_site_id()
    {
        $site_id = Db::get_site_id($this->galleries_url);
        if (!$site_id) {
            $site_id = DB::create_site($this->galleries_url);
        }
        return $site_id;
    }

    /**  获取主相册id
     * @return int
     */
    public function get_main_album_id(): int
    {
        $base_dir_name = basename($this->galleries_url);

        $cid = DB::get_category($this->site_id, $base_dir_name);
        if (!$cid) {
            $cid = DB::create_category($this->site_id, $base_dir_name, "null");
            DB::update_category_uppercats($cid, $cid);
        }
        return $cid;
    }

    /** 保存分类相册
     * @param $album_name
     * @param $id_uppercat
     * @return int|mixed 分类相册id
     */
    public function save_category_album($album_name, $id_uppercat)
    {
        $result = DB::get_category_by_name($this->site_id, $album_name, $id_uppercat);
        if ($result) {
            $cid = $result["id"];
        } else {
            $cid = DB::create_category($this->site_id, $album_name, $id_uppercat);
            DB::update_category_uppercats($cid, $id_uppercat . ",$cid");
        }

        return $cid;

    }

    /***保存产品相册
     * @param $album_dir_name
     * @param $album_name
     * @param $id_uppercat
     * @param $uppercats
     * @return int|mixed
     */
    public function save_product_album($album_dir_name, $album_name, $id_uppercat, $uppercats)
    {
        $result = DB::get_category($this->site_id, $album_dir_name, $id_uppercat);
        if ($result) {
            $cid = $result["id"];
        } else {
            $cid = DB::create_category($this->site_id, $album_name, $id_uppercat);
            DB::update_category_uppercats($cid, $uppercats);
        }

        return $cid;

    }


    public function run($is_set_main_alum)
    {
        $main_albums_id = "";
        if ($is_set_main_alum) {
            $main_albums_id = $this->get_main_album_id();
        }

        $products = $this->read_json();
        $success_quantity = 0;
        $failed_sku = [];
        foreach ($products as $key => $product) {
            $sku = $product->get_goods_id();

            if (!$sku) {
                continue;
            }
            $category_album_id = $this->save_category_album($product->get_tag(), $main_albums_id,);
            if ($is_set_main_alum) {
                $uppercats = "$main_albums_id,$category_album_id";
            } else {
                $uppercats = "$category_album_id";
            }
            $product_album_id = $this->save_product_album($sku, $key, $category_album_id, $uppercats);

            $representative_picture_id = 0;
            $title = pwg_db_real_escape_string($product->get_post_title());
            foreach ($product->get_product_gallery() as $path) {
                $base_dir = dirname($this->galleries_url);

                $image_path = $base_dir . "/" . $path;
                $image_id = DB::sava_image($image_path, $product_album_id, $sku, $title);
                if (!$representative_picture_id) {
                    $representative_picture_id = $image_id;
                }
            }

            DB::update_category_representative_picture($product_album_id, $representative_picture_id);


            $success_quantity = $success_quantity + 1;

        }

        return ["total" => count($products), "success_quantity" => $success_quantity, "failed_sku" => $failed_sku];
    }


}