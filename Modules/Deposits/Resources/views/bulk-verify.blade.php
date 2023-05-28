<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Deposits Verification</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <form method="post" action="{{url('deposits/bulk-verification')}}" enctype="multipart/form-data"> 
        @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <label>Select a file</label>
            <input type="file" class="form-control" name="file">
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