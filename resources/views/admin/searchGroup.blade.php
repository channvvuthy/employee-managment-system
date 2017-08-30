@extends('layout.admin.master')
@section('content')
    @include('layout.admin.widget.header')
    @include('layout.admin.widget.navbar')
    <div class="col-md-9">
        <div class="pangasu float">
            <ul class="list-unstyled text-center">
                <li><a href="{{route('adminManage')}}"><i class="glyphicon glyphicon-home"></i></a></li>
                <li><a href="{{route('createGroup')}}"><i class="glyphicon glyphicon-th-large"></i> Group</a></li>
            </ul>
        </div>
        <div class="clearfix clear-top-normal" style="margin-top:15px;"></div>
        @if($errors->first('notice'))
            <div class="alert alert-success">
                {{$errors->first('notice')}}
            </div>


        @endif
        <div class="panel panel-default SystemForm">
            <div class="panel-heading">
                <form action="{{route('searchGroup')}}" method="get">
                    <img src="{{asset('icon/1489866801_icon-111-search.png')}}" alt="" id="isearch">
                    <input type="text" name="search" id="search" placeholder="search...">
                    <input type="hidden" name="_token" value="{{Session::token()}}">
                </form>

            </div>
            <div class="panel-body">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th style="width:200px;">Action</th>
                        <th>ID</th>
                        <th style="width:140px;">Group Name</th>
                        <th>Description</th>
                        <th style="width:140px;">Group Type</th>
                        <th>Active</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>
                                <a href="{{route('editGroup',['id'=>$group->id])}}" class="btn btn-primary btn-xs" ><i class="glyphicon glyphicon-edit"></i> Edit</a> 
                                <a href="{{route('deleteGroup',['id'=>$group->id])}}" onclick="return confirm('Are you sure to delete this group?')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a> 
                                <a href="{{route('groupActive',array('id'=>$group->id))}}" class="btn btn-info btn-xs">@if($group->active=="0")  <i class="glyphicon glyphicon-ban-circle"></i> Disable   @else <i class="  glyphicon glyphicon-ok-circle"></i> Enable &nbsp; @endif</a></td>
                            <td>{{$group->id}}</td>
                            <td>{{$group->name}}</td>
                            <td>{{$group->description}}</td>
                            <td>{{$group->type}}</td>
                            <td>@if($group->active) Active @else Inactive @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop