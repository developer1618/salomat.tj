<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

require APPPATH. 'libraries/REST_Controller.php';

class Products extends REST_Controller {

    /**
     * Construction
     */
    public function __construct(){

        parent::__construct();
        $this->load->helper('security');
        $this->load->model('product');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->helper('cookie');
        $this->load->helper('url');
        $this->load->model('user');
        $this->load->model('category');
        $this->load->model('slider');
        $this->load->model('page');
        $this->load->model('rating');
        $this->load->model('indication');
        $this->load->model('advertisement');
        $this->load->model('ActiveSubstance');
        $this->load->model('blog');
        $this->load->model('tag');
        $this->load->model('sms');
        $this->load->model('delivery');
    }

    /**
     * Display a listing of the Products.
     *
     */
    public function index_get()
    {
        header("Access-Control-Allow-Origin: *");

//        $this->load->library('Authorization_Token');
//        $is_valid_token = $this->authorization_token->validateToken();

//        if(!empty($is_valid_token) && $is_valid_token['status'] === TRUE) {
//            $data['categories'] = $this->category->get_all();
//            $data['main_slider'] = $this->slider->get_all('normal');
//            $data['ad_mini'] = $this->advertisement->get_all('mini_pic');
//            $data['prods_of_the_day'] = $this->product->get_prods_by_slider_type('product_of_the_day');
//
//            $isFromMyBabilon = $this->input->get('from');
//            if ($isFromMyBabilon == 'babilon') {
//                $babilonUser = $this->input->get('phone');
//                $bUserInfo = array(
//                    'from' => $isFromMyBabilon,
//                    'phone' => $babilonUser
//                );
//                setcookie($this->bUserInfoName, json_encode($bUserInfo));
//            }
//
//            $categories = $this->category->get_all();
//            $j = 0;
//            $array = array();
//            for ($i = 0; $i < sizeof($categories); $i++) {
//                if ($categories[$i]['category_in_main'] == 1 && $j < 3) {
//                    $array[$j]['categ'] = $categories[$i];
//                    $array[$j]['categ_slider'] = $this->slider->get_by_slider_category($categories[$i]['id']);
//                    $array[$j]['categ_prods'] = $this->product->get_prods_in_categ($categories[$i]['id']);
//                    $j++;
//                }
//            }
            $data['meta_social_image'] = base_url('salomat_apteka.jpg');
            $data['meta_social_url'] = base_url();
            $data['meta_social_title'] = 'Интернет аптека Salomat.tj, купить онлайн лекарственные препараты и товары для здоровья';
            $data['meta_social_desc'] = 'Удобный поиск лекарств, можно заказать любые препараты недорого по выгодным ценам. Удобный каталог лекарств, инструкций и советы врачей! ☎ 9990 Salomat.tj';
            $data['title'] = 'Интернет аптека Salomat.tj, купить онлайн лекарственные препараты и товары для здоровья';
//            $data['categories_for_main_page'] = $array;

            $this->response($data, REST_Controller::HTTP_OK);

//        } else {
//            $message = [
//                'status' => FALSE,
//                'message' => $is_valid_token['message']
//            ];
//            $this->response($message, REST_Controller::HTTP_OK);
//        }
    }

