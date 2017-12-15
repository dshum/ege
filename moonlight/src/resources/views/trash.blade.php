@extends('moonlight::base')

@section('title', 'Корзина')

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/touch/css/search.css">
@endsection

@section('js')
<script>
jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

$(function() {
    var countUrl = '{{ route('trash.count') }}';
    
    $('#filter').addClear({
        right: 10,
        paddingRight: "25px",
        onClear: function(){
            $(".items li").show();
        }
    }).keyup(function() {
        var str = $(this).val()

        if (str.length > 0) {
            $(".items li:not(:contains('"+str+"'))").hide();
            $(".items li:contains('"+str+"')").show();
        } else {
            $(".items li").show();
        } 
    }).change(function() {
        var str = $(this).val()

        if (str.length > 0) {
            $(".items li:not(:contains('"+str+"'))").hide();
            $(".items li:contains('"+str+"')").show();
        } else {
            $(".items li").show();
        } 
    });
    
    $('ul.items > li').each(function() {
        var li = $(this);
        var item = $(this).attr('item');

        $.getJSON(countUrl, {
            item: item
        }, function(data) {
            if (data && data.count > 0) {
                var span = $('<span class="dnone total">'+data.count+'</span>');

                li.find('a').append(span);
                span.fadeIn(200);
            } else {
                li.slideUp(200, function() {
                    $(this).remove();
                });
            }
        });
    });
});
</script>
@endsection

@section('body')
<nav>
    <left><span class="hamburger"><span class="glyphicons glyphicons-menu-hamburger"></span></span></left>
    <center><a href="{{ route('home') }}">@yield('title')</a></center>
    <right><a href="{{ route('search') }}"><span class="glyphicons glyphicons-search"></span></a></right>
</nav>
<div class="sidebar">
    <div class="sidebar-container">
        <ul class="menu">
            <li><a href="{{ route('browse') }}">Корень сайта</a></li>
            <li><a href="{{ route('trash') }}">Корзина</a></li>
            <li><hr></li>
            <li><a href="{{ route('users') }}">Пользователи</a></li>
            <li><a href="{{ route('log') }}">Журнал</a></li>
            <li><hr></li>
            <li><a href="{{ route('profile') }}">{{ $loggedUser->first_name }} {{ $loggedUser->last_name }}</a></li>
            <li><a href="{{ route('logout') }}">Выход</a></li>
        </ul>
    </div>
</div>
<div class="main">
    <div class="search-field">
        <input type="text" id="filter" placeholder="Введите название">
    </div>
    <ul class="items">
        @foreach ($items as $item)
        <li item="{{ $item->getNameId() }}">
            <a href="{{ route('trash.item', $item->getNameId()) }}">{{ $item->getTitle() }}</a><br>
            <small>{{ $item->getNameId() }}</small>
        </li>
        @endforeach
    </ul>
</div>
@endsection