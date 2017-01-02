@extends('admin.layouts.default')

{{-- Content --}}
@section('content')

<div class="dark-panel form-holder">

    <div class="container">
        <div class="row">

            <div class="col-md-12">
                @include('alerts/alerts')
            </div>

        </div>
        <div class="row">

            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Suspend
                        </h3>
                    </div>

                    <form action="{{ URL::to('admin/group/suspend') }}/{{ $group->id }}" method="post">
                        {{ Form::token() }}

                        <div class="panel-body">

                            <div class="form-group {{ ($errors->has('suspendNote')) ? 'has-error' : '' }}" for="suspendNote">
                                <label class="control-label" for="suspendNote">Suspension Note</label>
                                <input name="suspendNote" id="suspendNote" value="{{ Request::old('suspendNote') }}" type="text" class="form-control" placeholder="Note">
                                <?php
                                if($errors->has('suspendNote')){
                                    echo '<span class="label label-danger">' . $errors->first('suspendNote') . '</span>';
                                }
                                ?>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Suspend Group">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop