@extends('layouts.app')

@section('title', $page['title'].' - Rizzo Christian')
@section('body_class', 'page-template-default page theme-motta no-sidebar elementor-default elementor-kit-8 elementor-page')

@section('content')
<div id="page-header" class="page-header">
	<div class="container clearfix">
		<div class="page-header__content">
			<h1 class="page-header__title">{{ $page['title'] }}</h1>
		</div>
	</div>
</div>

<div id="site-content" class="site-content">
	<div class="container clearfix" style="padding:40px 0;">
		{!! $page['content'] !!}
	</div>
</div>
@endsection
