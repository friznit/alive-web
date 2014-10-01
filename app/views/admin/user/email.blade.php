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
        
        <div class = "row">
        	<div class="col-md-13">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Your Message</h3>
                    </div>
                    <div class="panel-body">
                    	
					<form action="{{ URL::to('admin/users/email') }}" method="post">

                        {{ Form::token() }}

                        <div class="panel-body">

                            <div class="form-group {{ ($errors->has('msg')) ? 'has-error' : '' }}" for="msg">
                                <label class="control-label" for="msg">Text</label>
                                <input name="msg" value="{{ Request::old("msg") }}" type="textarea" class="form-control" placeholder="message">
                                <?php
                                if($errors->has('msg')){
                                    echo '<span class="label label-danger">' . $errors->first('msg') . '</span>';
                                }
                                ?>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Send Email">
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop