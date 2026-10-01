@extends('layouts.app')

@section('title', 'Pagina non trovata - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta no-sidebar elementor-default elementor-kit-8')

@section('content')
<div id="site-content" class="site-content">
	<div class="container clearfix" style="padding:80px 0;text-align:center;">
		<h1 class="page-header__title">404</h1>
		<p>Spiacenti, questa pagina non è stata trovata.</p>
		<p><a class="button motta-button--bg-color-black" href="{{ route('home') }}">Torna alla home</a></p>
	</div>
</div>
@endsection
