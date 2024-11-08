@extends('layouts.superadmin.app')
@section('content')

<main id="main" class="main">

<section class="section dashboard">

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('superadmin-change-password') }}">
    @csrf
    <hr>
    <div class="form-group mb-4">
        <div class="password-container position-relative">
            <input id="current_password" type="password" name="current_password" class="form-control" placeholder="Current Password">
            <i class="toggle-password bi bi-eye eye-toggle"></i>
        </div>
            @if($errors->has('current_password'))
                <p class="text-danger">{{ $errors->first('current_password') }}</p>
            @endif
    </div>

    <div class="form-group mb-4">
        <div class="password-container position-relative">
            <input id="new_password" type="password" name="new_password" class="form-control" placeholder="New Password">
            <i class="toggle-password1 bi bi-eye eye-toggle"></i>
        </div>
        @if($errors->has('new_password'))
            <p class="text-danger">{{ $errors->first('new_password') }}</p>
        @endif
    </div>

    <div class="form-group mb-4">
        <div class="password-container position-relative">
            <input id="new_password_confirmation" type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm New Password">
            <i class="toggle-password2 bi bi-eye eye-toggle"></i>
        </div>
        @if($errors->has('new_password_confirmation'))
            <p class="text-danger">{{ $errors->first('new_password_confirmation') }}</p>
        @elseif($errors->has('new_password'))
            <p class="text-danger">{{ $errors->first('new_password') }}</p>
        @endif
    </div>

    <div class="mb-3">
        <button class="btn btn-primary" type="submit">Ubah Password</button>
    </div>

</form>

</section>

</main><!-- End #main -->

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('current_password');
    const togglePasswordIcon = document.querySelector('.toggle-password');

    togglePasswordIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            togglePasswordIcon.classList.remove('bi-eye');
            togglePasswordIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            togglePasswordIcon.classList.remove('bi-eye-slash');
            togglePasswordIcon.classList.add('bi-eye');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password');
    const togglePasswordIcon = document.querySelector('.toggle-password1');

    togglePasswordIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            togglePasswordIcon.classList.remove('bi-eye');
            togglePasswordIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            togglePasswordIcon.classList.remove('bi-eye-slash');
            togglePasswordIcon.classList.add('bi-eye');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password_confirmation');
    const togglePasswordIcon = document.querySelector('.toggle-password2');

    togglePasswordIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            togglePasswordIcon.classList.remove('bi-eye');
            togglePasswordIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            togglePasswordIcon.classList.remove('bi-eye-slash');
            togglePasswordIcon.classList.add('bi-eye');
        }
    });
});
</script>

<style>
.eye-toggle {
  position: absolute;
  top: 50%;
  right: 20px;
  transform: translateY(-50%);
  cursor: pointer;
}
</style>
@endsection
