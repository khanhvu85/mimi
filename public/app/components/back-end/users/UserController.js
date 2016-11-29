userApp.controller('UserControler', ['$scope', '$uibModal', '$filter', 'ngTableParams', 'UserService', '$timeout', function ($scope, $uibModal, $filter, ngTableParams, UserService, $timeout) {
	/* When js didn't  loaded then hide table user */
	$('.container-fluid').removeClass('hidden');
	$('#page-loading').css('display', 'none');

	/* Not show search in table user */
	$scope.isSearch = false;

	/* Set data user to scope */
	$scope.data = UserService.setUsers(angular.copy(window.users.data));

	/* Use ng-table for table user */
	$scope.tableParams = new ngTableParams({
        page: 1,
        count: 20
    }, {
        total: angular.copy(window.users.total),
        getData: function ($defer, params) {
        	var orderedData = params.filter() ? $filter('filter')($scope.data, params.filter()) : $scope.data; /* Filter user */
        	orderedData = params.sorting() ? $filter('orderBy')(orderedData, params.orderBy()) : orderedData; /* Sort user */
            $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count())); /* Paging */
        }
    });

    $timeout(function() {
    	$('.ng-table-pager.ng-scope').css('display', 'none');
    });

	$scope.getModalUser = function(id){
		var template = '/admin/user/create?v='+ new Date().getTime();  /* Create user */
		if(typeof id != 'undefined'){
			template = '/admin/user/'+ id + '/edit?v=' + new Date().getTime(); /* Edit user */
		}
		var modalInstance = $uibModal.open({
		    animation: $scope.animationsEnabled,
		    templateUrl: window.baseUrl + template,
		    controller: 'ModalCreateUserCtrl',
		    size: null,
		    resolve: {
		    }
		    
		});

		/* After create or edit user then reset user and reload ng-table */
		modalInstance.result.then(function (data) {
			$scope.data = UserService.getUsers();
			$scope.tableParams.reload();
		}, function () {

		   });
	};

	/* Delete user */
	$scope.removeUser = function(id, size){
		var template = '/app/components/back-end/users/view/DeleteUser.html?v=' + new Date().getTime() /* Delete user */
		var modalInstance = $uibModal.open({
		    animation: $scope.animationsEnabled,
		    templateUrl: window.baseUrl + template,
		    controller: 'ModalDeleteUserCtrl',
		    size: size,
		    resolve: {
		    	userId: function(){
		            return id;
		        }
		    }
		    
		});

		/* After create or edit user then reset user and reload ng-table */
		modalInstance.result.then(function (data) {
			$scope.data = UserService.getUsers();
			$scope.tableParams.reload();
		}, function () {

		   });
	};

}])
.controller('ModalCreateUserCtrl', ['$scope', '$uibModalInstance', 'UserService', function ($scope, $uibModalInstance, UserService) {
	/* When user click add or edit user */
	$scope.submit = function () {
		$('#page-loading').css('display', 'block');
		UserService.createUserProvider($scope.userItem).then(function (data){
			if(data.status == 0){
				$('#page-loading').css('display', 'none');
				$scope.errors = data.error.email[0];
			} else{
				$('#page-loading').css('display', 'none');
				$uibModalInstance.close(data);
			}
		})
	};

	/* When user click cancel then close modal popup */
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
}])
.controller('ModalDeleteUserCtrl', ['$scope', '$uibModalInstance', 'userId', 'UserService', function ($scope, $uibModalInstance, userId, UserService) {
	/* When user click Delete user */
	$scope.submit = function () {
		$('#page-loading').css('display', 'block');
		UserService.deleteUser(userId).then(function (){
			$('#page-loading').css('display', 'none');
			$uibModalInstance.close();
		});
	};

	/* When user click cancel then close modal popup */
	$scope.cancel = function () {
	    $uibModalInstance.dismiss('cancel');
	};
}]);
