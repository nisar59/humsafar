@extends('layouts.template')
@section('title')
Regions
@endsection
@section('content')
<div class="page-title-box">
	<div class="row align-items-center">
		<div class="col-md-8">
			<h6 class="page-title">Regions</h6>
			<ol class="breadcrumb m-0">
				<li class="breadcrumb-item">{{Settings()->portal_name}}</li>
				<li class="breadcrumb-item">Regions</li>
				<li class="breadcrumb-item active">Import Regions</li>
			</ol>
		</div>
	</div>
</div>
<form action="{{url('regions/import-store')}}" method="post" enctype="multipart/form-data">
	@csrf
	<div class="row">
		<div class="col-12 col-md-12">
			<div class="card card-primary">
				<div class="card-header bg-white">
					<h4>Import Regions</h4>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="form-group col-md-12">
							<label>Select file</label>
							<input type="file" class="form-control" name="file">
							<a href="{{url('regions/sample-export')}}">Export sample</a>
						</div>
					</div>
				</div>
				<div class="card-footer text-end">
					<button class="btn btn-primary mr-1" type="submit">Submit</button>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection
@section('js')
@endsection