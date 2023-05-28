@extends('layouts.app')
@section('title')
Screen Locked
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
                        <h4 class="font-size-18 mt-5 text-center">Locked</h4>
                        <p class="text-muted text-center">Hello {{$user->name}}, enter your password to unlock the screen!</p>
                        <form class="mt-4" method="POST" action="{{ route('login') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{$user->email}}">
                            <div class="pt-3 text-center">
                                <img src="{{asset('assets/images/users/user-6.jpg')}}" class="rounded-circle img-thumbnail avatar-lg" alt="thumbnail">
                                <h6 class="font-size-16 mt-3">{{$user->name}}</h6>
                            </div>
                            <div class="mb-3">
                                <label for="userpassword">Password</label>
                                <input type="password" name="password" class="form-control" id="userpassword" placeholder="Enter password">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-12 text-end">
                                    <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Unlock</button>
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