<?php

    Class Packing extends CI_Controller
    {
        public function __construct()
        {
            parent::__construct();
            if(!$this->session->userdata('logged_in')) redirect('/');
            $this->load->model(['M_CRUD']);
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
                base_url("assets/js/packing/list.js"),
            ];
            $datas['title'] = 'Export - Packing';
            $datas['breadcrumb'] = ['Export', 'Transaction', 'Packing'];
            $datas['header'] = 'Packing';
            $datas['params'] = [
                'list' => $this->M_CRUD->readData('view_trans_packing_list')
            ];

            $this->template->load('default', 'contents' , 'export/packing/list', $datas);
        }

        public function add()
        {
            $datas['css'] = [
                "text/css,stylesheet, https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css",
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2/css/select2.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css"),
            ];

            $datas['js'] = [
                "https://cdn.jsdelivr.net/npm/flatpickr",
                base_url("assets/adminlte/plugins/select2/js/select2.full.min.js"),
                base_url("assets/adminlte/plugins/datatables/jquery.dataTables.min.js"),
                base_url("assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"),
                base_url("assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/jquery.validate.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/additional-methods.min.js"),
                base_url("assets/adminlte/plugins/sweetalert/sweetalert.min.js"),
                base_url("assets/js/packing/add.js"),
            ];
            $datas['title'] = 'Export - Packing';
            $datas['breadcrumb'] = ['Export', 'Transaction', 'Packing'];
            $datas['header'] = 'Add record';
            $datas['params'] = [
                'autonumber' => $this->M_CRUD->autoNumberPacking('trans_packing_list', 'code', '/SKP-EXT/PL/'.date('m/Y'), 4),
                'invoice' => $this->M_CRUD->readData('view_trans_pi_packing'),
                'country' => $this->M_CRUD->readData('master_country', ['is_deleted' => '0']),
                'loading' => $this->M_CRUD->readData('master_loading_port', ['is_deleted' => '0']),
            ];

            $this->template->load('default', 'contents' , 'export/packing/add/index', $datas);
        }

        public function data($id = NULl)
        {
            $data = $this->M_CRUD->readDatabyID('view_trans_packing_get', ['is_deleted' => '0', 'id' => $id]);
            echo json_encode($data);
        }

        public function item($id = NULL)
        {
            $data = $this->M_CRUD->readData('view_trans_packing_item', ['is_deleted' => '0', 'id' => $id]);
            echo json_encode($data);
        }

        public function item_detail($id = NULL)
        {
            $data = $this->M_CRUD->readDatabyID('view_trans_packing_item_get', ['is_deleted' => '0', 'pi_detail_id' => $id]);
            echo json_encode($data);
        }

        public function batch($id = NULL)
        {
            $data = $this->M_CRUD->readData('view_trans_packing_batch', ['pi_detail_id' => $id]);
            echo json_encode($data);
        }

        public function date($id = NULL)
        {
            $data = $this->M_CRUD->readDatabyID('trans_qcontrol_check', ['id' => $id]);
            echo json_encode($data);
        }

        public function save()
        {
            $post = $this->input->post();
            $packing = $this->M_CRUD->readDatabyID('trans_packing_list', ['code' => $post['code']]);

            if($packing) {
                $response = ['status' => 0, 'messages' => 'Packing list code already exist.', 'icon' => 'error'];
            } else {
                $params = [
                    'code' => $post['code'],
                    'invoice_id' => $post['invoice'],
                    'dates' => $post['pack_date'],
                    // 'container' => $post['container'],
                    'created_by' => $this->session->userdata('logged_in')->id,
                ];
                $header = $this->M_CRUD->insertData('trans_packing_list', $params);
    
                if($header) {
                    $paramsFilter = [
                        'packing_list_id' => $header,
                    ];
                    $this->M_CRUD->insertData('trans_packing_inv_filter', $paramsFilter);
                    $Grid = array();
                
                    foreach($_POST as $index => $value){
                        if(preg_match("/^grid_/i", $index)) {
                            $index = preg_replace("/^grid_/i","",$index);
                            $arr = explode('_',$index);
                            $rnd = $arr[count($arr)-1];
                            array_pop($arr);
                            $idx = implode('_',$arr);
                            
                            $Grid[$rnd][$idx] = $value;
                            if(!isset($Grid[$rnd]['id'])){
                                $Grid[$rnd]['id'] = $rnd;
                            }
                        }
                    }
    
                    if(!empty($Grid)) {
                        foreach($Grid as $detail) {
                            $paramDetail = [
                                'packing_list_id' => $header,
                                'pi_detail_id' => $detail['pi_detail_id'],
                                'qty' => $detail['qty'],
                                'carton_barcode' => $detail['carton'],
                                'qcontrol_check_id' => $detail['batch'],
                            ];
                            $this->M_CRUD->insertData('trans_packing_list_detail', $paramDetail);
                        }
                    }
                    $response = ['status' => 1, 'messages' => 'Packing has been saved successfully.', 'icon' => 'success', 'url' => 'export/packing'];
                } else {
                    $response = ['status' => 0, 'messages' => 'Packing check has failed to save.', 'icon' => 'error'];
                }
            }

            echo json_encode($response);
        }

        public function detail($id)
        {
            $datas['css'] = [
                "text/css,stylesheet, https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css",
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2/css/select2.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css"),
                "text/css,stylesheet,".base_url("assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css"),
            ];

            $datas['js'] = [
                "https://cdn.jsdelivr.net/npm/flatpickr",
                base_url("assets/adminlte/plugins/select2/js/select2.full.min.js"),
                base_url("assets/adminlte/plugins/datatables/jquery.dataTables.min.js"),
                base_url("assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"),
                base_url("assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/jquery.validate.min.js"),
                base_url("assets/adminlte/plugins/jquery-validation/additional-methods.min.js"),
                base_url("assets/adminlte/plugins/sweetalert/sweetalert.min.js"),
                base_url("assets/js/packing/detail.js"),
            ];
            $datas['title'] = 'Export - Packing';
            $datas['breadcrumb'] = ['Export', 'Transaction', 'Packing'];
            $datas['header'] = 'Detail record';
            $datas['params'] = [
                'detail' => $this->M_CRUD->readDatabyID('view_trans_packing_detail', ['is_deleted' => '0', 'id' => $id]),
                'list' => $this->M_CRUD->readData('view_trans_packing_detail_list', ['packing_list_id' => $id]),
                'item' => $this->M_CRUD->readData('view_trans_packing_detail_item', ['packing_list_id' => $id]),
            ];
            
            $datas['chained'] = [
                'qty' => $this->M_CRUD->readData('view_trans_packing_detail_qty', ['pi_id' => $datas['params']['detail']->pi_id]),
            ];

            $this->template->load('default', 'contents' , 'export/packing/detail/index', $datas);
        }

        public function update()
        {
            $post = $this->input->post();
            $Grid = array();
			
            foreach($_POST as $index => $value){
                if(preg_match("/^grid_/i", $index)) {
                    $index = preg_replace("/^grid_/i","",$index);
                    $arr = explode('_',$index);
                    $rnd = $arr[count($arr)-1];
                    array_pop($arr);
                    $idx = implode('_',$arr);
                    
                    $Grid[$rnd][$idx] = $value;
                    if(!isset($Grid[$rnd]['id'])){
                        $Grid[$rnd]['id'] = $rnd;
                    }
                }
            }

            $params = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->session->userdata('logged_in')->id,
            ];
            $header = $this->M_CRUD->updateData('trans_packing_list', $params, ['id' => $post['id']]);

            if($header) {
                $pi = $this->M_CRUD->readDatabyID('view_trans_packing_list_pi', ['packing_id' => $post['id']]);
                $paramsPI = ['number_of_container' => $post['container']];
                $this->M_CRUD->updateData('trans_pi', $paramsPI, ['id' => $pi->id]);

                if(!empty($Grid)) {
                    foreach($Grid as $detail) {
                        $paramDetail = [
                            'packing_list_id' => $post['id'],
                            'pi_detail_id' => $detail['pi_detail_id'],
                            'qty' => $detail['qty'],
                            'carton_barcode' => $detail['carton'],
                            'qcontrol_check_id' => $detail['batch'],
                            // 'expired_date' => $detail['expdate'],
                            // 'production_date' => $detail['proddate'],
                        ];
                        $this->M_CRUD->insertData('trans_packing_list_detail', $paramDetail);
                    }
                } 
                $response = ['status' => 1, 'messages' => 'Packing has been updated successfully.', 'icon' => 'success', 'url' => 'export/packing'];
            } else {
                $response = ['status' => 0, 'messages' => 'Packing check has failed to update.', 'icon' => 'error'];
            }

            echo json_encode($response);
        }

        public function filter($id)
        {
            $post = $this->input->post();
            $params = [
                $post['fields'] => $post['filter'],
            ];
            if($this->M_CRUD->updateData('trans_packing_inv_filter', $params, ['packing_list_id' => $id])) {
                $response = ['status' => 1, 'messages' => 'Filter packing list has been updated successfully.', 'icon' => 'success'];
            } else {
                $response = ['status' => 0, 'messages' => 'Packing check has failed to update.', 'icon' => 'error'];
            }

            echo json_encode($response);
        }

        public function delete_item($id)
        {
            $condition = [
                'id' => $id
            ];
            
            if($this->M_CRUD->deleteData('trans_packing_list_detail', $condition)) {
                $response = ['status' => 1, 'messages' => 'Item has been deleted successfully.'];
            } else {
                $response = ['status' => 0, 'messages' => 'Item has failed to delete.'];
            }

            echo json_encode($response);
        }
    }

?>