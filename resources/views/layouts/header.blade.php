<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>DIGITAL TELKOMSEL PEMUDA</title>
<!--
    
Template 2095 Level

http://www.tooplate.com/view/2095-level

-->
    <!-- load stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">  <!-- Google web font "Open Sans" -->
    <link rel="stylesheet" href="{{asset('font-awesome-4.7.0/css/font-awesome.min.css')}}">                <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">                                      <!-- Bootstrap style --><!-- 
    <link rel="stylesheet" type="text/css" href="{{asset('slick/slick.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('slick/slick-theme.css')}}"> -->
    <link rel="stylesheet" type="text/css" href="{{asset('css/datepicker.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/jquery-ui.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/tooplate-style.css')}}">

    <style>
    #mychart {
    height: 400px;
    max-width: 800px;
    margin: 0 auto;
    }

    /* Link the series colors to axis colors */
    .highcharts-color-0 {
        fill: red;
        stroke: #000000;
    }
    .highcharts-color-0 .highcharts-point {
        fill: red;
        stroke: #000000;
    }
    .highcharts-axis.highcharts-color-0 .highcharts-axis-line {
        stroke: red;
    }
    .highcharts-axis.highcharts-color-0 text {
        fill: #000000;
    }
    .highcharts-color-1 {
        fill: #FFD700;
        stroke: #000000;
    }
    .highcharts-axis.highcharts-color-1 .highcharts-axis-line {
        stroke: #FFD700;
    }
    .highcharts-axis.highcharts-color-1 text {
        fill: #000000;
    }

    .highcharts-color-1 .highcharts-point {
        fill: #FFD700;
        stroke: #000000;
    }


    .highcharts-yaxis .highcharts-axis-line {
        stroke-width: 2px;
    }</style>
    @yield('css')
</head>