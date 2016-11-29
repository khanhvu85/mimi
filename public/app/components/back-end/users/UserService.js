// var userApp = angular.module('AppUser', ['ui.bootstrap', 'ngResource', 'ngTable']);
var userApp = angular.module('AppUser', []);
userApp.factory('UserResource',['$resource', function ($resource){
    return $resource('/api/user/:method/:id', {'method':'@method','id':'@id'}, {
        add: {method: 'post'},
        save:{method: 'post'},
        update:{method: 'put'}
    });
}])
.service('UserService', ['UserResource', '$q', function (UserResource, $q) {
    var that = this;
    var users = [];

    /* Function create new user */
	this.createUserProvider = function(data){

        /* If isset id of user then call function edit user */
        if(typeof data['id'] != 'undefined') {
            return that.editUserProvider(data);
        }

		var defer = $q.defer(); 
        var temp  = new UserResource(data);

        temp.$save({}, function success(data) {

            /* If user is created */
            if(data.status != 0) {
                users.push(data.user);
            }

            /* Resolve result */
            defer.resolve(data);
        },
        
        /* If create user is error */
        function error(reponse) {
            /* Resolve result */
        	defer.resolve(reponse.data);
        });

        return defer.promise;  
	};

    /* Function edit user */
    this.editUserProvider = function(data){
        var defer = $q.defer(); 
        var temp  = new UserResource(data);
        /* Update user successfull */
        temp.$update({id: data['id']}, function success(data) {
            /* If user is edited */
            if(data.status != 0){
                /* Each user */
                for (var key in users) {
                    /* Set user edited */
                    if (users[key].id == data.user.id){
                        data.user.avatar = '/avatars/' + data.user.avatar;
                        users[key] = data.user;
                        break;
                    }
                }
            }
            defer.resolve(data);
        },
        /* If create user is error */
        function error(reponse) {
            /* If create user is error */
            defer.resolve(reponse.data);
        });
        
        return defer.promise;  
    };

    /* Function delete user */
    this.deleteUser = function (id) {
        var defer = $q.defer(); 
        var temp  = new UserResource();
        /* Delete user is successfull*/
        temp.$delete({id: id}, function success(data) {
            /* If user is deleted */
            if(data.status != 0){
                /* Each user */
                for (var key in users) {
                    /* Delete user is deleted in array users */
                    if (users[key].id == id){
                        users.splice(key, 1);
                        break;
                    }
                }
            }
            defer.resolve(data);
        },
        
        /* If delete user is error */
        function error(reponse) {
            defer.resolve(reponse.data);
        });
        return defer.promise;  
    }

    /* Set user to array users */
    this.setUsers = function(data) {
        users = data;
        return users;
    }
    
    /* Get array users */
    this.getUsers = function() {
        return users;
    }

}]);
