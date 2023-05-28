 <script type="text/javascript">
   var token="{{csrf_token()}}";
   var user_id="{{UserDetail(Auth::id())->lock_screen_token}}";
 </script>
<script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
<script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>


<!-- Peity chart-->
<!-- <script src="{{asset('assets/libs/peity/jquery.peity.min.js')}}"></script>
 -->
<!-- Plugin Js-->
<!-- <script src="{{asset('assets/libs/chartist/chartist.min.js')}}"></script>
<script src="{{asset('assets/libs/chartist-plugin-tooltips/chartist-plugin-tooltip.min.js')}}"></script> -->

<!-- <script src="{{asset('assets/js/pages/dashboard.init.js')}}"></script>
 -->
<script src="{{asset('assets/js/app.js')}}"></script>
<script src="{{asset('assets/izitoast/js/iziToast.min.js')}}"></script>





<!-- Required datatable js -->
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<!-- Buttons examples -->
<script src="{{asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/jszip/jszip.min.js')}}"></script>
<script src="{{asset('assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- Responsive examples -->
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script src="{{asset('assets/libs/select2/js/select2.min.js')}}"></script>

<script src="{{asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>


<script src="{{asset('assets/libs/chart.js/Chart.bundle.min.js')}}"></script>
<script src="{{asset('assets/libs/jquery-ui/jquery-ui.min.js')}}"></script>

<script src="{{asset('assets/context-menu/jquery.contextMenu.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/context-menu/jquery.ui.position.min.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/js/functions.js')}}"></script>






<script type="text/javascript">

  $(document).ready(function(){


    
  setInterval(function() {
      $.ajax({
          url:"{{url('check-auth')}}",
          success:function(res){
            console.log(res);
            if(!res){
              window.location="{{url('lock-screen')}}?id="+user_id;
            }
          },
          error:function() {
            location.reload();
          }
      })

  }, 101000);



  $(".select2").select2({
    width:"100%"
  });

$(function () {
  $('[data-toggle="popover"]').popover()
});



  $(document).on('click', '.img', function(){

    var img_src=$(this).attr('src');
    var src_html='<img src="'+img_src+'" width="100%">';
  Swal.fire({
    html:src_html,
    width: '50%'
  });
  });

$(document).on('click', '#maintenance',function (e) {

      if($(this).is(':checked')){
            Swal.fire({
              title: 'Are you sure you want to up the Application',
              showDenyButton: true,
              confirmButtonText: 'Yes',
              icon: 'question',
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href="{{url('artisan/up')}}";
                $(this).prop("checked", true);
              }else{
                $(this).prop("checked", false);
              }
          });

      }
      else {
            Swal.fire({
              title: 'Are you sure you want to down the Application',
              showDenyButton: true,
              confirmButtonText: 'Yes',
              icon: 'question',
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href="{{url('artisan/down')}}";
                $(this).prop("checked", false);
              }else{
                $(this).prop("checked", true);
              }
          });

      }
  
});

  $(document).on('click', '.verify-prompt', function(e) {
              e.preventDefault();
              Swal.fire({
                title: $(this).data('prompt-msg'),
                showDenyButton: true,
                confirmButtonText: 'Yes',
                icon: 'question',
              }).then((result) => {
                if(result.isConfirmed){
                window.location.href=$(this).attr('href');     
                }
            });

  });


       $.contextMenu({
            selector: 'body',
            autoHide: true,
            zIndex: 11111,
            callback: function(key, opt){ 
                url_link=opt.commands[key].url_link;
                window.location.href=url_link;
            },
            items: {
                dashboard: {name: "Dashboard", url_link:'{{url("/")}}', icon: 'fas fa-home',},
                refresh: {name: "Refresh", url_link:'{{url()->current()}}', icon: 'fas fa-redo',},
                "sep1": "---------",

                @can('users.view')
                users: {name: "Users", url_link:'{{url("users")}}', icon: 'fas fa-users',},
                @endcan

                @can('roles.view')
                roles: {name: "Roles", url_link:'{{url("roles")}}', icon: 'fas fa-user-lock',},
                "sep2": "---------",
                @endcan


                @can('desks.view')
                desk: {name: "Desks", url_link:'{{url("desks")}}', icon: 'fas fa-tv',},
                "sep3": "---------",
                @endcan

                @can('clients.view')
                clients: {name: "Clients", url_link:'{{url("clients")}}', icon: 'fas fa-hospital-user',},
                @endcan

                @can('clients-subscriptions.view')
                clientssubscriptions: {name: "Clients Subscriptions", url_link:'{{url("clients-subscriptions")}}', icon: 'fas fa-hands-helping',},
                "sep4": "---------",
                @endcan

                @can('deposits.view')
                deposits: {name: "Deposits", url_link:'{{url("deposits")}}', icon: 'fas fa-briefcase',},
                "sep5": "---------",
                @endcan


                @can('feedback.view')
                feedback: {name: "Feedback", url_link:'{{url("feedback")}}', icon: 'fas fa-comment-medical',},
                @endcan
                @can('feedback-questions.view')
                feedbackquestions: {name: "Feedback Question", url_link:'{{url("feedback-questions")}}', icon: 'fas fa-question-circle',},
                "sep6": "---------",
                @endcan


                @can('settings.view')
                settings: {name: "Settings", url_link:'{{url("settings")}}', icon: 'fas fa-cog',}
                @endcan
            }
        });
 

$(".search-bar").on('change', function(){
window.location=$(this).val();
});

});
</script>