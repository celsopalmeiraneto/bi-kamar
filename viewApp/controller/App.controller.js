sap.ui.define(["sap/ui/core/mvc/Controller"],
  function(Controller){
    "use strict";
    return Controller.extend("br.com.celsoneto.biKamar.controller.App",{
      onInit : function(){

      },
      navigateTo : function(oEvent){
        console.log(oEvent);
      }
    });
  }
);
