@extends('back-end.admin.master')
@section('title')
    User
@endsection
<style type="text/css">
	.page-navigation {
		float: right;
	}
</style>
@section('content')
    <div id="page-wrapper" data-ng-controller="UserControler">
        <div class="container-fluid hidden">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">List users</h3>
                </div>
                <table class="table table-hover fix-height-tb table-striped" ng-table="tableParams">
	                <tbody>
	                    <tr ng-repeat="user in $data">
	                    	<td data-title="'ID'" class="text-center" >
	                            @{{user.id}}
	                        </td>
	                        <td data-title="'Name'">
	                            <img ng-if="user.avatar" class="img-circle" ng-src="@{{user.avatar}}" alt="" height="40" style="margin-left: 20px;">
	                            <img ng-if="!user.avatar" class="img-circle" ng-src="{{URL::to('avatars')}}/50x50_avatar_default.png" alt="" height="40" style="margin-left: 20px;"> @{{user.name}}
	                        </td>
	                        <td class="text-center" data-title="'Chinese id'">
	                            @{{(user.chinese_id != Null) ? user.chinese_id : 'NULL'}}
	                        </td>
	                        <td class="text-center" data-title="'Match'">
	                            @{{user.matches}}
	                        </td>
	                        <td class="text-center" data-title="'Gender'">
	                            @{{user.gender}}
	                        </td>
	                        <td class="text-center" data-title="'Birth day'">
	                            @{{(user.dob != Null) ? user.dob : 'NULL'}}
	                        </td>
	                        <td class="text-center" data-title="'Phone'">
	                            @{{(user.phone != Null) ? user.phone : 'NULL'}}
	                        </td>
	                        <td class="text-center" data-title="'Date registered'">
	                            @{{user.created_at}}
	                        </td>
	                    </tr>
	                </tbody>
	            </table> 
	            <div class="page-navigation">
                	{!! $users->render(); !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
		window.users = {!! json_encode($users) !!}
	</script>
	
	<script type="text/javascript" src="/app/components/back-end/users/UserService.js"></script>
	<script type="text/javascript" src="/app/components/back-end/users/UserController.js"></script>
@endsection