@extends('warroom.layouts.operations')

{{-- Content --}}
@section('content')


<div class="playback-container" style="top: 33px;">
    <div class="playback-container__map" id="map"></div>

    <div class="player-list">
        <a href="<?php //echo WEB_PATH; ?>" class="playback-container__back">
            <i class="fa fa-arrow-left"></i>
            Mission list
        </a>

        <a href="#" class="player-list__toggle-sticky" title="Toggle player list auto hide">
            <i class="fa fa-low-vision" aria-hidden="true"></i>
        </a>

        <div class="player-list__content"></div>
    </div>

    <div class="timeline timeline--loading">

        <a href="#" class="timeline__toggle-playback">
            <i class="fa fa-pause"></i>
        </a>

        <a href="#" data-speed="5" class="timeline__speed">5x</a>
        <a href="#" data-speed="10" class="timeline__speed x10">10x</a>
        <a href="#" data-speed="30" class="timeline__speed x30">30x</a>

        <div id="timeline__silder" class="timeline__slider">
            <div class="progress">
                <div class="indeterminate"></div>
            </div>
        </div>

        <a href="#" class="timeline__share" title="Share the current time and speed">
            <i class="fa fa-share-alt"></i>
        </a>

        <a href="#" class="timeline__fullscreen" title="Go Fullscreen">
            <i class="fa fa-arrows-alt"></i>
        </a>
    </div><!--/timeline-->
</div><!--/playback-container-->

@stop
