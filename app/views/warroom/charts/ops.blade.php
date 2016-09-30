@extends('warroom.partials._chart')

{{-- Chart Data --}}
@section('chart_datasource')

            op_data = [];

            var ajaxUrl = '{{ URL::to('/') }}/api/operationsbymap';
            $.getJSON(ajaxUrl, function(data) {
                op_data = data;
                makeOpChart();
            });



@overwrite

{{-- Chart Data --}}
@section('chart_data')
data: op_data
@overwrite

{{-- Chart Function --}}
@section('chart_function')
function makeOpChart() {
@overwrite

{{-- Chart Colours --}}
@section('chart_colours')
colors: ['#226365', '#47a1a4', '#3a9093', '#308183', '#266f71', '#1b5b5d', '#104547'],
@overwrite

{{-- Chart Id --}}
@section('chart_id')
renderTo: 'chart_op',
@overwrite

{{-- Chart Title --}}
@section('chart_title')
text: 'Operation AO',
@overwrite

{{-- Chart Tooltip --}}
@section('chart_tooltip')
return '<b>' + (this.point.name.charAt(0).toUpperCase() + this.point.name.slice(1)) + ' Operations: '+ this.y + '</b> ';
@overwrite

{{-- Chart Element --}}
@section('chart_element')
<div id="chart_op"></div>
@overwrite