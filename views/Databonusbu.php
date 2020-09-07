<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Databonusbu extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function get_all($x)
    {
        $this->db->select('*, b.kode_transaksi as no_trans_ra, m.nama as nama_member, m.pin as pin_member');
        $this->db->from("header_transaksi b");
        $this->db->join("members m", "b.member_id=m.member_id");
        $this->db->group_by("b.member_id");
        $this->db->order_by("b.header_transaksi_id", "desc");
        return $this->db->get();
    }
      public function get_bonus_pertanggal($tanggal_awal,$tanggal_akhir)
    {
        return $this->db->query("SELECT *, sum(b.jumlah_bonus_bu) as total_bonus, m.nama as nama_member, m.pin as pin_member FROM bonus_bu b join members m on b.member_id = m.member_id WHERE b.create_at >= '$tanggal_awal' and b.create_at <= '$tanggal_akhir' GROUP BY b.member_id ORDER BY b.member_id ASC");
        
    }
    public function get_detail_bu($id)
    {

        $this->db->select('b.*,m.nama as nama_member, m.pin as pin_member,e.pin as pin_member_asal,');
        $this->db->from("bonus_bu b");
        $this->db->join("members m", "b.member_id=m.member_id");
        $this->db->join("members e", "b.member_asal=e.member_id");
        $this->db->where("b.member_id", $id);
        $this->db->order_by("b.nama_level", "asc");
        return $this->db->get();
    }
      // Tampil di laporan admin
    public function bonus_group($status)
    {
        $this->db->select('*,sum(b.jumlah_bonus_bu)as total_bonus');
        $this->db->from('bonus_bu b');
        $this->db->join("members m", "b.member_id=m.member_id");
        $this->db->where('b.status_bonus_bu', $status);
        $this->db->group_by('b.member_id');
        return $this->db->get();
    }
       public function get_children($id)
    {
        $this->db->select('member_id');
        $this->db->from('members');
        $this->db->where('parent_id', $id);
        return $this->db->get();
    }
    
      public function cek_data($member_id,$member_asal,$kode_transaksi)
    {
        $tahun = date('Y');
        $bulan = date('m');
        $this->db->select('*');
        $this->db->from('bonus_bu');
        $this->db->where('member_id', $member_id);
        $this->db->where('member_asal', $member_asal);
        $this->db->where('kode_transaksi', $kode_transaksi);
        $this->db->where('month(create_at)', $bulan); 
        $this->db->where('year(create_at)', $tahun);
        return $this->db->get();
    }
    public function cek($id)
    {
        $tahun = date('Y');
        $bulan = date('m');
        $this->db->select('a.header_transaksi_id,a.kode_transaksi,b.total_harga,a.tanggal_transaksi');
        $this->db->from('header_transaksi a');
        $this->db->join("transaksi b", "b.kode_transaksi=a.kode_transaksi");
        $this->db->where('a.member_id', $id);
        $this->db->where('month(a.tanggal_transaksi)', $bulan); 
        $this->db->where('year(a.tanggal_transaksi)', $tahun);
        return $this->db->get();
    }
     public function get_bank($id)
    {
        
        $this->db->select('no_rekening,nama_bank');
        $this->db->from('members');
        $this->db->where('member_id', $id);
        return $this->db->get();
    }
    public function get_member_perlevel($id,$level)
    {
        $tahun = date('Y');
        $bulan = date('m');
        $this->db->select('member_asal');
        $this->db->from('bonus_bu');
        $this->db->where('member_id', $id);
        $this->db->where('nama_level', $level);
        $this->db->where('month(create_at)', $bulan); 
        $this->db->where('year(create_at)', $tahun);
        return $this->db->get();
    }
        public function get_total_bonus($id)
    {
        $tahun = date('Y');
        $bulan = date('m');
        $this->db->select('sum(jumlah_bonus_bu) as total_bonus');
        $this->db->from('bonus_bu');
        $this->db->where('member_id', $id);
        $this->db->where('month(create_at)', $bulan); 
        $this->db->where('year(create_at)', $tahun);
        return $this->db->get();
    }
       public function insert($record)
    {
        return $this->db->insert('bonus_bu', $record);
    }
        public function update($where, $data)
    {
        $this->db->where($where);
        $this->db->update('bonus_bu', $data);
    }

}
