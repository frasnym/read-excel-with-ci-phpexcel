<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StartHere extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

	public function index()
	{
		$this->load->view('start_here');
    }
    
    public function import_excel()
    {
        $filename = "Uploaded_File";
        $this->load->library('upload'); // Load librari upload

        $config['upload_path'] = 'uploads/excel/';
        $config['allowed_types'] = 'xlsx';
        $config['max_size']  = '2048';
        $config['overwrite'] = true;
        $config['file_name'] = $filename;

        $this->upload->initialize($config); // Load konfigurasi uploadnya
        if ($this->upload->do_upload('userfile')) { // Lakukan upload dan Cek jika proses upload berhasil
            // Load plugin PHPExcel nya
            // var_dump(extension_loaded('zip'));
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

            $excelreader = new PHPExcel_Reader_Excel2007();
            $loadexcel = $excelreader->load('uploads/excel/'.$filename.'.xlsx'); // Load file yang telah diupload ke folder excel
            $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

            // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
            $data = array();

            $numrow = 1;
            foreach ($sheet as $row) {
                // Cek $numrow apakah lebih dari 1
                // Artinya karena baris pertama adalah nama-nama kolom
                // Jadi dilewat saja, tidak usah diimport
                if ($numrow > 1) {
                    // Kita push (add) array data ke variabel data
                    if ($row['A']) {
                        array_push($data, array(
                            'text_number' => $row['A'],
                            'text_alphabet' => $row['B'],
                        ));
                    } else continue;
                }

                $numrow++; // Tambah 1 setiap kali looping
            }

            $output = array('status' => 'SUCCESS', 'message' => json_encode($data));
        } else {
            $output = array('status' => 'FAILED', 'message' => "Err Upload: ". $this->upload->display_errors());
        }

        echo json_encode($output);
    }
}
