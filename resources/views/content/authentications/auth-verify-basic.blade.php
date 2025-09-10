@extends('layouts/blankLayout')

@section('title', 'Verify your email')

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6 mx-4">

      <!-- Logo -->
      <div class="card p-7">
        <!-- Forgot Password -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{url('/')}}" class="app-brand-link gap-3">
            <span class="app-brand-text demo text-heading fw-semibold">{{ config('variables.templateName') }}</span>
          </a>
        </div>
        <!-- /Logo -->
        <div class="card-body mt-1">
          @if (session('status'))
              <div class="alert alert-success" role="alert">
                  {{ session('status') }}
              </div>
          @endif
          <h4 class="mb-1">Verify your email ✉️</h4>
          <p class="mb-5"> Account activation link sent to your email address: {{ auth()->user()->email }} Please follow the link inside to continue.</p>
          @if (session('resent'))
              <div class="alert alert-success" role="alert">
                  {{ __('A fresh verification link has been sent to your email address.') }}
              </div>
          @endif
          <form id="formAuthentication" class="mb-5" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button class="btn btn-primary d-grid w-100 mb-5">Click here to request another</button>
          </form>
        </div>
      </div>
      <!-- /Forgot Password -->
      <img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block">
      <img src="{{asset('assets/img/illustrations/auth-basic-mask-light.png')}}" class="authentication-image d-none d-lg-block" height="172" alt="triangle-bg">
      <img src="{{asset('assets/img/illustrations/tree.png')}}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block">
    </div>
  </div>
</div>
@endsection
