@extends('layouts.app')
@section('title')
Login
@endsection
@section('content')
<div class="wrapper-page account-page-full">
  <div class="card shadow-none">
    <div class="card-block">
      <div class="account-box">
        <div class="card-box shadow-none p-4">
          <div class="p-2">
            <div class="text-center mt-4">
              <a href="{{url()->current()}}"><img width="100" src="{{url('public/img/settings/'.Settings()->portal_favicon)}}" alt="logo"></a>
            </div>
            <h4 class="font-size-18 mt-5 text-center">Welcome Back !</h4>
            <p class="text-muted text-center">Sign in to continue to {{Settings()->portal_name}}.</p>
            <form class="mt-4" method="POST" action="{{ route('login') }}" >
              @csrf
              <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Enter Email" autofocus  value="{{ old('email') }}">
                @error('email')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              
              <div class="mb-3">
                <label class="form-label" for="userpassword">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="userpassword" placeholder="Enter password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              
              <div class="mb-3 row">
                <div class="col-sm-6">
                  <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="customControlInline">
                    <label class="form-check-label" for="customControlInline">Remember me</label>
                  </div>
                </div>
                <div class="col-sm-6 text-end">
                  <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log In</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script>
$(document).ready(function() {
setInterval(function() {
location.reload();
}, 30000);
});
</script>
@endsection