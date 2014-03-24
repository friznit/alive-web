@extends('admin.layouts.default')

<div class="admin-panel">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

            {{-- Content --}}
            @section('content')

            @if (Sentry::check())

            <form class="light" action="{{ URL::to('admin/application/search') }}" method="post">

                {{ Form::token() }}

                <div class="row">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control">
                        <span class="input-group-btn">
                            <input class="btn btn-default" type="submit" value="Search">
                        </span>
                        </div>
                        <?php
                        if($errors->has('query')){
                            echo '<span class="label label-danger">' . $errors->first('query') . '</span>';
                        }
                        ?>
                        <div class="input-group">
                            <label class="radio-inline">
                                <input type="radio" name="type" value="name" checked> by Name
                            </label>
                        </div>
                    </div>
                </div>

            </form>


            <h2>Groups</h2>

            <table class="table table-hover">
                <tbody>
                @foreach ($allClans as $clan)
                <tr>
                    <td><a href="{{ URL::to('admin/clan/show') }}/{{ $clan->id }}"><img src="{{ $clan->avatar->url('tiny') }}" ></a></td>
                    <td><a href="{{ URL::to('admin/clan/show') }}/{{ $clan->id }}">{{{ $clan->name }}}</a></td>
                    <td>
                        <?php
                        if(!is_null($clan->country)){
                            ?>
                            <img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($clan->country) }}.png" alt="{{ $clan->country_name }}" title="{{ $clan->country_name }}"/>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $applied = false;
                        foreach($applications as $application){
                            $application_id = $application->clan_id;
                            if($clan->id == $application_id){
                                $applied = true;
                            }
                        }
                        if(!$applied){
                        ?>
                        <button class="btn btn-default" onClick="location.href='{{ URL::to('admin/application/lodge') }}/{{ $clan->id}}'">Apply</button>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>

            <?php echo $allClans->links(); ?>

            @endif

            </div>
        </div>
    </div>
</div>



@stop
