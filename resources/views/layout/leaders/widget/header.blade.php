<div class="header bg-primary">
    <div class="col-md-6">
        <div class="systemTitle font-impact" style="margin:10px 0px 0px 0px;">
            Employee Management System of Plan-B
        </div>

    </div>
    <div class="col-md-6">
        <div class="systemAlert pull-right colorWrite">
            <ul class="list-unstyled">

                <li  style="position: relative" class="message"><a href="#"><i class="glyphicon glyphicon-comment" title="Notification From You"></i>&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
                <li class="notification" style="position:relative;"><a href=""><i class="glyphicon glyphicon-globe" title="Notification From Member"></i>&nbsp;&nbsp;&nbsp;</a></li>
                <li class="qcNotification" style="position:relative;"><a href=""><i class="glyphicon glyphicon-envelope" title="Notification From QC"></i>&nbsp;&nbsp;&nbsp;</a></li>
                <li style="position: relative"><a href="{{route('profileLeader')}}"><i class="glyphicon-user glyphicon"></i> {{Auth::user()->name}}</a></li>
            </ul>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        jQuery.ajax({
            url:"{{route('notificationLeader')}}",
            type:"GET",
            dataType:"json",
            data:{},
            success:function(data){
                var notification="";
                var sub="<ul class='ProblemNot'>";
                var len=data.length;
                if(len > 0){
                    notification+='<b class="bnot">'+len+'</b>';
                    $(".notification").append(notification);

                }
                for(var i=0;i<data.length;i++){
                    sub+='<li><a href="#'+data[i]['name']+'">'+data[i]['name']+'</a> updated from <a href="">'+data[i]['userName']+'</a> with problem  <b>'+data[i]['leader_check_problem']+'</b></li>';
                }
                sub+="<ul>";
                $(".notification").append(sub);
            },
            complete:function(data){

            },
            error:function(){

            }
        });
        $(".notification a").click(function(e){
            e.preventDefault();
            $(".ProblemNot").toggle();
        });
        $("body").on('click','.ProblemNot a',function(){
            var hr=$(this).attr('href');
            hr=hr.replace("#","");
            $("td#"+hr+"").parent().css({"background-color":"rgba(178, 243, 189, 0.44)"});
        });
        //End notification alert
        jQuery.ajax({
            url:"{{route('messageLeader')}}",
            type:"GET",
            dataType:"json",
            data:{},
            success:function(data){
                var notification="";
                var sub="<ul class='ProblemNot Promessage'>";
                var len=data.length;
                if(len > 0){
                    notification+='<b class="bnot">'+len+'</b>';
                    $(".message").append(notification);

                }
                for(var i=0;i<data.length;i++){
                    sub+='<li><a href="#'+data[i]['name']+'">'+data[i]['name']+'</a> make by <a href="">'+data[i]['userName']+'</a> is <b>'+data[i]['leader_check_problem']+'</b></li>';
                }
                sub+="<ul>";
                $(".notification").append(sub);
            },
            complete:function(data){

            },
            error:function(){

            }
        });
        $(".message a").click(function(e){
            e.preventDefault();
            $(".Promessage").toggle();
        });
        $("body").on('click','.Promessage a',function(){
            var hr=$(this).attr('href');
            hr=hr.replace("#","");
            $("td#"+hr+"").parent().css({"background-color":"rgba(178, 243, 189, 0.44)"});
        });
        //End noticfication message

        //Notification from qc checker
        jQuery.ajax({
            url:"{{route('messageQC')}}",
            type:"GET",
            dataType:"json",
            data:{},
            success:function (data) {
                var notification="";
                var sub="<ul class='qcNot'>";
                var len=data.length;
                if(len > 0){
                    notification+='<b class="bnot">'+len+'</b>';
                    $(".qcNotification").append(notification);

                }
                for(var i=0;i<data.length;i++){
                    sub+='<li><a href="#'+data[i]['name']+'">'+data[i]['name']+'</a> Re-Correct form QC <a href="">'+data[i]['userName']+'</a> with problem  <b>'+data[i]['first_checker_problem']+'</b></li>';
                }
                sub+="<ul>";
                $(".qcNotification").append(sub);
            }
        });
        $("body").on("click",".qcNotification",function (e) {
            e.preventDefault();
            $(".qcNot").toggle();
        });
    });
</script>