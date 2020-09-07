<!-- END: Subheader -->
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Daftar <?php echo $title ?>
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
              
       <ul class="m-portlet__nav">
            <li class="m-portlet__nav-item">
               <button class=" btn btn-sm btn-block btn-info  m-btn--pill m-btn--custom m-btn--icon m-btn--air"  onclick='generete(this)'><i class="la la-download"></i>Generete</button>
            </li>
          </ul>
   
            </div>
        </div>
        <div class="m-portlet__body">
       
            <!--begin: Datatable -->
            <table class="data_table table table-striped- table-bordered table-hover table-checkable" id="">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>PIN</th>
                        <th>No Rekening</th>
                        <th>Nama Bank</th>
                       <th>Total Bonus</th> 
                        <th>Opsi Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var mydata = $('.data_table').DataTable({
            "processing": true,
            "ajax": "<?php echo $url ?>",
            stateSave: true,
            "order": []
        })

        $('.data_table').on('click', '.transfer-data', function() {

            Swal.fire({
                title: 'Ubah?',
                text: "Anda yakin mengubah status menjadi transfer?",
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Status Transfer',
                confirmButtonColor: '#ff5e5e',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?php echo base_url('bonus_bu/transfer') ?>",
                        method: "post",
                        data: {
                            id: $(this).data('id')
                        },
                        success: function(data) {
                            // Toast sukses
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })
                            Toast.fire({
                                type: 'success',
                                title: 'Diubah Status Transfer'
                            })
                            mydata.ajax.reload(null, false)
                        }
                    })
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    // Toast sukses
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        onOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })
                    Toast.fire({
                        type: 'error',
                        title: 'Eksekusi Dibatalkan'
                    })
                }
            })
        });
    });

    function generete(e){
    
      // var id = $(e).data("id");
      swal({
          title: 'Proses Generete data dilakun sekali dalam sebulan Apakah anda yakin ingin generete semua data ?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: '#3085d6',
          confirmButtonText: "Ya, Generete Data!",
          cancelButtonText: "Tidak",
          // buttonsStyling: true
      }).then((hasil) =>  {
        if (hasil.value) {
          $.ajax({
              type: "POST",
              url: "<?php echo site_url('bonus_bu/generate_all')?>",
              cache: false,
              success: function(response) {
                // refreshTable();
                swal(
                  'Berhasil!',
                  'Semua data berhasil digenerete!',
                   'success'
                )
                 location.reload();
              }
          });
        }   
          
      });
    }

</script>   