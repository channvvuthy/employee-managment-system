@extends('layout.leaders.master')
@section('title')
    Check Base Template
@stop
@section('content')
    @if(!empty(Auth::user()->group))
        @if(Auth::user()->group->type=="qc")
            @include('layout.leaders.widget.headerQC')
        @else
            @include('layout.leaders.widget.header')
        @endif
    @endif

    @include('layout.leaders.widget.navbar')
    <div class="col-md-10" style="overflow: auto;width: 1124px;">
        <div class="pangasu float">
            <ul class="list-unstyled text-center">
                <li><a href="{{route('leader.index')}}"><i class="glyphicon-home glyphicon"></i></a></li>
                <li><a href="#"> <i class="glyphicon glyphicon-folder-open"></i> Check Base</a></li>
            </ul>
        </div>
        <div class="clearfix clear-top-normal" style="margin-top:15px;"></div>
        <form action="{{route('leaderCheckBase')}}" class="SystemForm" method="post">
            <input type="hidden" name="_token" value="{{Session::token()}}">
            @if($errors->first('notice'))
                <div class="alert alert-success">{{$errors->first('notice')}}</div>
            @endif
            <div class="panel panel-default" style="overflow: auto;">
                <div class="panel-heading">
                    <h4>Check Base Template</h4>
                </div>
                <div class="panel-body" style="padding:10px;">
                    <div class="col-md-4 choose" style="padding-left:0px">
                        <select name="choose_action">
                            <option value="">Choose Action</option>
                            <option value="Delete">Delete</option>
                            <option value="Update">Update</option>
                        </select>
                        <button type="submit"><i class="glyphicon-save glyphicon"></i> Save</button>
                    </div>
                    <div class="col-md-4 list" style="padding-left:0px">
                        <input type="text" name="listFolder" id="" placeholder="Enter Location File">
                        <button type="submit"><i class="glyphicon glyphicon-th"></i> List</button>
                    </div>
                    <div class="col-md-4 search" style="padding-left:0px">
                        <input type="text" name="search" id="" placeholder="Search by name,not yet check(N)">
                        <button type="submit"><i class="glyphicon-search glyphicon"></i> Search</button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12" style="padding:0px;">
                        <div class="sort">
                            <label for="">Sort By Month</label>
                        </div>
                        <div class="sort">
                            <input type="date" name="from" id="">
                        </div>
                        <div class="sort">
                            <p>To</p>
                        </div>
                        <div class="sort">
                            <input type="date" name="to" id="">
                        </div>
                        <div class="sort" style="margin-top:7px;">
                            <button type="submit"><i class="glyphicon glyphicon-sort-by-order-alt"></i> Filter</button>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="showRecord">
                            <div class="show">
                                <label for="">Show Record</label>
                            </div>
                            <div class="show">
                                <select name="" id="showNumrow">
                                    <option value=""></option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <table class="table-bordered table" oncontextmenu="onCopyAndPass()" style="width:2600px;">
                        <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Create By</th>
                            <th>Variation Name</th>
                            <th>Pattern Name</th>
                            <th>Version</th>
                            <th>Type</th>
                            <th>Not</th>
                            <th>URL</th>
                            <th>QC Name</th>
                            <th>QC Check Result</th>
                            <th>
                                QC Check Problem
                            </th>

                        </tr>

                        </thead>
                        <tbody>
                        @if(!empty($bases))
                            @foreach($bases as $base)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="idCheck[]" id="" value="{{$base->id}}">
                                    </td>
                                    <td>
                                        {{$base->id}}
                                        <input type="hidden" name="id[]" value="{{$base->id}}">
                                    </td>
                                    <td>{{$base->name}}</td>
                                    <td></td>
                                    <td>{{$base->variations['name']}}</td>
                                    <td>{{$base->patterns['name']}}</td>
                                    <td>{{$base->version_name}}</td>
                                    <td>{{$base->type_name}}</td>
                                    <td>
                                        <input type="text" class="form-control">
                                    </td>
                                    <td>
                                        <a href="{{trim($base->url).'/'.$base->name}}">{{trim($base->url).'/'.$base->name}}</a>
                                    </td>
                                    <td class="QCname"><input type="text" name="qcName[]" id="" class="form-control"
                                                              value="{{$base->first_checker_name}}"></td>
                                    <td>
                                        <select name="" id="leaderChingResult" data="{{$base->id}}" class="form-control"
                                                @if($base->first_checker_result=="0") style="background-color:red;"
                                                @elseif($base->first_checker_result=="1") style="background-color:green;"
                                                @elseif($base->first_checker_result=="2" )style="background-color:darkorange;"
                                                @else style="background-color:#00BCD4;" @endif>
                                            <option value="0" @if($base->first_checker_result=="0") selected @endif>
                                                Recorect
                                            </option>
                                            <option value="1" @if($base->first_checker_result=="1") selected @endif>
                                                Complete
                                            </option>
                                            <option value="2" @if($base->first_checker_result=="2") selected @endif>
                                                Edited
                                            </option>
                                            <option value="3"
                                                    @if($base->first_checker_result!="0" &&$base->first_checker_result!="1"  && $base->first_checker_result!="2"  && $base->leader_check_result!="3"  ) selected @endif>
                                                Not yet check
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="qcProblem[]" id="" class="form-control qcProblem"
                                               title="{{$base->first_checker_problem}}" data-id="{{$base->id}}"
                                               value="{{$base->first_checker_problem}}">
                                    </td>

                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
    <div class="qc">
        <ul class="list-unstyled">
            @if(!empty($qc))
                @if(!empty($qc->users))
                    @foreach($qc->users as $q)
                        <li data-value="{{$q->name}}">{{$q->name}}</li>
                    @endforeach
                @endif
            @endif
        </ul>
    </div>
    {{--add script--}}
    <script type="text/javascript" charset="utf-8">
        $(".table td.QCname input").on('mouseover', function () {
            var isActive = $(this).parent().hasClass('isActive');
            if (isActive == false) {
                $(this).parent().addClass("isActive")
                isActive = true;
            } else if (isActive == true) {
                $(this).parent().removeClass("isActive")
                isActive = false;
            }

        });
        $(".table td.userby input").on('mouseover', function () {
            var isActive = $(this).parent().hasClass('useByActive');
            if (isActive == false) {
                $(this).parent().addClass("useByActive")
                isActive = true;
            } else if (isActive == true) {
                $(this).parent().removeClass("useByActive")
                isActive = false;
            }

        });
        $(".table td.baseURL input").on('mouseover', function () {
            var isActive = $(this).parent().hasClass('baseURLActive');
            if (isActive == false) {
                $(this).parent().addClass("baseURLActive")
                isActive = true;
            } else if (isActive == true) {
                $(this).parent().removeClass("baseURLActive")
                isActive = false;
            }

        });
        $("#baseURL").on("keyup", function () {
            var baseValue = $(this).val();
            $(".baseURLActive input").val(baseValue);
        });

        function changeInput(controlBox) {
            var vC = $("#" + controlBox + "").val();
            var tx = $("#" + controlBox + "").find(":selected").text();
            $(".isActive input[type='text']").val(tx);
            $(".isActive input[type='hidden']").val(vC);
            $(".isActive").removeClass("isActive");
        }
        $("#changeUseBy").on('change', function () {
            var vC = $(this).val();
            var hiddenId = $(this).attr('id');
            var attr = $("#" + hiddenId + " :selected").attr('data');
            $(".useByActive  input").val(vC);
            $(".useByActive  input[type='hidden']").val(attr);
            $(".useByActive").removeClass("useByActive");
        });
        $(".userby input").mouseout(function () {
            var valExist = $(this).val();
            if (valExist) {
                $(this).parent().removeClass("useByActive");
            }
        });
        $(".version input").mouseout(function () {
            var valExist = $(this).val();
            if (valExist) {
                $(this).parent().removeClass("isActive");
            }
        });
        var baseId;
        $(".tdLayout input").click(function () {
            var vl = $(this).val();

            $(".modal").modal("show");
            baseId = $(this).attr('data');
        });

        $("body").on('click', '.assignLayout', function () {
            var layout = $("#layout").val();
            var array = JSON.parse("[" + layout + "]");
            var ln = array.length;
            jQuery.ajax({
                url: "{{route('SaveLayout')}}",
                type: "GET",
                dataType: "json",
                data: {baseId: baseId, layout: layout},
                success: function (data) {
                    console.log(data)
                },
                complete: function (data) {
                    location.reload();
                }
            });
        });
        $("body").on('mouseout', '#layout', function () {
            var layout = $("#layout").val();
            var array = JSON.parse("[" + layout + "]");
            var ln = array.length;
            $(".totalSelect").html(ln + " layout selected");
        });

        function onCopyAndPass() {

        }
        $("body").on('change', '#leaderChingResult', function () {
            var baseId = $(this).attr('data');
            var baseName = $(this).val();
            $("#leaderChingResult optoin[value=" + baseName + "]").attr('selected', 'selected');
            if (baseName == "0") {
                $(this).css({"background-color": "red"});

            } else if (baseName == "1") {
                $(this).css({"background-color": "green"})
            } else if (baseName == "2") {
                $(this).css({"background-color": "darkorange"})
            } else if (baseName == "3") {
                $(this).css({"background-color": "#00BCD4"})
            }
            jQuery.ajax({
                url: "{{route('upateResuleBaseLeaderCheck')}}",
                type: "GET",
                dataType: "json",
                data: {baseName: baseName, baseId: baseId, qc: 1},
                success: function (data) {

                },
                complete: function (data) {

                },
                error: function () {

                }
            });

        });

        $("body").on('keyup', '.qcProblem', function () {
            var baseId = $(this).attr('data-id');
            var val = $(this).val();
            if (val) {
                jQuery.ajax({
                    url: "{{route('leaderCheckingProblem')}}",
                    type: "GET",
                    dataType: "json",
                    data: {baseId: baseId, val: val, qc: 1},
                    success: function () {

                    },
                    complete: function (data) {

                    },
                    error: function () {

                    }
                });
            }

        });
        $("body").on('keyup', '.note', function () {
            var baseId = $(this).attr('data');
            var val = $(this).val();
            if (val) {
                jQuery.ajax({
                    url: "{{route('leaderCheckingNote')}}",
                    type: "GET",
                    dataType: "json",
                    data: {baseId: baseId, val: val},
                    success: function () {

                    },
                    complete: function (data) {

                    },
                    error: function () {

                    }
                });
            }

        });
        $("#showNumrow").on('change', function () {
            var url = window.location.href;
            var number = $(this).val();
            var n = url.indexOf('?num_row');
            url = url.substring(0, n != -1 ? n : url.length);
            window.location.href = url + "?num_row=" + number;

        });
        var select_report = $("#select_report").val();
        var tableReport = "<table class='table table-bordered'><thead><tr><td>Member Name</td><td>Total</td></tr></thead><tbody>";
        var total = 0;
        var endTableReport = "</tbody></table>"
        jQuery.ajax({
            url: "{{route('reportBase')}}",
            type: "GET",
            dataType: "json",
            data: {select_report: select_report},
            beforeSend: function () {
                $(".loadingData").removeClass("hidden");
            },
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    tableReport += "<tr><td>" + data[i]['name'] + "</td><td>" + data[i]['Total'] + "</td></tr>"
                    total = total + data[i]['Total'];
                }
                tableReport += "<tr><td colspan='2'align='right'>Total:" + total + "</td></tr>";
                tableReport += endTableReport;
                $(".reportResult").html(tableReport);
            },
            complete: function () {
                $(".loadingData").addClass("hidden");
            }
        });
        $("#select_report").on('change', function () {
            var select_report = $("#select_report").val();
            var tableReport = "<table class='table table-bordered'><thead><tr><td>Member Name</td><td>Amount</td></tr></thead><tbody>";
            var endTableReport = "</tbody></table>";
            var total = 0;
            select_report = $(this).val();
            jQuery.ajax({
                url: "{{route('reportBase')}}",
                type: "GET",
                dataType: "json",
                data: {select_report: select_report},
                beforeSend: function () {
                    $(".loadingData").removeClass("hidden");
                },
                success: function (data) {
                    for (var i = 0; i < data.length; i++) {
                        tableReport += "<tr><td>" + data[i]['name'] + "</td><td>" + data[i]['Total'] + "</td></tr>";
                        total = total + data[i]['Total'];

                    }
                    tableReport += "<tr><td colspan='2'align='right'>Total:" + total + "</td></tr>";
                    tableReport += endTableReport;

                    $(".reportResult").html(tableReport);
                },
                complete: function () {
                    $(".loadingData").addClass("hidden");

                }
            });
        });

        $(".load").click(function () {
            var tableReport = "<table class='table table-bordered'><thead><tr><td>Member Name</td><td>Total</td></tr></thead><tbody>";
            var endTableReport = "</tbody></table>"
            var dateReport = $("#dateReport").val();
            var monthReport = $("#monthReport").val();
            var yearReport = $("#yearReport").val();
            var total = 0;
            jQuery.ajax({
                url: "{{route('loadReportBase')}}",
                type: "GET",
                dataType: "",
                data: {dateReport: dateReport, monthReport: monthReport, yearReport: yearReport},
                beforeSend: function () {
                    $(".loadingData").removeClass("hidden");
                },
                success: function (data) {
                    for (var i = 0; i < data.length; i++) {
                        tableReport += "<tr><td>" + data[i]['name'] + "</td><td>" + data[i]['Total'] + "</td></tr>";
                        total = total + data[i]['Total'];
                    }
                    tableReport += "<tr><td colspan='2'align='right'>Total:" + total + "</td></tr>";
                    tableReport += endTableReport;
                    $(".reportResult").html(tableReport);
                },
                complete: function () {
                    $(".loadingData").addClass("hidden");
                }
            });
        });
        $(document).ready(function () {
            $(".group").css({"display": "none"});
            $(".userby input").mouseover(function (e) {
                var x = $(this).position().top;
                var y = $(this).position().left;
                $(".group").css({"margin-top": x + "px"});
                $(".group").css({"margin-left": (y - 200) + "px"});
                $(".group").css({"display": "block"});
            });
            $(".QCname input").mouseover(function (e) {
                var x = $(this).position().top;
                var y = $(this).position().left;
                $(".qc").css({"margin-top": (x + 200) + "px"});
                $(".qc").css({"margin-left": (y - 1500) + "px"});
                $(".qc").css({"display": "block"});
            });
            $(".qc li").click(function () {
                var id = $(this).attr('data-value');

                $(".isActive input[type='text']").val(id);
                $(".qc").css({"display": "none"});
                $(".useByActive").removeClass("useByActive");

            });
            $("body").click(function () {
                $(".group").css({"display": "none"});
            })
        });

    </script>
@stop