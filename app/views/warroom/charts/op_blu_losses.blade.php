@extends('warroom.partials._chart')

{{-- Chart Data --}}
@section('chart_datasource')

            blu_casualties_data = [];

            var ajaxUrl = '{{ URL::to('/') }}/api/oplossesblu?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}';
            $.getJSON(ajaxUrl, function(data) {
                blu_casualties_data = data
                makeBluLossesChart();
            });

@overwrite

{{-- Chart Data --}}
@section('chart_data')
data: blu_casualties_data
@overwrite

{{-- Chart Function --}}
@section('chart_function')
function makeBluLossesChart() {
@overwrite

{{-- Chart Colours --}}
@section('chart_colours')
colors: ['#226365', '#47a1a4', '#3a9093', '#308183', '#266f71', '#1b5b5d', '#104547'],
@overwrite

{{-- Chart Id --}}
@section('chart_id')
renderTo: 'chart_blu_losses',
@overwrite

{{-- Chart Title --}}
@section('chart_title')
text: 'BLUFOR Losses',
@overwrite

{{-- Chart Tooltip --}}
@section('chart_tooltip')
return '<b>' + this.point.name + ' Losses: '+ this.y + '</b> ';
@overwrite

{{-- Chart Element --}}
@section('chart_element')
<div id="chart_blu_losses"></div>
@overwrite