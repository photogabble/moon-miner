{{--
    Blacknova Traders - A web-based massively multiplayer space combat and trading game
    Copyright (C) 2001-2024 Ron Harwood and the BNT development team.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    File: layout.blade.php
--}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- START OF HEADER -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Description" content="A free online game - Open source, web game, with multiplayer space exploration">
    <meta name="Keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
    <meta name="Rating" content="General">
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Styles -->
    @vite("resources/css/app.css")
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu">

    <title>@yield('title')</title>
    @if($include_ckeditor)
        <script src="/javascript/ckeditor/ckeditor.js"></script>
    @endif
    <script async src="/javascript/framebuster.js.php"></script>
</head>
<!-- END OF HEADER -->

<body class="{{ $body_class ?? 'bnt' }}">
<div class="wrapper">

    <!-- START OF BODY -->
    @yield('content')
    <!-- END OF BODY -->

    <!-- START OF FOOTER -->
    <x-footer />
<!-- END OF FOOTER -->

</body>
</html>
