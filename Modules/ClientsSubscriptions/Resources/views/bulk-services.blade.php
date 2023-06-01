<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Subscription & Services</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <form method="post" action="{{url('clients-subscriptions/bulk-services')}}" enctype="multipart/form-data"> 
        @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col-11">
            <label>Select a file</label>
            <input type="file" class="form-control mb-2" name="file">
          </div>
          <div class="col-1">
            <label class="label">Sample</label>            
            <a class="btn btn-success" href="{{url('export-sample/subscriptions-sample')}}"><i class="fa fa-download"></i></a>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary">Submit</button>
      </div>
    </form>
    </div>
  </div>
</div>