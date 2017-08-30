@extends('layout.manager.master')
@section('title')
    Manager Dashboard
@stop
@section('content')
    @include('layout.manager.widget.header')
    @include('layout.manager.widget.navbar')
    <div class="col-md-10">
        <div class="pangasu float">
            <ul class="list-unstyled text-center">
                <li><a href="/administrator/index"><i class="glyphicon-home glyphicon"></i></a></li>
                <li><a href="#"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; Base</a></li>
            </ul>
        </div>
        <div class="clearfix clear-top-normal" style="margin-top:15px;"></div>

        <form action="" class="SystemForm" method="get" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{Session::token()}}">
            @if($errors->first('notice'))
                <div class="alert alert-success">{{$errors->first('notice')}}</div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Base</h4>
                </div>
                <div class="panel-body autoPadding">
                    <div class="row">
                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <div class="page-header">
                                <p><i class="glyphicon glyphicon-folder-open"></i> &nbsp;&nbsp;Base <span class="totalOrder"></span></p>
                            </div>
                            <div class="base">
                                <div class="col-md-8" style="padding-left:0px;">
                                   <div class="form-group">
                                        <input type="text" class="form-control" id="search" placeholder="Search Base By ID,Name">
                                   </div>
                                </div>
                                <div class="col-md-4" style="padding-right:0px;">
                                    <div class="print"><i class="glyphicon glyphicon-print"></i> Print</div>
                                </div>
                          
                                <hr style="margin:auto;border:1px solid #ddd;clear:both;">
                                <div class="clearfix"><br></div>
                                <div class="col-md-3" style="padding-left:0px;">
                                     <div class="form-group">
                                        <select name="" id="first_filter" class="form-control">
                                            <option value="">Select Option</option>
                                            <option value="today">Today</option>
                                            <option value="this_month">This Month</option>
                                            <option value="this_year">This Year</option>
                               
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <b>OR</b>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="" id="" class="form-control date">
                                            <option value="">Select Date</option>
                                            <?php for($i=1; $i<=31;$i++):?>
                                                <?php if($i<10):?>
                                                    <option value="0<?php echo $i;?>">0<?php echo $i;?></option>
                                                <?php else:?>
                                                     <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                                <?php endif;?>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                       <select name="" id="" class="form-control month">
                                            <option value="">Select Month</option>
                                            <?php for($i=1; $i<=12;$i++):?>
                                                <?php if($i<10):?>
                                                    <option value="0<?php echo $i;?>">0<?php echo $i;?></option>
                                                <?php else:?>
                                                     <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                                <?php endif;?>
                                            <?php endfor;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" name="" id="" class="form-control year" placeholder="Year(Ex: 2017)">
                                    </div>
                                </div>
                                <div class="col-md-2 text-center" style="padding-right:0px;">
                                    <div class="load"><i class="glyphicon glyphicon-save"></i> Get Data</div>
                                </div>
                                <div class="result text-center">
                                    <div class="alert alert-danger hidden problem" data-start="0">Loading data problem</div>
                                    <div class="loading hidden"><img src="{{asset('images/200.gif')}}" alt="" style="margin:auto;"></div>
                                    <table class="table table-responsive">
                                    <tr>

                                        <th>Base ID</th>
                                        <th>Make By</th>
                                        <th>Type</th>
                                        <th>Version</th>
                                        <th>Created At</th>
                                    
                                    </tr>
                                    <tbody class="result_table">
                                        
                                    </tbody>

                                    </table>
                                    <div type="button" class="loadMore hidden">Load More..</div>
                                    <div type="button" class="loadMoreOption hidden">Load More..</div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            

                        </div>
                    </div>
                </div>
             </div>      
        </form>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
                    <h4 class="modal-title" id="modelTitleId">Printing..</h4>
                </div>
                <div class="modal-body text-center">
                    <div class=""><img src="{{asset('images/200.gif')}}" alt="" style="margin:auto;max-width:80px;"></div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        //document ready
        $(document).ready(function(){
             var windowHeight=$("html").height();
             $(".navLeft").css({"height":windowHeight+"px"});
             //auto load base from database
             jQuery.ajax({
                 url:"{{route('manage_get_base_template')}}",
                 type:"GET",
                 data:{filter:"today",start:0,page:1},
                 dataType:"json",
                 beforeSend:function(){
                    $(".loading").removeClass("hidden");
                 },
                 success:function(response){
                     $(".loading").addClass("hidden");
                     var dataBody="";
                     for(var i=0; i<response.length; i++){
                         dataBody+="<tr><td>"+response[i].name+"</td><td>"+response[i].UserName+"</td><td>"+response[i].type_name+"</td><td>"+response[i].version_name+"</td><td>"+response[i].created_at+"</td></tr>";
                     }
                    $(".result_table").html(dataBody);
                    if(response.length){
                        $(".loadMore").removeClass("hidden");
                    }
                    $(".loadMore").attr('data-start',i);
                 }
             });
             //search text box
             $("body").on("keyup","#search",function(){
                 var search=$(this).val();
                 //ajax request
                 
             });
             //button load more
             $("body").on("click",".loadMore",function(){
                   var pageStart=parseInt($(this).attr('data-start'));
                   jQuery.ajax({
                    url:"{{route('manage_get_base_template')}}",
                    type:"GET",
                    data:{filter:"today",start:pageStart,page:1},
                    dataType:"json",
                    beforeSend:function(){
                        $(".loading").removeClass("hidden");
                    },
                    success:function(response){
                        $(".loading").addClass("hidden");
                        var dataBody="";
                        for(var i=0; i<response.length; i++){
                            dataBody+="<tr><td>"+response[i].name+"</td><td>"+response[i].UserName+"</td><td>"+response[i].type_name+"</td><td>"+response[i].version_name+"</td><td>"+response[i].created_at+"</td></tr>";
                            pageStart++;
                        }
                        $(".result_table").append(dataBody);
                        $(".loadMore").removeClass("hidden");
                        $(".loadMore").attr('data-start',pageStart);
                    }
                });
                   
             });

             //search text box

             $("body").on("keyup","#search",function(){
                
                 var search=$(this).val();
                 var pageStart=parseInt($(".loadMore").attr('data-start'));
                 //ajax request when user typing...
                 $.ajax({
                     url:"{{route('manager_search_base')}}",
                     type:"GET",
                     data:{search:search,filter:"today",start:pageStart,page:10},
                     dataType:"json",
                     beforeSend:function(){
                        $(".loading").removeClass("hidden");
                     },
                     success:function(response){
                        $(".loading").addClass("hidden");
                        var dataBody="";
                        for(var i=0; i<response.length; i++){
                            dataBody+="<tr><td>"+response[i].name+"</td><td>"+response[i].UserName+"</td><td>"+response[i].type_name+"</td><td>"+response[i].version_name+"</td><td>"+response[i].created_at+"</td></tr>";
                            pageStart++;
                        }
                        $(".not").html(i);
                        $(".not").parent().attr('title','NUMBER OF ORDER THEPLATE');
                        $(".result_table").html(dataBody);
                        $(".loadMore").addClass("hidden");
                        $(".loadMore").attr('data-start',pageStart);
                        var windowHeight=$("html").height();
                        $(".navLeft").css({"height":windowHeight+"px"});
                     }
                 });
             });

             //filter option

             $("body").on("change","#first_filter",function(){
                 $(".print").attr('data-filter',$(this).val());
                 var filter=$(this).val();
                 var search=$("#search").val();
                 var pageStart=parseInt($(".loadMore").attr('data-start'));
                 //ajax request filter option
                  $.ajax({
                     url:"{{route('manager_filter_option')}}",
                     type:"GET",
                     data:{search:search,filter:filter,start:0,page:10},
                     dataType:"json",
                     beforeSend:function(){
                        $(".loading").removeClass("hidden");
                     },
                     success:function(response){
                        $(".loading").addClass("hidden");
                        var dataBody="";
                        for(var i=0; i<response.length; i++){
                            dataBody+="<tr><td>"+response[i].name+"</td><td>"+response[i].UserName+"</td><td>"+response[i].type_name+"</td><td>"+response[i].version_name+"</td><td>"+response[i].created_at+"</td></tr>";
                            pageStart++;
                        }
                        
                        $(".not").html(i);
                        if(filter=="today"){
                            $(".not").parent().attr('title','BASE TEMPLATE TODAY');
                        }else if(filter=="this_month"){
                            $(".not").parent().attr('title','BASE TEMPLATE THIS MONTH');
                        }else if(filter=="this_year"){
                            $(".not").parent().attr('title','BASE TEMPLATE THIS YEAR');
                         }else{
                            $(".not").parent().attr('title','ALL BASE TEMPLATE');
                        }
                        $(".result_table").html(dataBody);
                        $(".loadMore").addClass("hidden");
                        $(".loadMore").attr('data-start',pageStart);
                        var windowHeight=$("html").height();
                        $(".navLeft").css({"height":windowHeight+"px"});
                     }
                 });
             });

             // button load
             $("body").on("click",".load",function(){
                 $(".print").removeAttr('data-filter');
                 var day=$(".date").val();
                 var month=$(".month").val();
                 var search=$("#search").val();
                 var year=$(".year").val();
                 var pageStart=parseInt($(".loadMore").attr('data-start'));
                 $.ajax({
                     url:"{{route('manager_load_base_template')}}",
                     type:"GET",
                     data:{search:search,day:day,month:month,year:year,start:0,page:10},
                     dataType:"json",
                     beforeSend:function(){
                        $(".loading").removeClass("hidden");
                     },
                     success:function(response){
                        $(".loading").addClass("hidden");
                        var dataBody="";
                        for(var i=0; i<response.length; i++){
                            dataBody+="<tr><td>"+response[i].name+"</td><td>"+response[i].UserName+"</td><td>"+response[i].type_name+"</td><td>"+response[i].version_name+"</td><td>"+response[i].created_at+"</td></tr>";
                            pageStart++;
                        }
                        $(".result_table").html(dataBody);
                        $(".not").html(i);
                        $(".loadMore").addClass("hidden");
                        $(".loadMore").attr('data-start',pageStart);
                        var windowHeight=$("html").height();
                        $(".navLeft").css({"height":windowHeight+"px"});
                     }
                 });

             });

             //button print
             $("body").on("click",".print",function(){
                var filter = $(this).attr('data-filter');
                var day=$(".date").val();
                var month=$(".month").val();
                var year=$(".year").val();
                var search=$("#search").val();
                
                // For some browsers, `attr` is undefined; for others,
                // `attr` is false.  Check for both.
                if (typeof filter !== typeof undefined && filter !== false) { 
                }else{
                    filter="";
                }

                // print request ajax
                jQuery.ajax({
                    url:"{{route('manager_print_report')}}",
                    type:"GET",
                    data:{filter:filter,day:day,month:month,year:year,search:search},
                    dataType:"json",
                    beforeSend:function(){
                        $(".modal").modal("show");
                    },
                    success:function(response){
                        //window.location.href="{{route('manager_print_report')}}?filter="+filter+"&day="+day+"&month="+month+"&year="+year+"&search="+search;
                    },
                    complete:function(){
                        window.location.href="{{route('manager_print_report')}}?filter="+filter+"&day="+day+"&month="+month+"&year="+year+"&search="+search;
                        $(".modal").modal("hide");
                    }

                });


                
             });
        });
    </script>
@stop