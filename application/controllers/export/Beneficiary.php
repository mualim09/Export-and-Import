<?php

    Class Beneficiary extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            if(!$this->session->userdata('logged_in')) redirect('/');
            $this->load->model(['M_CRUD_Exp']);
        }

        public function index()
        {
            $datas['css'] = [
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css"),
            ];
            $datas['js'] = [
                base_url("assets/adminlte/plugins/datatables/jquery.dataTables.min.js"),
                base_url("assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"),
                base_url("assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"),
                base_url("assets/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/sweetalert/sweetalert.min.js"),
                base_url("assets/js/export/beneficiary/list.js"),
            ];
            $datas['title'] = 'Export - Beneficiary';
            $datas['breadcrumb'] = ['Export', 'Master', 'Beneficiary'];
            $datas['header'] = 'Beneficiary list';
            $datas['params'] = [
                'list' => $this->M_CRUD_Exp->readData('view_master_beneficiary')
            ];

            $this->template->load('default', 'contents' , 'export/beneficiary/list', $datas);
        }

        public function add()
        {
            $datas['css'] = [
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2/css/select2.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"),
            ];

            $datas['js'] = [
                base_url("assets/adminlte/plugins/select2/js/select2.full.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/jquery.validate.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/additional-methods.min.js"),
                base_url("assets/adminlte/plugins/sweetalert/sweetalert.min.js"),
                base_url("assets/js/export/beneficiary/add.js"),
            ];
            $datas['title'] = 'Export - Beneficiary';
            $datas['breadcrumb'] = ['Export', 'Master', 'Beneficiary'];
            $datas['header'] = 'Add record';
            $datas['params'] = [
                'country' => $this->M_CRUD_Exp->readData('master_country', ['is_deleted' => '0']),
            ];

            $this->template->load('default', 'contents' , 'export/beneficiary/add', $datas);
        }

        public function save()
        {
            $post = $this->input->post();
            $param = [
                'name' => $post['company'],
                'office' => $post['office'],
                'address' => $post['address'],
                'country_id' => $post['country'],
                'contact_person' => $post['cp'],
                'phone_no' => $post['phone'],
                'created_by' => $this->session->userdata('logged_in')->id,
            ];

            if($this->M_CRUD_Exp->insertData('master_beneficiary', $param)) {
                $response = ['status' => 1, 'messages' => 'Beneficiary has been saved successfully.', 'icon' => 'success', 'url' => 'export/master/beneficiary'];
            } else {
                $response = ['status' => 0, 'messages' => 'Beneficiary has failed to save.', 'icon' => 'error'];
            }

            echo json_encode($response);
        }

        public function detail($id)
        {
            $datas['css'] = [
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2/css/select2.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"),
            ];

            $datas['js'] = [
                base_url("assets/adminlte/plugins/select2/js/select2.full.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/jquery.validate.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/additional-methods.min.js"),
                base_url("assets/adminlte/plugins/sweetalert/sweetalert.min.js"),
                base_url("assets/js/export/beneficiary/detail.js"),
            ];
            $datas['title'] = 'Export - Beneficiary';
            $datas['breadcrumb'] = ['Export', 'Master', 'Beneficiary'];
            $datas['header'] = 'Detail record';
            $datas['params'] = [
                'detail' => $this->M_CRUD_Exp->readDatabyID('master_beneficiary', ['is_deleted' => '0', 'id' => $id]),
                'country' => $this->M_CRUD_Exp->readData('master_country', ['is_deleted' => '0']),
            ];
            
            $this->template->load('default', 'contents' , 'export/beneficiary/detail', $datas);
        }

        public function update()
        {
            $post = $this->input->post();
            $condition = ['id' => $post['id']];
            $param = [
                'name' => $post['company'],
                'office' => $post['office'],
                'address' => $post['address'],
                'country_id' => $post['country'],
                'contact_person' => $post['cp'],
                'phone_no' => $post['phone'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('logged_in')->id,
            ];

            if($this->M_CRUD_Exp->updateData('master_beneficiary', $param, $condition)) {
                $response = ['status' => 1, 'messages' => 'Beneficiary has been updated successfully.', 'icon' => 'success', 'url' => 'export/master/beneficiary'];
            } else {
                $response = ['status' => 0, 'messages' => 'Beneficiary has failed to update.', 'icon' => 'error'];
            }
            
            echo json_encode($response);
        }

        public function delete($id)
        {
            $condition = ['id' => $id];
            $beneficiary = $this->M_CRUD_Exp->readDatabyID('master_beneficiary', ['id' => $id]);
            $status = ($beneficiary->is_deleted == '1'?'0':'1');
            
            if($this->M_CRUD_Exp->updateData('master_beneficiary', ['is_deleted' => $status], $condition)) {
                $response = ['status' => 1, 'messages' => 'Beneficiary has been deleted successfully.', 'icon' => 'success', 'url' => 'export/master/beneficiary'];
            } else {
                $response = ['status' => 0, 'messages' => 'Beneficiary has failed to delete.', 'icon' => 'error'];
            }

            echo json_encode($response);
        }
    }

?>