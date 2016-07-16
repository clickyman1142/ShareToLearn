<?php

/**
 * Description of Category
 *
 * @author BaoToan
 */
class Category extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('mCategory');
    }
    
    public function index() {
        echo count($this->mCategory->getCategories()) . "<br/>";
        echo $this->mCategory->getCategoriesBox(0);
    }
    
    public function addCategory() {
        $hasParentBox = $this->input->post('hasParentBox');
        $name = $this->input->post('newcate');
        $parent = $this->input->post('parent_cate');
        $slug = $this->input->post('slug');
        $desc = $this->input->post('desc');
        
        $category = new ECategory(0, trim($name), $slug, $desc, $parent);
        $category_id = $this->mCategory->addCategory($category);
        if($category_id) {
            $newcate = $this->mCategory->getCategoryById($category_id);
            $categoriesParentBox = $this->mCategory->getCategoriesParentBox(0);
            // If required parentBox then return array include cateBox and parentBox
            if($hasParentBox) {
                $data = array(
                    "category" => $newcate,
                    "categoriesParentBox" => $categoriesParentBox
                );
                echo json_encode($data);
            } else {
                echo json_encode((array)$newcate);
            }
        } else {
            echo '{"status":"failure"}';
        }
    }
    
    public function categories() {
        $segment = trim($this->input->get('p', TRUE));
        $search = trim($this->input->get('search', TRUE));
        
        // Config pagination
        $config = array();
        $config['base_url'] = base_url() . "category";
        $config['prefix'] = "categories?p=";
        $config['per_page'] = 2;
        $config['cur_page'] = $segment;
        
        $limitConfig = array(
            "records" => $config['per_page'],
            "begin" => $segment
        );
        $this->load->library("pagination");
        $result = $this->mCategory->getCategories($limitConfig, $search);
        $config["total_rows"] = $result['total'];
        $data = array(
            "title" => "Danh Sách Thể Loại",
            "content" => "admin/categories",
            "categories" => $result['categories'],
            "links" => pagination($config, $this->pagination),
            "total" => $result['total'],
            "categoriesParentBox" => $this->mCategory->getCategoriesParentBox(0)
        );
        $this->load->view('admin/template/main', $data);
    }
    
    public function deleteCategory() {
        $category_id = $this->input->post('cate_id');
        if($this->mCategory->deleteCategory($category_id)) {
            echo "success";
        } else {
            echo "failure";
        }
    }
    
    public function editCategory($cate_id) {
        $category = $this->mCategory->getCategoryById($cate_id);
        $data = array(
            "category" => $category,
            "title" => "Cập nhật thể loại",
            "content" => "admin/category",
            "categoriesParentBox" => $this->mCategory->getCategoriesParentBox(0, "", array($category->getParent()))
        );
        $this->load->view('admin/template/main', $data);
    }
    
    public function updateCategory() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $parent = $this->input->post('parent_cate');
        $slug = $this->input->post('slug');
        $desc = $this->input->post('desc');
        $count = $this->input->post('count');
        
        $category = new ECategory($id, trim($name), $slug, $desc, $parent, $count);
        $category_id = $this->mCategory->updateCategory($category);
        
        if($category_id) {
            $this->session->set_flashdata('flash_message', 'Cập nhật thành công');
            header('Location: ' . base_url() . 'category/categories', TRUE, 301);
        } else {
            $data = array(
                "category" => $category,
                "title" => "Cập nhật thể loại",
                "content" => "admin/category",
                "categoriesParentBox" => $this->mCategory->getCategoriesParentBox(0, "", array($category->getParent()))
            );
            $this->session->set_flashdata('flash_error', 'Cập nhật thất bại');
            $this->load->view('admin/template/main', $data);
        }
    }
    
    public function searchCategoriesAjax() {
        $name = $this->input->post('name');
        if($name == '') {
            echo $this->mCategory->getCategoriesParentBox(0, "");
            return;
        }
        echo json_encode($this->mCategory->getCategories(array(), $name)['categories']);
    }
    
    public function getCategoriesAjax() {
        $cateIds = $this->input->post('cateIds');
        echo json_encode($this->mCategory->getCategoriesByIds($cateIds));
    }
}
