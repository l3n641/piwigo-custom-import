<?php

class Product
{
    private $meta;

    public function __construct($meta)
    {
        $this->meta = $meta;
    }

    private function get_meta_data($key)
    {
        if (!empty($this->meta[$key])) {
            return $this->meta[$key];
        }
        return "";
    }

    public function get_product_gallery(): array
    {
        $images = $this->get_meta_data("images");
        if ($images) {
            return explode("|", $images);
        }
        return [];
    }

    public function get_image_first()
    {
        $images = $this->get_product_gallery();
        if ($images) {
            return $images[0];
        }
        return "";
    }

    public function get_goods_id()
    {
        return $this->get_meta_data("goods_id");
    }

    public function get_post_title()
    {
        return $this->get_meta_data("title");
    }


    public function get_tag()
    {
        return $this->get_meta_data("tag_name");
    }


}