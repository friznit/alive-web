@extends('loading.layouts.default')

{{-- Content --}}
@section('content')
<div class="topspacer"></div>
<div id="loading" class="loading">
   <h1>
    <span class="let1">I</span>  
    <span class="let2">N</span>  
    <span class="let3">I</span>  
    <span class="let4">T</span>  
    <span class="let5">I</span>  
    <span class="let6">A</span>  
    <span class="let7">L</span>  
    <span class="let8">I</span>  
    <span class="let9">S</span>  
    <span class="let10">I</span>  
    <span class="let11">N</span>  
    <span class="let12">G</span>  
  </h1>
 </div>

<div class="bottomspacer"></div>
<script type="text/javascript">
$(document).ready(function() {

             $.ajax({
                    url: '{{ URL::to('/') }}/war-room/load',
                     type: "GET", 
    success: function(data){
    window.location = '{{ URL::to('/') }}/war-room/';
    }
                });  
                 }); 

  </script>

@stop
