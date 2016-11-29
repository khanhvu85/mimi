@extends('layouts.admin')

@section('title')
	MIMI ADMIN - SETTING
@endsection

@section('topic')
	<div class="col-md-12 view-topic">SETTING</div>
@endsection

@section('content')
	<div class="col-md-12 view-content" data-ng-controller="UserControler">
		<!-- Report -->
		<div class="view-block seeting-report-wrapper">
			<table class="table table-hover fix-height-tb table-striped" ng-table="tableParams">
                <tbody>
                    <tr ng-repeat="user in $data">
                    	<td data-title="'ID'" >
                            @{{user.id}}
                        </td>
                        <td data-title="'Name'">
                            <img class="img-circle" ng-src="@{{user.avatar}}" alt="" height="40">
                            @{{user.name}}
                        </td>
                        <td class="text-center" data-title="'Chinese id'">
                            @{{(user.chinese_id != Null) ? user.chinese_id : 'NULL'}}
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
                        {{-- <td class="text-center" data-title="''">
                            <a ng-click="getModalUser(user.id)" class="action-icon">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            <a ng-click="removeUser(user.id, 'sm')" class="action-icon">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </td> --}}
                    </tr>
                </tbody>
            </table> 
			<!-- Tittle -->
			{{-- <p class="view-tittle">
				* EXPORT REPORT
			</p> --}}
			<!-- Report -->
			<div class="seeting-report-content">
				<div class="row">
					@yield('content')
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')

	<script type="text/javascript">
		$(document).ready(function() {
			$(".link-setting").addClass("active");
		});	
		window.users = {!! json_encode($users) !!}
	</script>
	
	<script type="text/javascript" src="/app/components/back-end/users/UserService.js"></script>
	<script type="text/javascript" src="/app/components/back-end/users/UserController.js"></script>

@endsection