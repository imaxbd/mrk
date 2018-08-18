<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upcoming_tender extends CI_Controller {

	function __construct()
    {
        parent::__construct();
        // chekc authentication
        if(!is_logged_in()){
        	redirect('auth');
        }

        $this->load->model('MUpcoming_tender');
    }

    public function tlist(){
    	$tenders=$this->MUpcoming_tender->tlist();

    	$data["page_title"] = "Upcoming Tender List";
    	$data["main_content"] = $this->load->view('upc_tender/list',array('tenders'=>$tenders),true);
    	$this->load->view('master',$data);
    }

    public function create(){
    	$data["page_title"] = "Create New";
    	$data["main_content"] = $this->load->view('upc_tender/create','',true);
    	$this->load->view('master',$data);
    }

    public function save(){
    	if($this->input->post()){
    		$data['customer']=$this->input->post('customer',true);
    		$data['product']=$this->input->post('product',true);
    		$data['submission_date']=$this->input->post('submission_date',true);
    		$data['ernest_money']=$this->input->post('ernest_money',true);
    		$data['opening_date']=$this->input->post('opening_date',true);
    		$data['priority']=$this->input->post('priority',true);
    		$id=$this->MUpcoming_tender->save($data);

            if ($_FILES['attachments']['name']){
                    $config['file_name'] = $id;
                    $config['upload_path'] = './public/uploads/upct/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $this->upload->initialize($config);

                    if ( ! $this->upload->do_upload("attachments"))
                    {
                        $error = array('error' => $this->upload->display_errors());
                        print_r($error);
                        //die();
                    }
                    else
                    {
                        $filedata = $this->upload->data();
                        $attach_pic=$filedata['file_name'];
                        $this->MUpcoming_tender->update_pic($attach_pic,$id);
                    }

                    
                }

    		$this->session->set_flashdata('success', 'Successfully Added');
	        redirect('upcoming-tender', 'refresh');
    	}
    }

    public function edit(){
        if ($this->uri->segment(2) === FALSE)
        {
            if ($this->agent->is_referral())
            {
                echo $this->agent->referrer();
            }
        }
        else
        {
            $id=$this->uri->segment(2);
            $tender=$this->MUpcoming_tender->get_by_id($id);

            $data["page_title"] = "Edit Upcoming Tender";
            $data["main_content"] = $this->load->view('upc_tender/edit',array('tender'=>$tender),true);
            $this->load->view('master',$data);
        }
    }

    public function update(){
        if(!empty($this->input->post('id',true))){
            $id=$data['id']=$this->input->post('id',true);
            $data['customer']=$this->input->post('customer',true);
            $data['product']=$this->input->post('product',true);
            $data['submission_date']=$this->input->post('submission_date',true);
            $data['ernest_money']=$this->input->post('ernest_money',true);
            $data['opening_date']=$this->input->post('opening_date',true);
            $data['priority']=$this->input->post('priority',true);

            $this->MUpcoming_tender->update($data);

            if($this->input->post('delete_current')){
                $this->delete_attach_image($id);

            }

            if ($_FILES['attachments']['name']){
                    $config['file_name'] = $id;
                    $config['upload_path'] = './public/uploads/upct/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $this->upload->initialize($config);

                    if ( ! $this->upload->do_upload("attachments"))
                    {
                        $error = array('error' => $this->upload->display_errors());
                        print_r($error);
                        //die();
                    }
                    else
                    {
                        $filedata = $this->upload->data();
                        $attach_pic=$filedata['file_name'];
                        $this->MUpcoming_tender->update_pic($attach_pic,$id);
                    }

                    
                }

            $this->session->set_flashdata('success', 'Successfully Updated');
            redirect('upcoming-tender', 'refresh');
        }
    }

    public function remove(){
    	if ($this->uri->segment(2) === FALSE)
        {
            if ($this->agent->is_referral())
            {
                echo $this->agent->referrer();
            }
        }
        else
        {
            $id=$this->uri->segment(2);
            if($this->MUpcoming_tender->remove($id)){
    			$this->session->set_flashdata('success', 'Successfully deleted');
	        	redirect('upcoming-tender', 'refresh');
	    	}else{
	    		$this->session->set_flashdata('danger', 'Ops! something wrong');
	        	redirect('upcoming-tender', 'refresh');
	    	}
        }
    }

    public function delete_attach_image($id){
        $upc_data=$this->MUpcoming_tender->get_by_id($id);

        $filePath='./public/uploads/upct/'.$upc_data->attachments;
        if(file_exists($filePath)){
            if(!unlink($filePath)){
                
            }else{
                $this->MUpcoming_tender->update_pic('',$id);
            }
        }
    }
}