    /**
     * Get category of products.
     *
     */
    public function categories_get()
    {
        header("Access-Control-Allow-Origin: *");
        $data['categories'] = $this->category->get_all();

        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Get main sliders for home.
     *
     */
    public function main_sliders_get()
    {
        header("Access-Control-Allow-Origin: *");
        $data['main_slider'] = $this->slider->get_all('normal');

        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Get main sliders for home.
     *
     */
    public function ad_mini_get()
    {
        header("Access-Control-Allow-Origin: *");
        $data['ad_mini'] = $this->advertisement->get_all('mini_pic');

        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Get main sliders for home.
     *
     */
    public function prods_of_the_day_get()
    {
        $user_id = $this->input->get('user_id');

        header("Access-Control-Allow-Origin: *");
        $data['prods_of_the_day'] = $this->product->get_prods_by_slider_type('product_of_the_day',$user_id?:0);

        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Get main sliders for home.
     *
     */
    public function categories_for_main_page_get()
    {
        header("Access-Control-Allow-Origin: *");
        $categories = $this->category->get_all();
        $j = 0;
        $array = array();
        for ($i = 0; $i < sizeof($categories); $i++) {
            if ($categories[$i]['category_in_main'] == 1 && $j < 3) {
                $array[$j]['categ'] = $categories[$i];
                $array[$j]['categ_slider'] = $this->slider->get_by_slider_category($categories[$i]['id']);
                $array[$j]['categ_prods'] = $this->product->get_prods_in_categ($categories[$i]['id']);
                $j++;
            }
        }
        $data['categories_for_main_page'] = $array;

        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function user_orders_get($id)
    {
        $this->db->select('*');
        $this->db->from('user_order');
        $this->db->order_by("order_id", "desc");
        $this->db->where('user_id', $id);
        $this->db->join('order', 'user_order.order_id = order.id');
        $query = $this->db->get();
        $order = $query->result();
        $data = array();
        foreach ( $order as $item => $value) {
            $this->db->select('*');
            $this->db->from('product_order');
            $this->db->where('order_id', $value->id);
            $this->db->join('product', 'product_order.product_id = product.id');
            $product = $this->db->get();
            if ($product){
                $this->db->select('delivery_price');
                $this->db->from('delivery');
                $this->db->where('delivery_id', $value->delivery_type);
                $delivery = $this->db->get();
            }
            if ($product){
                $this->db->select('status_text');
                $this->db->from('status');
                $this->db->where('id', $value->status_id);
                $status = $this->db->get();
            }
            $data [] = [
                'order' => $value,
                'status' => $status->result(),
                'delivery' => $delivery->result(),
                'products' => $product->result(),
            ];
        }

        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function order_products_get($id)
    {
        $this->db->select('*');
        $this->db->from('product_order');
        $this->db->where('order_id', $id);
        $this->db->join('product', 'product_order.product_id = product.id');
        $query = $this->db->get();
        $this->response($query->result(), REST_Controller::HTTP_OK);
    }

    public function category_products_get($id)
    {
        var_dump($id);
        $current_page = $this->input->get('page');
        $sort_by = $this->input->get('sort_by');

        if ($current_page == '' || $current_page < 0) {
            $current_page = 1;
        }
        if ($sort_by == '') {
            $sort_by = 'asc';
        } else if ($sort_by == 'pr') {
            $sort_by = 'product_rating';
        }

        $category_with_parents = $this->category->get_parent_categories($id);

        if (sizeof($category_with_parents) == 0) {
            redirect('errors/cli/error_404', TRUE);
            return;
        }

        $res = $this->product->get_products_by_category($id, $current_page, $sort_by); //returns products with total count > 0
        $data['total_products'] = $res['total_products'];
        $data['category_with_parents'] = $category_with_parents;
        unset($res['total_products']);

        if (isset($category_with_parents['parent_cat']['parent_cat'])) {
            $data['category'] = $this->category->get_with_children($category_with_parents['parent_cat']['id']);
        } else {
            $data['category'] = $this->category->get_with_children($category_with_parents['id']);
        }
        $data['second_category_id'] = $data['category']['id'];
        $ad_slider = $this->advertisement->get_slider_by_category($id, 'slider');
        $data['ad_slider'] = sizeof($ad_slider) != 0 ? $ad_slider : [];



        $data['categories_for_main_page'] = [];

        if (isset($res['prod_max_price'])) {
            $data['prod_max_price'] = $res['prod_max_price'];
        } else {
            $data['prod_max_price'] = 9999;
        }
        $data['title'] = $category_with_parents['category_name'] . ' на Salomat.tj';
        $data['isOnlySecondCategory'] = $res['isOnlySecondCategory'];
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function all_get()
    {
        $page = $this->input->get('page');
        $user_id = $this->input->get('user_id');
        $this->db->select('product.*');
        $this->db->from('product');
//        $this->db->join('favorites', 'product.id = favorites.favoriteable_id');
//        $this->db->join('users',     'favorites.user_id = users.user_id');
//        $this->db->join('favorites', 'favorites.favoriteable_id', '=', 'product.id');
        $this->db->limit(10,$page);
        $query = $this->db->get();
//        $array = $query->result_array();

        $page = $this->input->get('page');
        $this->db->select('product.*');
        $this->db->from('product');
        $this->db->join('favorites', 'product.id = favorites.favoriteable_id');
        $this->db->where('favorites.user_id', $user_id);
        $query2 = $this->db->get();
        $array2 = $query2->result_array();

        $product_id = [];
        $rde = [];
//        foreach ($array as $item){
//
//            $product_id[] = $item['id'];
//
//
//            foreach ($array2 as $it){
//
//                if ( $it['id'] == $product_id){
//
//                    $rde[] = $product_id;
//                }
//            }
//        }

        foreach ($query->result_array() as $row) {
            $rating = $this->get_rating($row['id']);
            if (sizeof($rating) != 0) {
                $row['prod_rating_average'] = $rating['prod_rating_average'];
                $row['review_count'] = $rating['review_count'];
            } else {
                $row['prod_rating_average'] = '';
                $row['review_count'] = 0;
            }

            $favorite = $this->get_favorite($row['id']);
            if (sizeof($favorite) != 0) {
                $row['is_favorite'] = true;
            } else {
                $row['is_favorite'] = false;
            }
            $row['base_url'] = base_url();

            $array[] = $row;
        };

        foreach ($query->result_array() as $row) {


        }
        $this->response($array, REST_Controller::HTTP_OK);
    }

    public function get_rating($id)
    {
        $array = array();
        $q_count = $this->db->query("SELECT COUNT(*) as count FROM `product_rating` WHERE `prod_id` = " . $id . " AND `status` = 1");
        $arr_count = $q_count->result_array();
        if ($arr_count[0]['count'] != 0) {
            $this->db->select('*');
            $this->db->from('product_rating');
            $this->db->where('prod_id', $id);
            $this->db->where('status', '1');
            $query = $this->db->get();
            $count = 0;
            $ones = 0;
            $twos = 0;
            $threes = 0;
            $fours = 0;
            $fives = 0;
            foreach ($query->result_array() as $pr) {
                $count += $pr['star_rating'];
                if ($pr['star_rating'] == 1) {
                    $ones += 1;
                } else if ($pr['star_rating'] == 2) {
                    $twos += 1;
                } else if ($pr['star_rating'] == 3) {
                    $threes += 1;
                } else if ($pr['star_rating'] == 4) {
                    $fours += 1;
                } else if ($pr['star_rating'] == 5) {
                    $fives += 1;
                }
            }
            $rating = round($count / $arr_count[0]['count']);
            $array['prod_rating_average'] = $rating;

            $array['prod_rating_each']['ones']['count'] = $ones;
            $array['prod_rating_each']['ones']['percentage'] = round(($ones / $arr_count[0]['count']) * 100);

            $array['prod_rating_each']['twos']['count'] = $twos;
            $array['prod_rating_each']['twos']['percentage'] = round(($twos / $arr_count[0]['count']) * 100);

            $array['prod_rating_each']['threes']['count'] = $threes;
            $array['prod_rating_each']['threes']['percentage'] = round(($threes / $arr_count[0]['count']) * 100);

            $array['prod_rating_each']['fours']['count'] = $fours;
            $array['prod_rating_each']['fours']['percentage'] = round(($fours / $arr_count[0]['count']) * 100);

            $array['prod_rating_each']['fives']['count'] = $fives;
            $array['prod_rating_each']['fives']['percentage'] = round(($fives / $arr_count[0]['count']) * 100);

            $array['review_count'] = $arr_count[0]['count'];
        }

        return $array;
    }

    public function get_favorite($id)
    {
        $array = array();
        $q_count = $this->db->query("SELECT COUNT(*) as count FROM `favorites` WHERE `favoriteable_id` = " . $id . " AND `user_id` = 61");
        $arr_count = $q_count->result_array();
        if ($arr_count[0]['count'] != 0) {
            $this->db->select('*');
            $this->db->from('favorites');
            $this->db->where('favoriteable_id', $id);
            $this->db->where('user_id', '61');
            $query = $this->db->get();
            $array['is_favorite'] = $query->result_array();

        }
        return $array;
    }
}