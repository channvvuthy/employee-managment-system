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
                <li><a href="{{route('createUser')}}"><i class="glyphicon-dashboard glyphicon"></i> Order</a></li>
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
                    <h4>Order</h4>
                </div>
                <div class="panel-body autoPadding">
                    <div class="row">
                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <div class="page-header">
                                <p><i class="glyphicon glyphicon-th-list"></i> &nbsp;&nbsp;Order <span class="totalOrder"></span></p>
                            </div>
                            <div class="base">
                                <div class="col-md-8" style="padding-left:0px;">
                                    <ul class="list-inline">
                                        <li><input type="radio" name="filter"  class="filter" value="all" ><span for="all">All</span></li>
                                        <li><input type="radio" name="filter"  class="filter" value="ready" ><span for="ready">Ready</span></li>
                                        <li><input type="radio" name="filter"  class="filter" value="not_yet"><span for="not_yet">Not Yet Ready</span></li>
                                        <li><input type="radio" name="filter" class="filter"  value="rest_deadline" checked for="rest_dealine"><span>Rest Deadline</span></li>
                                    </ul>
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
                                    <table class="table table-responsive table_order hidden">
                                    <tr>

                                        <th>Order Name</th>
                                        <th>Member Name</th>
                                        <th>Deadline</th>
                                        <th>Base Name</th>
                                        <th>Type</th>
                                        <th>Version</th>
                                        <th>Status</th>
                                    
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
    <script type="text/javascript">
       $(document).ready(function(){
         // Count table order
         jQuery.ajax({
            url:"{{route('manager_count_order')}}",
            type:"GET",
            data:{filter:"rest_deadline"},
            dataType:"json",
            success:function(response){
                $(".not").html(response);
                $(".not").parent().attr('title','NUMBER ORDER THAT REST DEADLINE');
            }
         });
         // Load first
         var filter="rest_deadline";
            jQuery.ajax({
                url:"{{route('manage_load_first')}}",
                type:"GET",
                data:{filter:"rest_deadline",start:0},
                dataType:"json",
                beforeSend:function(){
                    $(".loading").removeClass('hidden');
                },
                success:function(response){
                     $(".loading").addClass('hidden');
                     $(".table_order").removeClass('hidden');
                     var tbody="";
                     for(var i=0; i<response.length; i++){
                        if(filter=="rest_deadline"){
                        tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"   <b style='color:red;'>&nbsp;"+response[i].Expire+" day</b></td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                        }else{
                        tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"</td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                        }
                     }
                     $(".loadMore").attr('data-start',i);

                     
                     $(".result_table").html(tbody);
                     var windowHeight=$("html").height();
                     $(".loadMore").removeClass('hidden');
                     $(".navLeft").css({"height":windowHeight+"px"});
                
                },
                error:function(error){
                    $(".problem").removeClass('hidden');
                    $(".loading").removeClass('hidden');
                }
            });
            var pageStart=0;
            var option="";
            // load button first
            $("body").on("click",".loadMore",function(e){
                var filter=$(".filter:checked").val();
                var attr = $(this).attr('option');
                if (typeof attr !== typeof undefined && attr !== false) {
                    option=attr;
                }
                e.preventDefault();
                var indexStart=$(this).attr('data-start');
                pageStart=pageStart+parseInt(indexStart);
                jQuery.ajax({
                    url:"{{route('manage_load_first')}}",
                    type:"GET",
                    data:{filter:filter,start:pageStart,option:option},
                    dataType:"json",
                    beforeSend:function(){
                        $(".loading").removeClass('hidden');
                    },
                    success:function(response){
                         $(".loading").addClass('hidden');
                         $(".table_order").removeClass('hidden');
                         var tbody="";
                         if(response.length <= 0){
                            return;
                         }
                         for(var i=0; i<response.length; i++){
                            tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"</td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                         }
                         $(".loadMore").attr('data-start',i);

                         
                         $(".result_table").append(tbody);
                         var windowHeight=$("html").height();
                         $(".loadMore").removeClass('hidden');
                         $(".navLeft").css({"height":windowHeight+"px"});
                    
                    },
                    error:function(error){
                        $(".problem").removeClass('hidden');
                        $(".loading").removeClass('hidden');
                    }
                });
            });

            // Radio Filter

            $("body").on("click",".filter",function(e){
                $(".not").html("");
                $(".table_order").addClass('hidden');
                var filter=$(this).val();
                $(this).prop('checked', true);
                 jQuery.ajax({
                    url:"{{route('manage_load_first')}}",
                    type:"GET",
                    data:{filter:filter,start:0},
                    dataType:"json",
                    beforeSend:function(){
                        $(".loading").removeClass('hidden');
                    },
                    success:function(response){
                         $(".loading").addClass('hidden');
                         $(".table_order").removeClass('hidden');
                         var tbody="";
                         for(var i=0; i<response.length; i++){
                                if(filter=="rest_deadline"){
                                    tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"   <b style='color:red;'>&nbsp;"+response[i].Expire+" day</b></td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                                }else{
                                tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"</td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                                }
                         }
                         $(".loadMore").attr('data-start',i);
                         $(".result_table").html(tbody);
                         var windowHeight=$("html").height();
                         $(".loadMore").removeClass('hidden');
                         $(".navLeft").css({"height":windowHeight+"px"});

                         // Count table order
                         jQuery.ajax({
                            url:"{{route('manager_count_order')}}",
                            type:"GET",
                            data:{filter:filter},
                            dataType:"json",
                            success:function(response){
                                $(".not").html(response);
                                if(filter=="all"){
                                    $(".not").parent().attr('title','NUMBER OF ORDER THEPLATE');
                                }else if(filter=="ready"){
                                     $(".not").parent().attr('title','NUMBER OF ORDER THAT READY');
                                }else if(filter=="not_yet"){
                                     $(".not").parent().attr('title','NUMBER OF ORDER NOT YET READY');
                                }else{
                                     $(".not").parent().attr('title','NUMBER OF  ORDER THAT REST DEALINE');
                                }
                            }
                         });
                    },
                    error:function(error){
                        $(".problem").removeClass('hidden');
                        $(".loading").removeClass('hidden');
                    }
                });
                
            });

            $("body").on("change","#first_filter",function(e){
                $(".table_order").addClass('hidden');
                e.preventDefault();

                var filter=$(".filter:checked").val();
                var option=$(this).val();
                $(".print").attr('option',option);
                jQuery.ajax({
                    url:"{{route('manager_option_select')}}",
                    type:"GET",
                    data:{filter:filter,option:option},
                    dataType:"json",
                    beforeSend:function(){
                         $(".loading").removeClass('hidden');
                    },
                    success:function(response){
                        $(".loading").addClass('hidden');
                        $(".table_order").removeClass('hidden');
                         var tbody="";
                         for(var i=0; i<response.length; i++){
                            tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"</td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                         }
                         $(".loadMore").attr('data-start',i);
                         $(".result_table").html(tbody);
                         var windowHeight=$("html").height();
                         $(".loadMore").addClass('hidden');
                         $(".loadMore").attr('option',option);
                         $(".navLeft").css({"height":windowHeight+"px"});

                        // Count table order
                         jQuery.ajax({
                            url:"{{route('manager_count_order_option')}}",
                            type:"GET",
                            data:{filter:filter,option:option},
                            dataType:"json",
                            success:function(response){
                                $(".not").html(response);
                                if(filter=="all"){
                                    $(".not").parent().attr('title','NUMBER OF ORDER THEPLATE');
                                }else if(filter=="ready"){
                                     $(".not").parent().attr('title','NUMBER OF ORDER THAT READY');
                                }else if(filter=="not_yet"){
                                     $(".not").parent().attr('title','NUMBER OF ORDER NOT YET READY');
                                }else{
                                     $(".not").parent().attr('title','NUMBER OF  ORDER THAT REST DEALINE');
                                }
                            }
                         });
                    },
                    error:function(error){
                    }

                });
            });

            // load option
            $("body").on("click",".load",function(e){
                $(".loadMore").addClass('hidden');
                 $(".table_order").addClass('hidden');
                    e.preventDefault();
                    var first_select_date=$('.date').find(":selected").val();
                    var first_select_month=$('.month').find(":selected").val();
                    var select_year=$(".year").val();
                    var filter=$(".filter:checked").val();
                    $(".print").attr('date',first_select_date);
                    $(".print").attr('month',first_select_month);
                    $(".print").attr('year',select_year);
                    $(".print").removeAttr('option');
                    // Ajax Request 

                    jQuery.ajax({
                        url:"{{route('manager_load_first')}}",
                        type:"GET",
                        dataType:"json",
                        data:{first_select_date:first_select_date,first_select_month:first_select_month,select_year:select_year,filter:filter},
                        beforeSend:function(){
                            $(".loading").removeClass('hidden');
                        },
                        success:function(response){
                                $(".loading").addClass('hidden');
                                $(".table_order").removeClass('hidden');
                                 var tbody="";
                                 for(var i=0; i<response.length; i++){
                                    tbody+="<tr><td>"+response[i].order_id+"</td><td>"+response[i].member_name+"</td><td>"+response[i].dateline+"</td><td>"+response[i].base_name+"</td><td>"+response[i].type+"</td><td>"+response[i].version+"</td><td>"+(response[i].status== 1 ? 'Complete' : 'Not Yet Complete')+"</td></tr>";
                                 }
                                 $(".loadMore").attr('data-start',i);
                                 $(".result_table").html(tbody);
                                 var windowHeight=$("html").height();
                                $(".navLeft").css({"height":windowHeight+"px"});
                        },
                        error:function(){

                        }
                    });

            });

            // button print

            $("body").on("click",".print",function(e){
                    var option=$(this).attr('option');
                    if (typeof option == typeof undefined ) {
                         option="";
                     }
                    var day=$(this).attr('date');
                    if (typeof day == typeof undefined ) {
                     day="";
                     }

                    var month=$(this).attr('month');
                    if (typeof month == typeof undefined) {
                         month="";
                     }
                    var year=$(this).attr('year');
                    if (typeof year == typeof undefined) {
                         year="";
                     }
                    var filter=$(".filter:checked").val();
                     window.open('{{route('print_report_first')}}?filter='+filter+'&option='+option+'&day='+day+'&month='+month+'&year='+year, '_blank');

            });
       });
    </script>
@stop