<?php

class Favorite extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    /***
     * @param $user_id
     * @return mixed
     *
     */
    public function get($user_id)
    {
        $this->db->select("product.*");
        $this->db->where('user_id', $user_id);
        $this->db->from('favorites');
        $this->db->join('product', 'product.id = favorites.favoriteable_id', 'left');
        $query = $this->db->get();
        $favorites = $query->result_array();
        $data = [];
        foreach ($favorites as $favorite => $key){
                $key['is_favorite'] = true;
                $data [] = $key;
        }
        return $data;
    }

    /***
     * @param $array
     * @return mixed
     *
     */
    public function add($array)
    {
        $this->db->insert('favorites', $array);

        return $this->db->insert_id();
    }

    /***
     * @param $array
     * @param $id
     *
     */
    public function update($id, $array)
    {
        $this->db->update('favorites', $array, array('id' => $id));
    }


    /***
     * @param $favoriteable_id
     * @param $user_id
     *
     */
    public function delete($user_id, $favoriteable_id)
    {
        $this->db->delete('favorites', array('user_id' => $user_id,'favoriteable_id' => $favoriteable_id));
    }
}
