@if (Alert::has('success'))

<div id="warroom_alert">
@foreach (Alert::get('success') as $alert)
<div class="alert alert-success">{{ $alert }}</div>
@endforeach
</div>

@endif

@if (Alert::has('info'))

<div id="warroom_alert">
@foreach (Alert::get('info') as $alert)
<div class="alert alert-info">{{ $alert }}</div>
@endforeach
</div>

@endif

@if (Alert::has('warning'))

<div id="warroom_alert">
@foreach (Alert::get('warning') as $alert)
<div class="alert warning-info">{{ $alert }}</div>
@endforeach
</div>

@endif

@if (Alert::has('error'))

<div id="warroom_alert">
@foreach (Alert::get('error') as $alert)
<div class="alert alert-danger">{{ $alert }}</div>
@endforeach
</div>

@endif