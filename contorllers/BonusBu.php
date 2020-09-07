<?php defined('BASEPATH') or exit('No direct script access allowed');

class Bonus_bu extends CI_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('dataMember');
        $this->load->model('dataBonusBu');
        $this->load->model('dataMasterBu');
        $this->load->library('encryption');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    }


    public function index()
    {
        $data['title'] = "Bonus Belanja Ulang";
        $this->template->load('member/template_member', 'member/bonus_bu', $data);
    }

       public function generate($member_id,$id1,$persen,$nama_level)
    {
       
       $children = $this->dataBonusBu->get_children($id1);
       foreach ($children->result_array() as $key) {
       	$id2 = $key['member_id'];
       	$cek = $this->dataBonusBu->cek($id2);
       	$data = $cek->num_rows();
       	if ($data==0) {
       		$this->generate($member_id,$id2,$persen,$nama_level); 
       	}else{
       		echo $id2;

       		foreach ($cek->result_array() as $key1) {
       			$total = $key1['total_harga'];
                $kode_transaksi = $key1['kode_transaksi'];
                $tanggal_transaksi = $key1['tanggal_transaksi'];
                $bonus1 = $total * $persen / 100;
                $cekdata = $this->dataBonusBu->cek_data($member_id,$id2,$kode_transaksi);
                $hasilcek = $cekdata->num_rows();
            

            
            if ($hasilcek == 0) {

                $level = array(
                'member_asal' => $id2,
                'kode_transaksi' => $kode_transaksi,
                'tanggal_transaksi' => $tanggal_transaksi,
                'member_id' => $member_id,
                'persen_bonus_bu' => $persen,
                'jumlah_bonus_bu' => $bonus1,
                'status_bonus_bu' => "publish",
                'nama_level'=> $nama_level
            );
                    
            $data = $this->dataBonusBu->insert($level);

            }else {
                echo "";
                  }
       		}
        	}
          }
    }

     public function generate_all()
        {
         $bu = $this->dataBonusBu->get_all('publish');
         foreach ($bu->result_array() as $key ) {
             $id=$key['member_id'];
             $this->bonus($id);
         }
        }
    
     public function bonus($id)
        {


        $m = $this->dataMasterBu->get_master()->row();

        $sponsor1 =$id;

        $this->generate($sponsor1,$sponsor1,$m->bonus_level_1,1);

        #level 2
        $get_level1 = $this->dataBonusBu->get_member_perlevel($sponsor1,1);
        foreach ($get_level1->result_array() as $key) {
        	$id_level1 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level1,$m->bonus_level_2,2);
        }

        #level 3
        $get_level2 = $this->dataBonusBu->get_member_perlevel($sponsor1,2);
        foreach ($get_level2->result_array() as $key) {
        	$id_level2 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level2,$m->bonus_level_3,3);
        }

         #level 4
        $get_level3 = $this->dataBonusBu->get_member_perlevel($sponsor1,3);
        foreach ($get_level3->result_array() as $key) {
        	$id_level3 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level3,$m->bonus_level_4,4);
        }

         #level 5
        $get_level4 = $this->dataBonusBu->get_member_perlevel($sponsor1,4);
        foreach ($get_level4->result_array() as $key) {
        	$id_level4 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level4,$m->bonus_level_5,5);
        }

         #level 6
        $get_level5 = $this->dataBonusBu->get_member_perlevel($sponsor1,5);
        foreach ($get_level5->result_array() as $key) {
        	$id_level5 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level5,$m->bonus_level_6,6);
        }

         #level 7
        $get_level6 = $this->dataBonusBu->get_member_perlevel($sponsor1,6);
        foreach ($get_level6->result_array() as $key) {
        	$id_level6 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level6,$m->bonus_level_7,7);
        }

         #level 8
        $get_level7 = $this->dataBonusBu->get_member_perlevel($sponsor1,7);
        foreach ($get_level7->result_array() as $key) {
        	$id_level7 = $key['member_asal'];
        	$this->generate($sponsor1,$id_level7,$m->bonus_level_8,8);
        }

        $n = $this->dataBonusBu->get_total_bonus($id)->row();
        $data['total_bonus'] = $n->total_bonus;
        echo json_encode($data);
        }

     public function data()
        {
            if ($this->session->userdata('logged_in_user') == FALSE) {
                reject();
            }

            $data['title'] = "Bonus Beli Ulang ";
            $this->template->load('admin/layout_admin', 'admin/laporan_bu/laporan_bu', $data);
        }

    public function laporan_bu()
        {
            if ($this->session->userdata('logged_in_user') == FALSE) {
                reject();
            }

            $tanggal_awal = date('Y-m-d',strtotime($this->input->post('tanggal_awal')));
            $tanggal_akhir = date('Y-m-d',strtotime($this->input->post('tanggal_akhir')));
            $data['title'] = "Bonus Beli Ulang ";
             $data['url'] = base_url('bonus_bu/api_detail/'.$tanggal_awal.'/'.$tanggal_akhir);
            $this->load->view('admin/laporan_bu/list_bonus_bu', $data);
        }

    public function api_detail($tanggal_awal,$tanggal_akhir)
        {

            $record = $this->dataBonusBu->get_bonus_pertanggal($tanggal_awal,$tanggal_akhir)->result();
            $no = 1;
            foreach ($record as $d) {
                $tbody = array();
                $tbody[] = $no++;
                $tbody[] = $d->nama_member;
                $tbody[] = $d->pin_member;
                $tbody[] = rupiah($d->total_bonus);
                $tbody[] = '<button data-id=' . $d->member_id . ' data-toggle="modal" class="detail-data btn btn-info"><i class="la la-info"></i> Detail</a>';
                $data[] = $tbody;
            }

            if ($record) {
                echo json_encode(array('data' => $data));
            } else {
                echo json_encode(array('data' => 0));
            }
        }

    public function detail()
        {
             if ($this->session->userdata('logged_in_user') == FALSE) {
                reject();
            }

            $id = $this->input->post('id');

            $data['title'] = "Detail Bonus Beli Ulang ";
            $data['url'] = base_url('bonus_bu/api_detail_bu/'.$id);
            //$data['bonus_bu'] = $this->dataBonusBu->get_bonus_pertanggal('publish',$tanggal_awal,$tanggal_akhir)->result();
            $this->load->view('admin/laporan_bu/list_detail_bonus_bu', $data);
        }

    public function api_detail_bu($id)
        {
           
            $record = $this->dataBonusBu->get_detail_bu($id)->result();
            $no = 1;
            foreach ($record as $d) {
                $tbody = array();
                $tbody[] = $no++;
                $tbody[] = $d->kode_transaksi;
                $tbody[] = tanggal($d->tanggal_transaksi);
                $tbody[] = $d->nama_member;
                $tbody[] = $d->pin_member;
                //$tbody[] = $d->pin_sponsor_asal;
                $tbody[] = $d->pin_member_asal;
                $tbody[] = 'Level '.$d->nama_level;
                $tbody[] = $d->persen_bonus_bu . ' %';
                $tbody[] = rupiah($d->jumlah_bonus_bu);
                $data[] = $tbody;
            }

            if ($record) {
                echo json_encode(array('data' => $data));
            } else {
                echo json_encode(array('data' => 0));
            }
        }
     public function transfer()
        {
            $id = de($this->input->post('id'));
            $record = array(
                'status_bonus_bu' => 'sent'
            );
            $where = array(
                'member_id' => $id,
                'status_bonus_bu' => 'publish'
            );
            $data = $this->dataBonusBu->update($where, $record);
            echo json_encode($data);
        }


    public function belum_transfer()
        {
            if ($this->session->userdata('logged_in_user') == FALSE) {
                reject();
            }

            $data['title'] = "Bonus BU Belum Transfer";
            $data['url'] = base_url('bonus_bu/api_belum_transfer');
            $data['bonus_ra'] = $this->dataBonusBu->get_all('publish')->result();
            $this->template->load('admin/layout_admin', 'admin/bu_belum_transfer', $data);
        }

    public function api_belum_transfer()
        {
            $record = $this->dataBonusBu->bonus_group('publish')->result();
            $no = 1;
            foreach ($record as $d) {
                $tbody = array();
                $tbody[] = $no++;
                $tbody[] = $d->nama;
                $tbody[] = $d->pin;
                $tbody[] = $d->no_rekening;
                $tbody[] = $d->nama_bank;
                $tbody[] = rupiah($d->total_bonus);
                $tbody[] = '<button data-id=' . en($d->member_id) . ' data-toggle="modal" class="transfer-data btn btn-success"><i class="la la-check"></i> Transfer</a>';
                $data[] = $tbody;
            }

            if ($record) {
                echo json_encode(array('data' => $data));
            } else {
                echo json_encode(array('data' => 0));
            }
        }

}

/* End of file Controllername.php */
