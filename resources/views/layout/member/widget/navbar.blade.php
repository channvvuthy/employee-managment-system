<div class="col-md-2 navLeft">
    <ul class="list-unstyled">
        @if(!empty(Auth::user()->group['type']))
            @if(Auth::user()->group['type']=='base')
                <li><a href="{{route('createPath')}}"><i class="glyphicon glyphicon-folder-open"></i> Your
                        Directory</a></li>
                <li><a href="{{ route('tool') }}"><i class="glyphicon glyphicon-hdd"></i> Tool</a>
                </li>
                <li>
                    <a href="@if(Auth::check()) @if(!empty(Auth::user()->path)){{route('createBase')}} @else {{route('createBase')}}@endif @endif"> <i class="glyphicon glyphicon-envelope"></i> Create Base
                    </a></li>

                <li><a href="{{route('memberViewBase')}}"><i class="glyphicon glyphicon-book"></i> Your Base</a>
                <li><a href="{{route('memberViewLayout')}}"><i class="glyphicon glyphicon-book"></i> View
                        Layout</a>
                </li>
                <li><a href="{{route('logout')}}"><i class="glyphicon glyphicon-log-in"></i> Logout</a>
                </li>
            @elseif(Auth::user()->group['type']=='qc')
                <li><a href="{{route('leaderFirstGetBase')}}"><i class="fa fa-book"></i> Check Base</a>
                <li><a href="{{route('leaderFirstGetBase')}}"><i class="fa fa-book"></i> Check First</a>
                <li><a href="{{route('FirstLeaderReport')}}"><i class="fa fa-print"></i> Report</a></li>
                <li><a href="{{route('logout')}}"><i class="fa fa-sign-out"></i> Logout</a>
                </li>

            @elseif(Auth::user()->group['type']=='first')
                <li><a href="{{route('member.index')}}"><i class="glyphicon glyphicon-signal"></i> Order</a></li>
                <li><a href="{{route('member.directory')}}"><i class="glyphicon glyphicon-file"></i> Directory</a></li>
                <li><a href="{{route('create.first')}}"><i class="glyphicon-user glyphicon"></i> Create First</a></li>
                <li><a href="{{route('logout')}}"><i class="glyphicon glyphicon-log-in"></i> Logout</a></li>
            @endif
        @endif
    </ul>
</div>