DirtyBranding.controller('HomeController',
    [
        '$scope',
        '$rootScope',
        '$location',
        '$routeParams',
        'SearchFactory',
        function ($scope, $rootScope, $location, $routeParams, SearchFactory){


            $scope.selectIPOffice = function(item, model){
                $scope.searchForm.ipoffices.unshift(item.name);
            };
            $scope.removeIPOffice = function(item, model){
                var index = $scope.searchForm.ipoffices.indexOf(item.name);
                $scope.searchForm.ipoffices.splice(index,1);
                $scope.searchChange();
            };

            $scope.searchChangeIdea = function(){
                if ($scope.searchForm.ideas_inline.length > 0){
                    $scope.searchForm.visibleOptionBtn = true;
                }else{
                    //$scope.searchForm.visibleOptionPanel = false;
                    //$scope.searchForm.visibleOptionBtn = false;
                }
                $scope.searchChange();
            };

            $scope.searchChange = function(){
                //va mettre à jour les tab de la factory avelc les inline du formulaire de recherche
                SearchFactory.changeForm($scope.searchForm);
            };

            $scope.searchSubmit = function(){

                //si des champs New sont remplis mais pas validé, on les inclus dans les listing
                if(typeof  $scope.new_prefixes != 'undefined' && $scope.new_prefixes.length >0)
                    $scope.searchAddElmt('prefixes');
                if(typeof  $scope.new_suffixes != 'undefined' && $scope.new_suffixes.length >0)
                    $scope.searchAddElmt('suffixes');
                if(typeof  $scope.new_extensions != 'undefined' && $scope.new_extensions.length >0)
                    $scope.searchAddElmt('extensions');

                SearchFactory.save($scope.searchForm);
                $location.path("/results/"+$scope.searchForm.ideas);


            };

            $scope.searchAddElmt = function(array_name){

                var value = $scope['new_'+array_name];

                 //traitements spécifiques
                if(array_name == 'extensions' && value[0] =="."){
                    value = value.substr(1);
                 }

                //dedoublonnage
                if($scope.searchForm[array_name].indexOf(value) == -1){
                    $scope.searchForm[array_name].unshift(value);
                    $scope.searchChange();
                }
                $scope['new_'+array_name] = '';

            };

            $scope.searchRemoveIndex = function(index, array_name){
                //Bug Angular UI : keypress "enter" fire ng-click sur extensions ou ipoffices
                //Workaround : désactiver la suppression si la sourie ne survol pas le bouton de suppression correspondant

                click_valide = false;
                 if ($('#remove_'+array_name+'_'+index).is(':hover'))
                    click_valide = true;

                if(click_valide){
                    $scope.searchForm[array_name].splice(index, 1);
                    $scope.searchChange();
                }
            };

            $scope.searchForm = SearchFactory.get($scope.searchForm);
            $scope.searchChangeIdea();

            $scope.ipoffice_model = {
                selected: []
            };

            angular.forEach($scope.searchForm.available_ipoffices, function(object_ipoffice, key){
                if($scope.searchForm.ipoffices.indexOf(object_ipoffice.name) != -1){
                    $scope.ipoffice_model.selected.push(object_ipoffice);
                }
            });

            //desactivation des tooltip pour mobile (cause un double click inutile car pas de survol sur mobile)
            if (!window.matchMedia || (window.matchMedia("(min-width: 768px)").matches)) {
                $(function () {
                  $('[data-toggle="tooltip"]').tooltip()
                });
            }

            document.getElementById('newIdeasInput').focus();

            $('.selectpicker').selectpicker();

}]);
