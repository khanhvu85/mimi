
var defaultModules = 
[
	'ui.bootstrap',
	'ngResource',
	'ngTable',
	'AppUser',
];

if(typeof modules != 'undefined'){
	defaultModules = defaultModules.concat(modules);
}
angular.module('mimi', defaultModules);



