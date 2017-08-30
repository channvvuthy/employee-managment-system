@extends('layout.admin.master')
@section('content')
@include('layout.admin.widget.header')
@include('layout.admin.widget.navbar')
<div class="col-md-9">
    <div class="pangasu float">
        <ul class="list-unstyled text-center">
            <li><a href="/administrator/index"><i class="glyphicon glyphicon-home"></i></a></li>
            <li><a href="">Database</a></li>
        </ul>
    </div>
    <div class="clearfix clear-top-normal" style="margin-top:15px;"></div>
    @if($errors->first('notice'))
    <div class="alert alert-success">
        {{$errors->first('notice')}}
    </div>
    @endif
    <div class="row">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Databases</h4>
                    </div>
                    <div class="panel-body">
                        <div class="clearfix clear-top-simple"></div>
                        <div class="form-group">
                            <div class="col-md-2" style="padding-left: 0px;">
                                <br>
                                <label for="">Backup Database</label>
                            </div>
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-primary btn-block" style="height:35px;"
                                        id="backup"><i class="glyphicon glyphicon-save"></i> Backup
                                </button>
                            </div>
                        </div>
                        <div class="clearfix" style="margin-top:150px;"></div>
                        {{-- List database --}}
                        <div class="databaseTable">

                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar"
                                     aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%">Loading
                                    data...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- Modal database backup --}}
    <div class="modal fade" id="modal-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4>Backup Database</h4>
                </div>
                <div class="modal-body">
                    <p class="titleLoading">Loading databases&hellip;</p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40"
                             aria-valuemin="0" aria-valuemax="100" style="width:100%">
                        </div>
                    </div>
                    <div class="alert alert-success hidden message">
                        <p>Dabase backup complete!</p>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{-- End modal database backup --}}
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("select").select2();
        // backup databae
        $("#backup").on("click", function (e) {
            e.preventDefault();
            jQuery.ajax({
                url: "{{route('backup_database')}}",
                dataType: "json",
                type: "GET",
                beforeSend: function () {
                    $(".modal").modal("show");
                },
                success: function (response) {
                    $(".progress").addClass('hidden');
                    $(".titleLoading").addClass('hidden');
                    $(".message").removeClass('hidden');
                    // List database
                    jQuery.ajax({
                        url: "{{route('list_database')}}",
                        type: "GET",
                        data: {},
                        dataType: "json",
                        beforeSend: function () {
                        },
                        success: function (response) {
                            var table = "<table class='table table-responsive'><tr><th>Database</th><th>Action</th>";
                            for (var index = 0; index < response.length; index++) {
                                table += "<tr><td>" + response[index] + "</td><td><a href='{{asset('http---localhost')}}/" + response[index] + "' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-save'></i> Download</a> | <a href='' class='btn btn-danger delete btn-xs' data-id='" + response[index] + "'><i class='glyphicon glyphicon-trash'></i> Delete</a></td></tr>"
                            }
                            table += "</tr></table>";
                            $(".databaseTable").html(table);
                        }
                    });
                },
                complete: function () {
                    $(".progress").addClass('hidden');
                    $(".titleLoading").addClass('hidden');
                    $(".message").removeClass('hidden');
                    var i=1;
                    var nTime=setInterval(function(){
                        i++;
                        console.log(i)
                        if(i==3){
                             clearNtime();
                             $(".modal").modal("hide");
                        }
                    },1000);
                    
                    function clearNtime(){
                        clearInterval(nTime);
                    }
                    // List database
                    jQuery.ajax({
                        url: "{{route('list_database')}}",
                        type: "GET",
                        data: {},
                        dataType: "json",
                        beforeSend: function () {
                        },
                        success: function (response) {
                            var table = "<table class='table table-responsive'><tr><th>Database</th><th>Action</th>";
                            for (var index = 0; index < response.length; index++) {
                                table += "<tr><td>" + response[index] + "</td><td><a href='{{asset('http---localhost')}}/" + response[index] + "' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-save'></i> Download</a> | <a href='' class='btn btn-danger delete btn-xs' data-id='" + response[index] + "'><i class='glyphicon glyphicon-trash'></i> Delete</a></td></tr>"
                            }
                            table += "</tr></table>";
                            $(".databaseTable").html(table);
                        }
                    });
                }
            });
        });
        // List database
        jQuery.ajax({
            url: "{{route('list_database')}}",
            type: "GET",
            data: {},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (response) {
                var table = "<table class='table table-responsive'><tr><th>Database</th><th>Action</th>";
                for (var index = 0; index < response.length; index++) {
                    table += "<tr><td>" + response[index] + "</td><td><a href='{{asset('http---localhost')}}/" + response[index] + "' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-save'></i> Download</a> | <a href='' class='btn btn-danger delete btn-xs' data-id='" + response[index] + "'><i class='glyphicon glyphicon-trash'></i> Delete</a></td></tr>"
                }
                table += "</tr></table>";
                $(".databaseTable").html(table);
            }
        });
        // Delete database backup
        $("body").on("click", ".delete ", function (e) {
            e.preventDefault();
            var fileName = $(this).attr('data-id');
            jQuery.ajax({
                url: "{{route('delete_database')}}",
                type: "GET",
                data: {fileName: fileName},
                dataType: "json",
                beforeSend: function () {
                },
                success: function (response) {
                    // List database
                    jQuery.ajax({
                        url: "{{route('list_database')}}",
                        type: "GET",
                        data: {},
                        dataType: "json",
                        beforeSend: function () {
                        },
                        success: function (response) {
                            var table = "<table class='table table-responsive'><tr><th>Database</th><th>Action</th>";
                            for (var index = 0; index < response.length; index++) {
                                table += "<tr><td>" + response[index] + "</td><td><a href='{{asset('http---localhost')}}/" + response[index] + "' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-save'></i> Download</a> | <a href='' class='btn btn-danger delete btn-xs' data-id='" + response[index] + "'><i class='glyphicon glyphicon-trash'></i> Delete</a></td></tr>"
                            }
                            table += "</tr></table>";
                            $(".databaseTable").html(table);
                        }
                    });
                },
                complete: function () {
                    // List database
                    jQuery.ajax({
                        url: "{{route('list_database')}}",
                        type: "GET",
                        data: {},
                        dataType: "json",
                        beforeSend: function () {
                        },
                        success: function (response) {
                            var table = "<table class='table table-responsive'><tr><th>Database</th><th>Action</th>";
                            for (var index = 0; index < response.length; index++) {
                                table += "<tr><td>" + response[index] + "</td><td><a href='{{asset('http---localhost')}}/" + response[index] + "' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-save'></i> Download</a> | <a href='' class='btn btn-danger delete btn-xs' data-id='" + response[index] + "'><i class='glyphicon glyphicon-trash'></i> Delete</a></td></tr>"
                            }
                            table += "</tr></table>";
                            $(".databaseTable").html(table);
                        }
                    });
                }
            });
        });
    });
</script>
@stop