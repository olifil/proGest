var VenteApp = VenteApp || {};

(function($, Backbone, _) {

  // Ajout de la collection de produits
  productsList = new VenteApp.ProductsCollection;

  // Ajout de la vente
  vente = new VenteApp.VenteModel;

  // Ajout de la vue correspondant à la vente
  app = new VenteApp.VenteView({

  });

  $(document).ready(function() {

    $('#btnWait').on('click', function(event) {
      window.open(Routing.generate('vente-new'));
    });

    window.onbeforeunload = function(event) {
      var bdName = 'venteDB' + $('#now').attr('data');
      $.each(productsList.toJSON(), function(index, value){
          localStorage.removeItem(bdName+'-'+value.id);
      });
      localStorage.removeItem(bdName);
    }


  })

}(jQuery, Backbone, _));
